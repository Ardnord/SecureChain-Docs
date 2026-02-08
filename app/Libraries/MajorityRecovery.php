<?php

namespace App\Libraries;

use App\Models\BackupModel;
use App\Models\ActivityLogModel;
use App\Models\RecoveryHistoryModel;
use Config\Recovery as RecoveryConfig;


class MajorityRecovery
{
    protected $config;
    protected $userDb;
    protected $adminDb;
    protected $konsensusDb;
    protected $backupModel;
    protected $activityLogModel;
    protected $recoveryHistoryModel;

    public function __construct()
    {
        $this->config = new RecoveryConfig();

        // Connect ke ketiga database
        $this->userDb = \Config\Database::connect('userdb');
        $this->adminDb = \Config\Database::connect('admindb');
        $this->konsensusDb = \Config\Database::connect('konsensus');

        // Load models
        $this->backupModel = model(BackupModel::class);
        $this->activityLogModel = model(ActivityLogModel::class);
        $this->recoveryHistoryModel = model(RecoveryHistoryModel::class);
    }

    /**
     * @return array 
     */
    public function check(): array
    {
        $startTime = microtime(true);

        // Ambil semua records dari userdb (sebagai baseline)
        $userRecords = $this->userDb->table('blockchain')->get()->getResultArray();

        $results = [
            'total_checked'     => count($userRecords),
            'healthy'           => 0,
            'minority_corrupt'  => 0,
            'no_consensus'      => 0,
            'missing_in_db'     => 0,
            'details'           => [],
            'execution_time'    => 0
        ];

        foreach ($userRecords as $userRecord) {
            $checkResult = $this->checkSingleRecord($userRecord);

            // Update statistik
            switch ($checkResult['status']) {
                case 'healthy':
                    $results['healthy']++;
                    break;
                case 'minority':
                    $results['minority_corrupt']++;
                    $results['details'][] = $checkResult;
                    break;
                case 'no_consensus':
                    $results['no_consensus']++;
                    $results['details'][] = $checkResult;
                    break;
                case 'missing':
                    $results['missing_in_db']++;
                    $results['details'][] = $checkResult;
                    break;
            }
        }

        $results['execution_time'] = round(microtime(true) - $startTime, 2);

        // Log pengecekan
        $this->activityLogModel->logActivity([
            'action_type'   => 'CONSENSUS_CHECK',
            'status'        => 'INFO',
            'description'   => sprintf(
                'Consensus check completed: %d healthy, %d minority, %d no-consensus, %d missing',
                $results['healthy'],
                $results['minority_corrupt'],
                $results['no_consensus'],
                $results['missing_in_db']
            ),
            'original_data' => $results
        ]);

        return $results;
    }

    /**
     * Check konsensus untuk single record
     */
    protected function checkSingleRecord(array $userRecord): array
    {
        $blockHash = $userRecord['block_hash'];
        $nomorPermohonan = $userRecord['nomor_permohonan'];
        $tanggalDokumen = $userRecord['tanggal_dokumen'];

        if ($this->config->verboseLogging) {
            log_message('debug', "[CONSENSUS_CHECK] Checking record: {$nomorPermohonan} (hash: " . substr($blockHash, 0, 16) . "...)");
        }

        // Ambil data dari ketiga database
        $data = [
            'userdb'    => $userRecord,
            'admindb'   => $this->getFromAdminDb($blockHash, $nomorPermohonan, $tanggalDokumen),
            'konsensus' => $this->getFromKonsensusDb($blockHash, $nomorPermohonan, $tanggalDokumen)
        ];

        // Log data availability
        if ($this->config->verboseLogging) {
            log_message('debug', "[CONSENSUS_CHECK] Data availability - UserDB: YES, AdminDB: " .
                ($data['admindb'] ? 'YES' : 'NO') . ", KonsensusDB: " .
                ($data['konsensus'] ? 'YES' : 'NO'));
        }

        // Hitung checksum untuk masing-masing
        $checksums = [
            'userdb'    => $data['userdb'] ? $this->calculateChecksum($data['userdb']) : null,
            'admindb'   => $data['admindb'] ? $this->calculateChecksum($data['admindb']) : null,
            'konsensus' => $data['konsensus'] ? $this->calculateChecksum($data['konsensus']) : null
        ];

        if ($this->config->verboseLogging) {
            log_message('debug', "[CONSENSUS_CHECK] Checksums - UserDB: " . substr($checksums['userdb'] ?? 'NULL', 0, 16) .
                ", AdminDB: " . substr($checksums['admindb'] ?? 'NULL', 0, 16) .
                ", KonsensusDB: " . substr($checksums['konsensus'] ?? 'NULL', 0, 16));
        }

        // Voting mechanism
        $voteResult = $this->performVoting($checksums, $data);

        return [
            'record_key'    => $blockHash,
            'identifier'    => $nomorPermohonan,
            'status'        => $voteResult['status'],
            'checksums'     => $checksums,
            'majority_hash' => $voteResult['majority_hash'] ?? null,
            'corrupt_dbs'   => $voteResult['corrupt_dbs'] ?? [],
            'data'          => $data,
            'recommendation' => $voteResult['recommendation'] ?? null
        ];
    }

