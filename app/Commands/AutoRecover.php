<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MajorityRecovery;
use App\Models\BlockModel;
use App\Models\ActivityLogModel;

class AutoRecover extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'auto:recover';
    protected $description = 'Run consensus-based auto recovery for manipulated blocks';

    public function run(array $params = [])
    {
        CLI::write('Starting consensus auto-recovery...', 'yellow');

        $majorityRecovery = new MajorityRecovery();
        $checkResult = $majorityRecovery->check();

        $blockModel = new BlockModel();
        $activityLog = new ActivityLogModel();

        $recoveredCount = 0;
        $failedCount = 0;

        if (!empty($checkResult['details'])) {
            foreach ($checkResult['details'] as $detail) {
                $status = $detail['status'] ?? 'unknown';
                $corruptDbs = $detail['corrupt_dbs'] ?? [];
                $blockId = $detail['block_id'] ?? null;

                if ($status === 'minority' && !empty($corruptDbs) && $blockId) {
                    $blockData = null;
                    $healthyDb = null;

                    if (in_array('userdb', $corruptDbs)) {
                        $blockData = $detail['data']['admindb'] ?? $detail['data']['konsensus'] ?? null;
                        $healthyDb = !empty($detail['data']['admindb']) ? 'admindb' : 'konsensus';
                    } elseif (in_array('admindb', $corruptDbs)) {
                        $blockData = $detail['data']['userdb'] ?? $detail['data']['konsensus'] ?? null;
                        $healthyDb = !empty($detail['data']['userdb']) ? 'userdb' : 'konsensus';
                    } elseif (in_array('konsensus', $corruptDbs)) {
                        $blockData = $detail['data']['userdb'] ?? $detail['data']['admindb'] ?? null;
                        $healthyDb = !empty($detail['data']['userdb']) ? 'userdb' : 'admindb';
                    }

                    if ($blockData && $healthyDb) {
                        $recoveryData = [
                            'nomor_permohonan' => $blockData['nomor_permohonan'] ?? null,
                            'nomor_dokumen' => $blockData['nomor_dokumen'] ?? null,
                            'tanggal_dokumen' => $blockData['tanggal_dokumen'] ?? null,
                            'tanggal_filing' => $blockData['tanggal_filing'] ?? null,
                            'dokumen_base64' => $blockData['dokumen_base64'] ?? null,
                            'ip_address' => $blockData['ip_address'] ?? null,
                            'block_hash' => $blockData['block_hash'] ?? null,
                            'previous_hash' => $blockData['previous_hash'] ?? null,
                        ];

                        try {
                            if ($blockModel->update($blockId, $recoveryData)) {
                                $recoveredCount++;
                                $activityLog->logActivity([
                                    'action_type' => 'RECOVER',
                                    'block_id' => $blockId,
                                    'identifier' => $blockData['nomor_permohonan'] ?? null,
                                    'status' => 'Recovered',
                                    'description' => "Data dipulihkan dari {$healthyDb} database (consensus)",
                                    'original_data' => ['corrupt_dbs' => $corruptDbs],
                                    'modified_data' => ['block_hash' => $blockData['block_hash'] ?? null]
                                ]);
                            } else {
                                $failedCount++;
                            }
                        } catch (\Exception $e) {
                            $failedCount++;
                            CLI::write('Error recovering block ' . $blockId . ': ' . $e->getMessage(), 'red');
                        }
                    }
                }

                if ($status === 'no_consensus') {
                    // No automatic recovery for full disagreement — log for manual review
                    $activityLog->logActivity([
                        'action_type' => 'MANUAL_REVIEW',
                        'block_id' => $detail['block_id'] ?? null,
                        'identifier' => $detail['nomor_permohonan'] ?? null,
                        'status' => 'NoConsensus',
                        'description' => 'No consensus across databases — manual review required',
                    ]);
                    $failedCount++;
                }
            }
        }

        CLI::write("Auto-recovery finished. Recovered: {$recoveredCount}, Failed/Manual: {$failedCount}", 'green');
    }
}
