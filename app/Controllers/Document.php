<?php

namespace App\Controllers;

use App\Models\BlockModel;
use App\Models\BackupModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Files\File;

class Document extends BaseController
{
    protected $blockModel;
    protected $backupModel;
    protected $activityLogModel;
    protected $cache;
    protected $session;
    protected $db;
    protected $adminDb;
    protected $konsensusDb;

    public function __construct()
    {
        // Inisialisasi model, library, dan koneksi database yang dibutuhkan.
        $this->blockModel = model(BlockModel::class);
        $this->backupModel = model(BackupModel::class);
        $this->activityLogModel = model(ActivityLogModel::class);
        $this->cache = \Config\Services::cache();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect('userdb');
        $this->adminDb = \Config\Database::connect('admindb');
        $this->konsensusDb = \Config\Database::connect('konsensus');
    }

    public function index()
    {
        // Menampilkan halaman utama pengguna dengan daftar dokumen.
        $this->detectAndRecover();

        $keyword = $this->request->getGet('keyword');
        $db = $this->blockModel;

        $fieldsToSelect = 'id, nomor_permohonan, nomor_dokumen, tanggal_dokumen, tanggal_filing, block_hash, previous_hash, timestamp';

        if ($keyword) {
            $documents = $db->search($keyword);
        } else {
            // Terapkan select() juga di sini
            $documents = $db->select($fieldsToSelect)
                ->orderBy('id', 'DESC')
                ->paginate(10);
        }

        $data = [
            'documents'  => $documents,
            'pager'      => $db->pager,
            'validation' => \Config\Services::validation()
        ];
        // Logika caching tidak direkomendasikan lagi saat menggunakan search
        return view('user/dashboard/index', $data);
    }

    public function create()
    {
        // Memproses pembuatan blok baru dari data dokumen yang diunggah.
        $rules = [
            'nomor_permohonan' => 'required|string|max_length[100]',
            'nomor_dokumen'    => 'required|string|max_length[100]',
            'tanggal_dokumen'  => 'required|valid_date',
            'tanggal_filing'   => 'required|valid_date',
            'dokumen'          => 'uploaded[dokumen]|max_size[dokumen,5120]|ext_in[dokumen,pdf,docx,jpg,png]',
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan pengguna ke form.
            return redirect()->to('/')->withInput();
        }

        // Jika validasi berhasil, lanjutkan proses.
        try {
            $postData = [
                'nomor_permohonan' => htmlspecialchars($this->request->getPost('nomor_permohonan'), ENT_QUOTES, 'UTF-8'),
                'nomor_dokumen'    => htmlspecialchars($this->request->getPost('nomor_dokumen'), ENT_QUOTES, 'UTF-8'),
                'tanggal_dokumen'  => $this->request->getPost('tanggal_dokumen'),
                'tanggal_filing'   => $this->request->getPost('tanggal_filing')
            ];
            $file = $this->request->getFile('dokumen');

            $this->createNewBlock($postData, $file);

            return redirect()->to('/')->with('success', 'Dokumen berhasil diamankan dalam blockchain!');
        } catch (\Exception $e) {
            log_message('error', '[BLOCKCHAIN_ERROR] Gagal membuat blok: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'Gagal menyimpan dokumen karena terjadi kesalahan pada sistem.');
        }
    }

    public function download(string $block_hash)
    {
        // Mengunduh file dokumen asli berdasarkan hash blok.
        $block = $this->blockModel->getBlockByHash($block_hash);

        if (!$block) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Dokumen tidak ditemukan untuk hash tersebut.');
        }

        $fileData = base64_decode($block['dokumen_base64']);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $fileData);
        finfo_close($finfo);

        $extension = $this->getExtensionFromMime($mimeType);
        $fileName = $block['nomor_permohonan'] . '_' . $block['nomor_dokumen'] . '.' . $extension;