    /**
     * Voting mechanism: tentukan majority consensus
     */
    protected function performVoting(array $checksums, array $data): array
    {
        // Filter null checksums
        $validChecksums = array_filter($checksums, fn($c) => $c !== null);

        // Jika ada DB yang missing data
        if (count($validChecksums) < 3) {
            $missingDbs = array_keys(array_filter($checksums, fn($c) => $c === null));
            return [
                'status'         => 'missing',
                'corrupt_dbs'    => $missingDbs,
                'recommendation' => 'Sync missing data from available databases'
            ];
        }

        // Hitung votes
        $votes = array_count_values($validChecksums);
        arsort($votes);

        $majorityHash = array_key_first($votes);
        $majorityCount = $votes[$majorityHash];

        // Case 1: Semua sama (3-0) - HEALTHY
        if ($majorityCount === 3) {
            return [
                'status'        => 'healthy',
                'majority_hash' => $majorityHash
            ];
        }

        // Case 2: Majority 2-1 - MINORITY CORRUPT
        if ($majorityCount === 2) {
            $corruptDbs = [];
            foreach ($checksums as $db => $hash) {
                if ($hash !== $majorityHash) {
                    $corruptDbs[] = $db;
                }
            }

            return [
                'status'         => 'minority',
                'majority_hash'  => $majorityHash,
                'corrupt_dbs'    => $corruptDbs,
                'recommendation' => 'Recover from majority'
            ];
        }

        // Case 3: Semua berbeda (1-1-1) - NO CONSENSUS
        return [
            'status'         => 'no_consensus',
            'corrupt_dbs'    => array_keys($checksums),
            'recommendation' => 'Manual review required - use blockchain_backup as source of truth'
        ];
    }

    /**
     * recover records yang corrupt (minority)
     * 
     * MEKANISME:
     * 1. Filter hanya minority corrupt (2 vs 1)
     * 2. Langsung repair corrupt DB dengan data majority
     * 3. Data corrupt disimpan di recovery_history.before_data (untuk rollback)
     * 4. Data corrupt TIDAK di-backup ke blockchain_backup (karena corrupt)
     * 
     * @param array $items Items dari hasil check() yang perlu di-recover
     * @param string $performedBy Username yang trigger recovery
     * @return array Hasil recovery
     */
    public function recover(array $items, string $performedBy = 'system'): array
    {
        $results = [
            'total_attempted' => count($items),
            'success'         => 0,
            'failed'          => 0,
            'skipped'         => 0,
            'details'         => []
        ];

        foreach ($items as $item) {
            // Handle missing data (sync dari DB yang ada)
            if ($item['status'] === 'missing') {
                $syncResult = $this->syncMissingData($item, $performedBy);
                if ($syncResult['success']) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }
                $results['details'][] = $syncResult;
                continue;
            }

            // Skip jika bukan minority (hanya recover yang 2-1)
            if ($item['status'] !== 'minority') {
                $results['skipped']++;
                continue;
            }

            // Skip jika table di-blacklist
            if (in_array('blockchain', $this->config->blacklistTables)) {
                $results['skipped']++;
                $results['details'][] = [
                    'record_key' => $item['record_key'],
                    'status'     => 'skipped',
                    'reason'     => 'Table is blacklisted'
                ];
                continue;
            }

            // Lakukan recovery (langsung repair tanpa backup ke blockchain_backup)
            $recoveryResult = $this->recoverSingleRecord($item, $performedBy);

            if ($recoveryResult['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
            }

            $results['details'][] = $recoveryResult;
        }

        // Log hasil recovery
        $this->activityLogModel->logActivity([
            'action_type'   => 'CONSENSUS_RECOVER',
            'status'        => $results['failed'] > 0 ? 'WARNING' : 'SUCCESS',
            'description'   => sprintf(
                'Auto-recovery completed: %d success, %d failed, %d skipped',
                $results['success'],
                $results['failed'],
                $results['skipped']
            ),
            'original_data' => $results
        ]);

        return $results;
    }

