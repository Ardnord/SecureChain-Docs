<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Recovery Configuration
 * 
 * Konfigurasi untuk sistem auto-recovery berbasis konsensus mayoritas
 * dari 3 database: userdb, admindb, konsensus
 */
class Recovery extends BaseConfig
{
    /**
     * Enable/Disable Auto-Recovery
     * 
     * Jika true, sistem akan otomatis memperbaiki data corrupt
     * berdasarkan konsensus mayoritas (2 dari 3 database)
     */
    public bool $autoRecoverEnabled = true;

    /**
     * Check Interval (dalam detik)
     * 
     * Interval waktu untuk menjalankan pengecekan konsensus
     * Default: 300 detik (5 menit)
     */
    public int $checkIntervalSeconds = 300;

    /**
     * Alert Threshold
     * 
     * Jumlah minimum inkonsistensi yang memicu alert/notifikasi
     * Default: 1 (alert jika ada minimal 1 data corrupt)
     */
    public int $alertThreshold = 1;

    /**
     * Blacklist Tables
     * 
     * Daftar tabel yang TIDAK BOLEH di-auto-recover
     * Tabel-tabel ini harus di-recover manual oleh admin
     * 
     * @var array<string>
     */
    public array $blacklistTables = [
        'users',
        'ip_whitelist',
        'activity_logs',
        'recovery_history',
        'blockchain_backup'
    ];

    /**
     * Database Groups untuk Konsensus
     * 
     * Urutan prioritas untuk voting:
     * 1. konsensus (highest priority - immutable ledger)
     * 2. admindb (backup & audit trail)
     * 3. userdb (public-facing data)
     */
    public array $databaseGroups = [
        'konsensus',
        'admindb',
        'userdb'
    ];

    /**
     * Target Table untuk Monitoring
     * 
     * Tabel yang akan dimonitor untuk konsensus
     * Format: ['db_group' => 'table_name']
     */
    public array $monitoredTables = [
        'userdb' => 'blockchain',
        'admindb' => 'blockchain_backup',
        'konsensus' => 'konsensus'
    ];

    /**
     * Primary Key untuk Pencocokan Data
     * 
     * Kolom yang digunakan untuk mencocokkan record yang sama
     * di ketiga database
     */
    public string $primaryMatchKey = 'block_hash';

    /**
     * Fallback Match Keys
     * 
     * Jika primary key tidak ditemukan, gunakan kombinasi kolom ini
     * 
     * @var array<string>
     */
    public array $fallbackMatchKeys = [
        'nomor_permohonan',
        'tanggal_dokumen'
    ];

    /**
     * Fields untuk Checksum Calculation
     * 
     * Field yang digunakan untuk menghitung checksum/hash
     * Harus sesuai dengan logika hash saat insert
     * 
     * @var array<string>
     */
    public array $checksumFields = [
        'nomor_permohonan',
        'nomor_dokumen',
        'tanggal_dokumen',
        'tanggal_filing',
        'dokumen_base64'
    ];

    /**
     * Recovery Strategy
     * 
     * PENTING:
     * - Data corrupt TIDAK di-backup ke blockchain_backup (karena corrupt)
     * - Data corrupt hanya disimpan di recovery_history.before_data (untuk rollback)
     * - Backup ke blockchain_backup hanya untuk data VALID
     * 
     * Mekanisme:
     * 1. Deteksi corrupt data (minority 1 vs 2)
     * 2. Simpan corrupt data ke recovery_history.before_data
     * 3. Langsung repair dengan data majority
     * 4. Rollback capability dari recovery_history
     */
    public bool $storeCorruptDataInHistory = true;

    /**
     * Maximum Recovery History Records
     * 
     * Jumlah maksimal record history yang disimpan
     * Older records akan dihapus otomatis
     */
    public int $maxHistoryRecords = 1000;

    /**
     * Enable Notifications
     * 
     * Kirim notifikasi ke admin saat ada inkonsistensi
     */
    public bool $enableNotifications = true;

    /**
     * Notification Channels
     * 
     * Channel untuk mengirim notifikasi
     * Supported: 'log', 'email', 'webhook'
     * 
     * @var array<string>
     */
    public array $notificationChannels = [
        'log' // Default: log ke file
    ];

    /**
     * Admin Email untuk Notifikasi
     * 
     * Email yang menerima notifikasi inkonsistensi
     */
    public ?string $adminEmail = null;

    /**
     * Webhook URL untuk Notifikasi
     * 
     * URL webhook untuk mengirim alert (opsional)
     */
    public ?string $webhookUrl = null;

    /**
     * Verbose Logging
     * 
     * Jika true, log detail setiap step recovery
     */
    public bool $verboseLogging = true;

    /**
     * Dry Run Mode
     * 
     * Jika true, hanya simulasi tanpa benar-benar update database
     * Berguna untuk testing
     */
    public bool $dryRunMode = false;
}