        return $this->response
            ->setBody($fileData)
            ->setContentType($mimeType)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->send();
    }

    private function createNewBlock(array $postData, \CodeIgniter\HTTP\Files\UploadedFile $file): void
    {
        // Membuat blok baru, menghitung hash, dan menyimpannya ke semua database.
        $dokumenBase64 = base64_encode(file_get_contents($file->getTempName()));

        // Get latest block from cache or database
        $cacheKey = 'latest_block';
        $latestBlock = $this->cache->get($cacheKey);

        if (!$latestBlock) {
            $latestBlock = $this->blockModel->getLatestBlock();
            // Cache the latest block for 1 minute
            $this->cache->save($cacheKey, $latestBlock, 60);
        }

        $previousHash = $latestBlock ? $latestBlock['block_hash'] : '0';

        $timestamp = date('Y-m-d H:i:s');

        // Hash menggunakan: nomor_permohonan, nomor_dokumen, tanggal_dokumen, tanggal_filing, dokumen_base64
        $dataToHash = $postData['nomor_permohonan'] . $postData['nomor_dokumen'] .
            $postData['tanggal_dokumen'] . $postData['tanggal_filing'] .
            $dokumenBase64;
        $newBlockHash = hash('sha256', $dataToHash);

        $saveData = array_merge($postData, [
            'dokumen_base64'   => $dokumenBase64,
            'ip_address'       => $this->request->getIPAddress(),
            'block_hash'       => $newBlockHash,
            'previous_hash'    => $previousHash,
        ]);

        $this->blockModel->save($saveData);

        // Simpan data ke database konsensus
        $this->konsensusDb->table('konsensus')->insert($saveData);

        // Trigger event untuk auto-backup
        \CodeIgniter\Events\Events::trigger('afterInsertBlock', $saveData);

        // Clear the cache after adding new block
        $this->cache->delete($cacheKey);
        $this->cache->clean();
    }

    private function getExtensionFromMime(string $mimeType): string
    {
        // Mendapatkan ekstensi file dari tipe MIME.
        $mimes = [
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        return $mimes[$mimeType] ?? 'bin';
    }

    private function detectAndRecover(): void
    {
        // Mendeteksi manipulasi data pada setiap request dan mencoba pemulihan otomatis.
        try {
            if (!$this->adminDb->tableExists('blockchain_backup')) {
                log_message('warning', '[SYNC] Tabel admindb tidak ada, skip backup check');
            }
            if (!$this->konsensusDb->tableExists('konsensus')) {
                log_message('warning', '[SYNC] Tabel konsensus tidak ada, skip konsensus check');
            }
        } catch (\Throwable $e) {
            log_message('error', '[SYNC] Gagal cek ketersediaan tabel: ' . $e->getMessage());
            return;
        }

        $startTime = microtime(true);
        $allBlocks = $this->blockModel->getAllBlocks();
        $totalBlocks = count($allBlocks);

        $manipulatedCount = 0;
        $recoveredCount = 0;
        $manipulatedBlocks = [];
        $recoveredBlocks = [];

        foreach ($allBlocks as $block) {
            $dataToHash = $block['nomor_permohonan'] . $block['nomor_dokumen'] .
                $block['tanggal_dokumen'] . $block['tanggal_filing'] .
                $block['dokumen_base64'];
            $recalculatedHash = hash('sha256', $dataToHash);

            // Jika hash tidak cocok → manipulasi terdeteksi
            if ($recalculatedHash !== $block['block_hash']) {
                $manipulatedCount++;
                $manipulatedBlocks[] = [
                    'id' => $block['id'],
                    'nomor_permohonan' => $block['nomor_permohonan'],
                    'stored_hash' => $block['block_hash'],
                    'calc_hash' => $recalculatedHash,
                ];

                log_message('error', "[MANIPULASI] Blok ID {$block['id']} - Stored: {$block['block_hash']}, Calculated: {$recalculatedHash}");

                // === PRIORITAS RECOVERY: Konsensus → Admin → Identifier ===
                $recoverySource = null;
                $recoveryData = null;

                // 1. Coba dari KONSENSUS DB berdasarkan block_hash asli
                $consensusRecord = $this->konsensusDb->table('konsensus')
                    ->where('block_hash', $block['block_hash'])
                    ->get()
                    ->getRowArray();

                if ($consensusRecord) {
                    $recoverySource = 'konsensus';
                    $recoveryData = $consensusRecord;
                    log_message('info', "[RECOVERY] Ditemukan di konsensus untuk hash: {$block['block_hash']}");
                } else {
                    // 2. Coba dari ADMIN DB
                    $adminBackup = $this->backupModel->getBackupByHash($block['block_hash']);
                    if ($adminBackup) {
                        $recoverySource = 'admin';
                        $recoveryData = $adminBackup;
                        log_message('info', "[RECOVERY] Ditemukan di admin backup untuk hash: {$block['block_hash']}");
                    } else {
                        // 3. Fallback: cari berdasarkan identifier (risiko duplikat)
                        $adminBackup = $this->backupModel->getBackupByIdentifier(
                            $block['nomor_permohonan'],
                            $block['tanggal_dokumen']
                        );
                        if ($adminBackup) {
                            $recoverySource = 'admin_fallback';
                            $recoveryData = $adminBackup;
                            log_message('info', "[RECOVERY] Fallback ke admin berdasarkan identifier");
                        }
                    }
                }

                // Jika ditemukan data valid, lakukan recovery
                if ($recoveryData) {
                    // Hitung ulang hash berdasarkan data pemulihan (untuk konsistensi logika)
                    $dataRehash = $recoveryData['nomor_permohonan'] . $recoveryData['nomor_dokumen'] .
                        $recoveryData['tanggal_dokumen'] . $recoveryData['tanggal_filing'] .
                        $recoveryData['dokumen_base64'];
                    $correctHash = hash('sha256', $dataRehash);

                    // Pastikan recoveryData memiliki semua field yang diperlukan
                    $updateData = [
                        'nomor_permohonan' => $recoveryData['nomor_permohonan'],
                        'nomor_dokumen'    => $recoveryData['nomor_dokumen'],
                        'tanggal_dokumen'  => $recoveryData['tanggal_dokumen'],
                        'tanggal_filing'   => $recoveryData['tanggal_filing'],
                        'dokumen_base64'   => $recoveryData['dokumen_base64'],
                        'ip_address'       => $recoveryData['ip_address'] ?? $block['ip_address'],
                        'block_hash'       => $correctHash,
                        'previous_hash'    => $recoveryData['previous_hash'] ?? $block['previous_hash'],
                    ];

                    if ($this->blockModel->update($block['id'], $updateData)) {
                        $recoveredCount++;
                        $recoveredBlocks[] = [
                            'id' => $block['id'],
                            'source' => $recoverySource,
                            'nomor' => $recoveryData['nomor_permohonan']
                        ];

                        log_message('info', "[RECOVERY] Berhasil memulihkan Blok ID {$block['id']} dari {$recoverySource}");

                        // Log aktivitas recovery
                        $this->activityLogModel->logActivity([
                            'action_type' => 'RECOVER',
                            'block_id' => $block['id'],
                            'identifier' => $recoveryData['nomor_permohonan'],
                            'status' => 'Recovered',
                            'description' => "Data dipulihkan dari {$recoverySource}",
                            'original_data' => ['hash' => $block['block_hash']],
                            'modified_data' => ['hash' => $correctHash]
                        ]);
                    }
                } else {
                    log_message('warning', "[RECOVERY] Tidak ditemukan data valid untuk Blok ID {$block['id']}");
                }
            }
        }

        // === Sinkronisasi Otomatis: Pastikan userdb → konsensusdb & admindb sinkron ===
        // (Opsional: jalankan hanya jika tidak ada manipulasi, atau selalu)
        $this->syncValidBlocksToBackups($allBlocks);

        // === Flash message ===
        if ($manipulatedCount > 0) {
            $message = "<strong>🔍 Deteksi Manipulasi</strong><br>";
            if ($recoveredCount > 0) {
                $message .= "<span class='text-green-700'>✓ {$recoveredCount} blok berhasil dipulihkan</span><br>";
            } else {
                $message .= "<span class='text-red-700'>❌ Tidak ada backup valid untuk pemulihan</span><br>";
            }
            $this->session->setFlashdata('warning', $message);
        }
    }
    private function syncValidBlocksToBackups(array $allBlocks): void
    {
        // Mensinkronisasi blok yang valid ke database cadangan dan konsensus.
        foreach ($allBlocks as $block) {
            $dataToHash = $block['nomor_permohonan'] . $block['nomor_dokumen'] .
                $block['tanggal_dokumen'] . $block['tanggal_filing'] .
                $block['dokumen_base64'];
            $currentHash = hash('sha256', $dataToHash);

            // Hanya sinkronkan jika hash valid
            if ($currentHash === $block['block_hash']) {
                // Sinkron ke Konsensus (jika belum ada)
                $existsInKonsensus = $this->konsensusDb->table('konsensus')
                    ->where('block_hash', $block['block_hash'])
                    ->countAllResults() > 0;

                if (!$existsInKonsensus) {
                    $this->konsensusDb->table('konsensus')->insert($block);
                    log_message('info', "[SYNC] Tambah ke konsensus: {$block['block_hash']}");
                }

                // Sinkron ke Admin Backup (jika belum ada)
                $adminBackup = $this->backupModel->getBackupByIdentifier(
                    $block['nomor_permohonan'],
                    $block['tanggal_dokumen']
                );
                if (!$adminBackup) {
                    $this->backupModel->createBackup($block, 'auto_sync');
                    log_message('info', "[SYNC] Tambah ke admin backup: {$block['nomor_permohonan']}");
                }
            }
        }
    }

    private function findValidDataForBackup(array $manipulatedBlock): ?array
    {
        // Placeholder untuk pengembangan di masa depan: mencari data valid untuk backup.
        return null;
    }
}