    /**
     * Recover single record
     */
    protected function recoverSingleRecord(array $item, string $performedBy): array
    {
        $recordKey = $item['record_key'];
        $majorityHash = $item['majority_hash'];
        $corruptDbs = $item['corrupt_dbs'];

        // Tentukan source data (dari DB yang memiliki majority hash)
        $sourceDb = null;
        $sourceData = null;

        foreach (['konsensus', 'admindb', 'userdb'] as $db) {
            if ($item['checksums'][$db] === $majorityHash) {
                $sourceDb = $db;
                $sourceData = $item['data'][$db];
                break;
            }
        }

        if (!$sourceData) {
            return [
                'record_key' => $recordKey,
                'success'    => false,
                'error'      => 'Source data not found'
            ];
        }

        $recoveryDetails = [
            'record_key' => $recordKey,
            'source_db'  => $sourceDb,
            'target_dbs' => $corruptDbs,
            'success'    => true,
            'errors'     => []
        ];

        // Recover setiap corrupt DB
        foreach ($corruptDbs as $targetDb) {
            try {
                // PENTING: Data corrupt TIDAK di-backup ke blockchain_backup
                // Hanya disimpan di recovery_history.before_data untuk rollback capability

                $corruptData = $item['data'][$targetDb];

                if ($this->config->verboseLogging) {
                    log_message('info', "[CONSENSUS_RECOVERY] Repairing {$recordKey} in {$targetDb} (corrupt) from {$sourceDb} (majority)");
                }

                // Update corrupt DB dengan data majority (langsung repair)
                $updateSuccess = $this->updateDatabase($targetDb, $sourceData, $corruptData);

                if ($updateSuccess) {
                    // Log ke recovery history (before_data untuk rollback)
                    $this->recoveryHistoryModel->logRecovery([
                        'recovery_type'    => $performedBy === 'system' ? 'consensus_auto' : 'consensus_manual',
                        'source_db'        => $sourceDb,
                        'target_db'        => $targetDb,
                        'table_name'       => 'blockchain',
                        'record_key'       => $recordKey,
                        'before_checksum'  => $item['checksums'][$targetDb],
                        'after_checksum'   => $majorityHash,
                        'before_data'      => $corruptData,  // Corrupt data (untuk rollback)
                        'after_data'       => $sourceData,   // Data majority (hasil repair)
                        'consensus_result' => $item['checksums'],
                        'status'           => 'success',
                        'performed_by'     => $performedBy
                    ]);

                    // Log activity for this recovery so Riwayat aktivitas shows the identifier
                    $this->activityLogModel->logActivity([
                        'action_type'   => 'CONSENSUS_RECOVER',
                        'status'        => 'SUCCESS',
                        'identifier'    => $item['identifier'] ?? $recordKey,
                        'description'   => "Repaired {$item['identifier']} in {$targetDb} from {$sourceDb}",
                        'original_data' => $corruptData,
                        'modified_data' => $sourceData
                    ]);

                    if ($this->config->verboseLogging) {
                        log_message('info', "[CONSENSUS_RECOVERY] ✓ Successfully repaired {$recordKey} in {$targetDb} from {$sourceDb}");
                    }
                } else {
                    $recoveryDetails['success'] = false;
                    $recoveryDetails['errors'][] = "Failed to update {$targetDb}";

                    log_message('error', "[CONSENSUS_RECOVERY] ✗ Failed to repair {$recordKey} in {$targetDb}");
                }
            } catch (\Exception $e) {
                $recoveryDetails['success'] = false;
                $recoveryDetails['errors'][] = "Error updating {$targetDb}: " . $e->getMessage();

                log_message('error', "[CONSENSUS_RECOVERY] Exception: " . $e->getMessage());
            }
        }

        return $recoveryDetails;
    }

    /**
     * Update database dengan data majority
     * 
     * PENTING:
     * - Filter field yang boleh di-update (exclude id, timestamp, backup_timestamp)
     * - Gunakan block_hash sebagai key untuk matching
     * - Support update ke userdb.blockchain, admindb.blockchain_backup, konsensus.konsensus
     */
    protected function updateDatabase(string $targetDb, array $sourceData, array $currentData): bool
    {
        if ($this->config->dryRunMode) {
            log_message('info', "[DRY-RUN] Would update {$targetDb} with data from majority");
            return true;
        }

        $db = null;
        $table = '';

        switch ($targetDb) {
            case 'userdb':
                $db = $this->userDb;
                $table = 'blockchain';
                break;
            case 'admindb':
                $db = $this->adminDb;
                $table = 'blockchain_backup';
                break;
            case 'konsensus':
                $db = $this->konsensusDb;
                $table = 'konsensus';
                break;
        }

        if (!$db) {
            log_message('error', "[CONSENSUS_UPDATE] Invalid target DB: {$targetDb}");
            return false;
        }

        // Field yang boleh di-update (exclude id, timestamp, backup_timestamp)
        $updateableFields = [
            'nomor_permohonan',
            'nomor_dokumen',
            'tanggal_dokumen',
            'tanggal_filing',
            'dokumen_base64',
            'ip_address',
            'block_hash',
            'previous_hash'
        ];

        // Filter hanya field yang boleh di-update
        $updateData = [];
        foreach ($updateableFields as $field) {
            if (isset($sourceData[$field])) {
                $updateData[$field] = $sourceData[$field];
            }
        }

        if (empty($updateData)) {
            log_message('error', "[CONSENSUS_UPDATE] No updateable fields found");
            return false;
        }

        // Cari record berdasarkan block_hash (lebih reliable) atau id
        $blockHash = $currentData['block_hash'] ?? null;
        $recordId = $currentData['id'] ?? null;

        try {
            $builder = $db->table($table);

            // Prioritas: cari by block_hash (unique identifier)
            if ($blockHash) {
                $builder->where('block_hash', $blockHash);
            } elseif ($recordId) {
                $builder->where('id', $recordId);
            } else {
                log_message('error', "[CONSENSUS_UPDATE] No identifier (block_hash or id) found");
                return false;
            }

            $result = $builder->update($updateData);

            if ($result) {
                log_message('info', "[CONSENSUS_UPDATE] ✓ Updated {$table} in {$targetDb} (hash: " . substr($blockHash, 0, 16) . "...)");
            } else {
                log_message('warning', "[CONSENSUS_UPDATE] ✗ Update returned false for {$table} in {$targetDb}");
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', "[CONSENSUS_UPDATE] Exception updating {$table} in {$targetDb}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync missing data ke database yang tidak punya record
     * 
     * Jika salah satu DB tidak punya data, ambil dari DB yang ada (valid)
     */
    protected function syncMissingData(array $item, string $performedBy): array
    {
        $recordKey = $item['record_key'];
        $missingDbs = $item['corrupt_dbs']; // Untuk status 'missing', ini adalah DB yang tidak punya data

        // Cari DB yang punya data valid
        $sourceDb = null;
        $sourceData = null;

        foreach (['userdb', 'admindb', 'konsensus'] as $db) {
            if ($item['data'][$db] !== null) {
                $sourceDb = $db;
                $sourceData = $item['data'][$db];
                break;
            }
        }

        if (!$sourceData) {
            return [
                'record_key' => $recordKey,
                'success'    => false,
                'error'      => 'No source data found for sync'
            ];
        }

        $syncDetails = [
            'record_key' => $recordKey,
            'source_db'  => $sourceDb,
            'target_dbs' => $missingDbs,
            'success'    => true,
            'errors'     => []
        ];

        // Sync ke setiap missing DB
        foreach ($missingDbs as $targetDb) {
            try {
                $insertSuccess = $this->insertToDatabase($targetDb, $sourceData);

                if ($insertSuccess) {
                    // Log ke recovery history
                    $this->recoveryHistoryModel->logRecovery([
                        'recovery_type'    => 'consensus_auto',
                        'source_db'        => $sourceDb,
                        'target_db'        => $targetDb,
                        'table_name'       => 'blockchain',
                        'record_key'       => $recordKey,
                        'before_checksum'  => null,
                        'after_checksum'   => $this->calculateChecksum($sourceData),
                        'before_data'      => null,
                        'after_data'       => $sourceData,
                        'consensus_result' => $item['checksums'],
                        'status'           => 'success',
                        'performed_by'     => $performedBy
                    ]);
                    // Log activity so Riwayat aktivitas includes identifier for sync operations
                    $this->activityLogModel->logActivity([
                        'action_type'   => 'CONSENSUS_SYNC',
                        'status'        => 'SUCCESS',
                        'identifier'    => $item['identifier'] ?? $recordKey,
                        'description'   => "Synced missing data for {$item['identifier']} to {$targetDb} from {$sourceDb}",
                        'modified_data' => $sourceData
                    ]);

                    if ($this->config->verboseLogging) {
                        log_message('info', "[CONSENSUS_SYNC] ✓ Synced missing data to {$targetDb} from {$sourceDb}");
                    }
                } else {
                    $syncDetails['success'] = false;
                    $syncDetails['errors'][] = "Failed to insert to {$targetDb}";
                }
            } catch (\Exception $e) {
                $syncDetails['success'] = false;
                $syncDetails['errors'][] = "Error inserting to {$targetDb}: " . $e->getMessage();
                log_message('error', "[CONSENSUS_SYNC] Exception: " . $e->getMessage());
            }
        }

        return $syncDetails;
    }

    /**
     * Insert data ke database yang missing
     */
    protected function insertToDatabase(string $targetDb, array $data): bool
    {
        if ($this->config->dryRunMode) {
            log_message('info', "[DRY-RUN] Would insert to {$targetDb}");
            return true;
        }

        $db = null;
        $table = '';

        switch ($targetDb) {
            case 'userdb':
                $db = $this->userDb;
                $table = 'blockchain';
                break;
            case 'admindb':
                $db = $this->adminDb;
                $table = 'blockchain_backup';
                break;
            case 'konsensus':
                $db = $this->konsensusDb;
                $table = 'konsensus';
                break;
        }

        if (!$db) {
            return false;
        }

        // Field yang boleh di-insert (exclude id jika auto_increment)
        $insertableFields = [
            'nomor_permohonan',
            'nomor_dokumen',
            'tanggal_dokumen',
            'tanggal_filing',
            'dokumen_base64',
            'ip_address',
            'block_hash',
            'previous_hash',
            'timestamp'
        ];

        // Filter data
        $insertData = [];
        foreach ($insertableFields as $field) {
            if (isset($data[$field])) {
                $insertData[$field] = $data[$field];
            }
        }

        // Untuk admindb, tambahkan backup_type
        if ($targetDb === 'admindb') {
            $insertData['backup_type'] = 'consensus_sync';
        }

        try {
            $result = $db->table($table)->insert($insertData);

            if ($result) {
                log_message('info', "[CONSENSUS_INSERT] ✓ Inserted to {$table} in {$targetDb}");
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', "[CONSENSUS_INSERT] Exception inserting to {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Backup data corrupt sebelum overwrite
     * 
     * CATATAN PENTING:
     * - Data corrupt TIDAK di-backup ke blockchain_backup (karena corrupt)
     * - Data corrupt hanya disimpan di recovery_history.before_data (untuk rollback)
     * - Backup ke blockchain_backup hanya untuk data VALID
     */
    protected function backupCorruptData(array $data, string $sourceDb): void
    {
        // TIDAK backup corrupt data ke blockchain_backup
        // Corrupt data hanya tersimpan di recovery_history.before_data

        if ($this->config->verboseLogging) {
            log_message('info', "[CONSENSUS] Corrupt data from {$sourceDb} will be stored in recovery_history only (not in blockchain_backup)");
        }
    }

    /**
     * Calculate checksum untuk record
     */
    protected function calculateChecksum(array $record): string
    {
        $dataToHash = '';

        foreach ($this->config->checksumFields as $field) {
            $dataToHash .= $record[$field] ?? '';
        }

        return hash('sha256', $dataToHash);
    }

    /**
     * Get data dari admindb (blockchain_backup)
     */
    protected function getFromAdminDb(string $blockHash, string $nomorPermohonan, string $tanggalDokumen): ?array
    {
        // Prioritas: cari by block_hash
        $result = $this->adminDb->table('blockchain_backup')
            ->where('block_hash', $blockHash)
            ->get()
            ->getRowArray();

        // Fallback: cari by identifier
        if (!$result) {
            $result = $this->adminDb->table('blockchain_backup')
                ->where('nomor_permohonan', $nomorPermohonan)
                ->where('DATE(tanggal_dokumen)', date('Y-m-d', strtotime($tanggalDokumen)))
                ->orderBy('backup_timestamp', 'DESC')
                ->get()
                ->getRowArray();
        }

        return $result;
    }

    /**
     * Get data dari konsensus db
     */
    protected function getFromKonsensusDb(string $blockHash, string $nomorPermohonan, string $tanggalDokumen): ?array
    {
        // Prioritas: cari by block_hash
        $result = $this->konsensusDb->table('konsensus')
            ->where('block_hash', $blockHash)
            ->get()
            ->getRowArray();

        // Fallback: cari by identifier
        if (!$result) {
            $result = $this->konsensusDb->table('konsensus')
                ->where('nomor_permohonan', $nomorPermohonan)
                ->where('DATE(tanggal_dokumen)', date('Y-m-d', strtotime($tanggalDokumen)))
                ->orderBy('timestamp', 'DESC')
                ->get()
                ->getRowArray();
        }

        return $result;
    }

    /**
     * Rollback recovery berdasarkan history ID
     */
    public function rollback(int $historyId, string $performedBy = 'admin'): array
    {
        $history = $this->recoveryHistoryModel->getRecoveryById($historyId);

        if (!$history) {
            return [
                'success' => false,
                'error'   => 'Recovery history not found'
            ];
        }

        if ($history['status'] === 'rolled_back') {
            return [
                'success' => false,
                'error'   => 'This recovery has already been rolled back'
            ];
        }

        try {
            // Restore data dari before_data
            $beforeData = $history['before_data'];
            $targetDb = $history['target_db'];

            $rollbackSuccess = $this->updateDatabase($targetDb, $beforeData, $history['after_data']);

            if ($rollbackSuccess) {
                // Mark as rolled back
                $this->recoveryHistoryModel->markAsRolledBack($historyId);

                // Log rollback
                $this->recoveryHistoryModel->logRecovery([
                    'recovery_type'    => 'rollback',
                    'source_db'        => $targetDb,
                    'target_db'        => $targetDb,
                    'table_name'       => $history['table_name'],
                    'record_key'       => $history['record_key'],
                    'before_checksum'  => $history['after_checksum'],
                    'after_checksum'   => $history['before_checksum'],
                    'before_data'      => $history['after_data'],
                    'after_data'       => $beforeData,
                    'status'           => 'success',
                    'performed_by'     => $performedBy
                ]);

                $this->activityLogModel->logActivity([
                    'action_type'   => 'CONSENSUS_ROLLBACK',
                    'status'        => 'SUCCESS',
                    'description'   => "Rolled back recovery #{$historyId} for {$history['record_key']}",
                    'original_data' => $history
                ]);

                return [
                    'success' => true,
                    'message' => 'Recovery successfully rolled back'
                ];
            }

            return [
                'success' => false,
                'error'   => 'Failed to update database during rollback'
            ];
        } catch (\Exception $e) {
            log_message('error', "[CONSENSUS_ROLLBACK] Error: " . $e->getMessage());

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }
}
