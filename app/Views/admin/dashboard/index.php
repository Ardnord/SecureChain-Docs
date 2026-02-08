<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
    <div class="mb-6 rounded-lg bg-red-600 border-l-4 border-red-800 p-4 text-white" role="alert">
        <p class="font-medium"><?= session()->getFlashdata('success') ?? session()->getFlashdata('error') ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($manipulatedData)): ?>
    <div class="mb-6 rounded-lg bg-red-600 border-l-4 border-red-800 p-3 text-white">
        <div class="flex items-center justify-between">
            <div>
                <span class="font-bold">Manipulasi Terdeteksi:</span>
                <span class="text-sm ml-2"><?= count($manipulatedData) ?> dokumen dimanipulasi
                    <?php if (!empty($recoveryResults)): ?>
                        | <?= count($recoveryResults) ?> dipulihkan
                    <?php endif; ?>
                </span>
            </div>
            <button onclick="this.parentElement.parentElement.classList.toggle('hidden')" class="text-white hover:text-red-200 transition-colors" aria-label="close-alert">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <details class="mt-2">
            <summary class="cursor-pointer text-white text-xs hover:text-red-200 transition-colors">Lihat detail ↓</summary>
            <div class="mt-2 space-y-2">
                <?php foreach ($manipulatedData as $data): ?>
                    <div class="bg-white bg-opacity-10 rounded p-2 text-white text-xs flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold"><?= esc($data['nomor_permohonan']) ?></span>
                            <span class="mx-2">•</span>
                            <span><?= esc($data['nomor_dokumen']) ?></span>
                        </div>
                        <?php if ($data['has_backup']): ?>
                            <a href="<?= base_url('/admin/recover/' . $data['block_id']) ?>"
                                class="px-3 py-1 bg-white text-red-600 rounded hover:bg-red-50 font-semibold transition-colors">
                                Pulihkan
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= number_format($stats['total_blocks'] ?? 0) ?></p>
        <p class="text-sm text-slate-600">Total Blok</p>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= number_format($stats['total_backups'] ?? 0) ?></p>
        <p class="text-sm text-slate-600">Total Cadangan</p>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= number_format($stats['active_whitelist'] ?? 0) ?></p>
        <p class="text-sm text-slate-600">IP Aktif (<?= number_format($stats['total_whitelist'] ?? 0) ?> total)</p>
    </div>

    <div class="bg-<?= ($stats['chain_valid'] ?? true) ? 'white' : 'red-600' ?> border-2 border-<?= ($stats['chain_valid'] ?? true) ? 'slate-200' : 'red-700' ?> rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-<?= ($stats['chain_valid'] ?? true) ? 'slate-900' : 'white' ?> rounded-lg p-3">
                <?php if ($stats['chain_valid'] ?? true): ?>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                <?php else: ?>
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                <?php endif; ?>
            </div>
        </div>
        <p class="text-2xl font-bold text-<?= ($stats['chain_valid'] ?? true) ? 'slate-900' : 'white' ?> mb-1">
            <?= ($stats['chain_valid'] ?? true) ? 'VALID' : 'TIDAK VALID' ?>
        </p>
        <p class="text-sm text-<?= ($stats['chain_valid'] ?? true) ? 'slate-600' : 'red-100' ?>">
            <?php if ($stats['chain_valid'] ?? true): ?>
                Integritas Rantai OK
            <?php else: ?>
                <?= ($stats['manipulated_count'] ?? 0) > 0 ? $stats['manipulated_count'] . ' Data Termanipulasi' : 'Struktur Rantai Tidak Valid' ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white border-2 border-slate-200 rounded-lg">
        <div class="border-b-2 border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-900">Quick Actions</h2>
            <p class="mt-1 text-sm text-slate-600">Akses cepat ke fitur utama</p>
        </div>
        <div class="p-6 space-y-3">
            <a href="<?= base_url('/admin/explorer') ?>"
                class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-900 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-slate-900">Blockchain Explorer</span>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="<?= base_url('/admin/backup/create') ?>"
                class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-900 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="font-semibold text-slate-900">Create Backup</span>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="<?= base_url('/admin/whitelist') ?>"
                class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-900 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-slate-900">Manage IP Whitelist</span>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="<?= base_url('/admin/monitoring') ?>"
                class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-900 rounded-lg p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-slate-900">System Monitoring</span>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg">
        <div class="border-b-2 border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-900">System Information</h2>
            <p class="mt-1 text-sm text-slate-600">Informasi sistem blockchain</p>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-sm font-medium text-slate-700">Genesis Block</span>
                <span class="text-sm font-mono font-semibold text-slate-900">
                    <?= $stats['genesis_block'] ? '#' . $stats['genesis_block']['id'] : 'N/A' ?>
                </span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-sm font-medium text-slate-700">Latest Block Time</span>
                <span class="text-sm font-semibold text-slate-900">
                    <?= $stats['latest_block_time'] ? date('d/m/Y H:i', strtotime($stats['latest_block_time'])) : 'N/A' ?>
                </span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-sm font-medium text-slate-700">Chain Integrity</span>
                <span class="text-sm font-semibold text-slate-900">
                    <?= ($stats['chain_valid'] ?? true) ? 'Valid' : 'Invalid' ?>
                </span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                <span class="text-sm font-medium text-slate-700">Manipulated Data</span>
                <span class="text-sm font-semibold text-slate-900">
                    <?= $stats['manipulated_count'] ?? 0 ?> documents
                </span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white border-2 border-slate-200 rounded-lg">
    <div class="border-b-2 border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Latest Blocks</h2>
                <p class="mt-1 text-sm text-slate-600">10 blok terbaru dalam blockchain</p>
            </div>
            <a href="<?= base_url('/admin/explorer') ?>"
                class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-700 font-semibold text-sm transition-colors">
                View All
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Block ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Permohonan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Tanggal Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Hash</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <?php if (!empty($latestBlocks)): ?>
                    <?php foreach ($latestBlocks as $block): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-slate-900">#<?= $block['id'] ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-slate-900"><?= esc($block['nomor_permohonan']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y', strtotime($block['tanggal_dokumen'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y H:i', strtotime($block['timestamp'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-slate-500"><?= substr($block['block_hash'], 0, 16) ?>...</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Belum ada blok dalam blockchain
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 bg-white border-2 border-slate-200 rounded-lg">
    <div class="border-b-2 border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900">Log Aktivitas</h2>
        <p class="mt-1 text-sm text-slate-600">Riwayat aktivitas manipulasi dan pemulihan data</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Aksi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Blok/Identitas</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white" id="activity-logs-tbody">
                <?php if (!empty($activityLogs)): ?>
                    <?php foreach ($activityLogs as $log): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $actionLabels = [
                                    'CREATE' => 'BUAT',
                                    'UPDATE' => 'UBAH',
                                    'DELETE' => 'HAPUS',
                                    'MANIPULATE' => 'MANIPULASI',
                                    'RECOVER' => 'PEMULIHAN',
                                    'CHECK' => 'PENGECEKAN',
                                    'CONSENSUS_CHECK' => 'PENGECEKAN',
                                    'CONSENSUS_RECOVER' => 'PEMULIHAN',
                                    'CONSENSUS_SYNC' => 'SYNC'
                                ];
                                $colors = [
                                    'CREATE' => 'bg-slate-900 text-white',
                                    'UPDATE' => 'bg-amber-600 text-white',
                                    'DELETE' => 'bg-red-600 text-white',
                                    'MANIPULATE' => 'bg-red-600 text-white',
                                    'RECOVER' => 'bg-green-600 text-white',
                                    'CHECK' => 'bg-slate-600 text-white',
                                    'CONSENSUS_CHECK' => 'bg-slate-600 text-white',
                                    'CONSENSUS_RECOVER' => 'bg-green-600 text-white',
                                    'CONSENSUS_SYNC' => 'bg-amber-600 text-white'
                                ];
                                $color = $colors[$log['action_type']] ?? 'bg-slate-600 text-white';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded <?= $color ?>">
                                    <?= $actionLabels[$log['action_type']] ?? esc($log['action_type']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php
                                if ($log['action_type'] === 'CONSENSUS_CHECK') {
                                    $description = $log['description'];
                                    if (preg_match('/(\d+)\s+healthy.*?(\d+)\s+minority.*?(\d+)\s+no-consensus.*?(\d+)\s+missing/', $description, $matches)) {
                                        $healthy = $matches[1];
                                        $minority = $matches[2];
                                        $noConsensus = $matches[3];
                                        $missing = $matches[4];
                                        $totalAnomalies = $minority + $noConsensus + $missing;
                                        
                                        if ($totalAnomalies > 0) {
                                            echo '<div class="space-y-1">';
                                            echo '<span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Manipulasi: ' . $totalAnomalies . '</span>';
                                            echo '<div class="text-xs text-slate-600 mt-1">';
                                            if ($minority > 0) echo '<div>• Minority: ' . $minority . '</div>';
                                            if ($noConsensus > 0) echo '<div>• No-Consensus: ' . $noConsensus . '</div>';
                                            if ($missing > 0) echo '<div>• Missing: ' . $missing . '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="space-y-1">';
                                            echo '<span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">✓ Sehat</span>';
                                            echo '<div class="text-xs text-slate-600 mt-1">Healthy: ' . $healthy . '</div>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '-';
                                    }
                                } else if ($log['block_id']): ?>
                                    <span class="font-mono text-slate-900">#<?= $log['block_id'] ?></span>
                                    <?php if ($log['identifier']): ?>
                                        <div class="text-slate-600 text-xs mt-1">(<?= esc($log['identifier']) ?>)</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusLabels = [
                                    'Manipulated' => 'Dimanipulasi',
                                    'Recovered' => 'Dipulihkan',
                                    'SUCCESS' => 'Berhasil',
                                    'Failed' => 'Gagal',
                                    'WARNING' => 'Peringatan',
                                    'INFO' => 'Info'
                                ];
                                $statusText = $statusLabels[$log['status']] ?? $log['status'];
                                ?>
                                <?php
                                $statusClass = 'bg-slate-600 text-white';
                                if ($log['status'] === 'Manipulated' || $log['status'] === 'Failed') {
                                    $statusClass = 'bg-red-600 text-white';
                                } elseif ($log['status'] === 'Recovered' || $log['status'] === 'SUCCESS') {
                                    $statusClass = 'bg-green-600 text-white';
                                } elseif ($log['status'] === 'WARNING') {
                                    $statusClass = 'bg-amber-600 text-white';
                                }
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded <?= $statusClass ?>">
                                    <?= esc($statusText) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php 
                                $isAnomalyType = in_array($log['action_type'], ['MANIPULATE', 'CONSENSUS_CHECK']);
                                $hasAnomaly = false;
                                $anomalyDetails = [];
                                
                                if ($log['action_type'] === 'MANIPULATE' && strpos($log['description'], 'Field yang dimanipulasi:') === 0) {
                                    $hasAnomaly = true;
                                    $details = str_replace('Field yang dimanipulasi: ', '', $log['description']);
                                    $anomalyDetails = explode(', ', $details);
                                } 
                                else if ($log['action_type'] === 'CONSENSUS_CHECK') {
                                    if (preg_match('/(\d+)\s+minority|(\d+)\s+no-consensus/', $log['description'])) {
                                        $hasAnomaly = true;
                                    }
                                }
                                ?>
                                
                                <?php if ($hasAnomaly && $log['action_type'] === 'MANIPULATE'): ?>
                                    <div class="space-y-1">
                                        <p class="font-semibold text-red-700">Anomali Terdeteksi - Manipulasi:</p>
                                        <div class="text-xs text-slate-700 bg-red-50 border border-red-200 rounded p-2 mt-1">
                                            <?php foreach ($anomalyDetails as $field): ?>
                                                <div class="py-0.5">
                                                    <span class="font-mono text-red-800">→</span> <?= esc($field) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php elseif ($hasAnomaly && $log['action_type'] === 'CONSENSUS_CHECK'): ?>
                                    <div class="space-y-1">
                                        <div class="text-xs text-slate-700 bg-amber-50 border border-amber-200 rounded p-2 mt-1">
                                            <div class="py-0.5"><?= esc($log['description']) ?></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php if ($log['action_type'] === 'CONSENSUS_CHECK'): ?>
                                        <div class="space-y-1">
                                            <p class="font-semibold text-green-700">Tidak Ada Anomali</p>
                                            <div class="text-xs text-slate-700 bg-green-50 border border-green-200 rounded p-2 mt-1">
                                                <div class="py-0.5"><?= esc($log['description']) ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-600"><?= esc($log['description']) ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Belum ada log aktivitas
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function refreshActivityLogs() {
        try {
            const response = await fetch('<?= base_url('/api/activity-logs?limit=10') ?>');
            const result = await response.json();

            if (result.status === 'success' && result.data.logs) {
                const tbody = document.getElementById('activity-logs-tbody');
                tbody.innerHTML = result.data.logs.map(log => {
                    const actionLabels = {
                        'CREATE': 'BUAT',
                        'UPDATE': 'UBAH',
                        'DELETE': 'HAPUS',
                        'MANIPULATE': 'MANIPULASI',
                        'RECOVER': 'PEMULIHAN',
                        'CHECK': 'PENGECEKAN',
                        'CONSENSUS_CHECK': 'PENGECEKAN',
                        'CONSENSUS_RECOVER': 'PEMULIHAN',
                        'CONSENSUS_SYNC': 'SYNC'
                    };
                    const statusLabels = {
                        'Manipulated': 'Dimanipulasi',
                        'Recovered': 'Dipulihkan',
                        'Success': 'Berhasil',
                        'Failed': 'Gagal',
                        'WARNING': 'Peringatan',
                        'INFO': 'Info'
                    };
                    const colors = {
                        'CREATE': 'bg-slate-900 text-white',
                        'UPDATE': 'bg-amber-600 text-white',
                        'DELETE': 'bg-red-600 text-white',
                        'MANIPULATE': 'bg-red-600 text-white',
                        'RECOVER': 'bg-green-600 text-white',
                        'CHECK': 'bg-slate-600 text-white',
                        'CONSENSUS_CHECK': 'bg-slate-600 text-white',
                        'CONSENSUS_RECOVER': 'bg-green-600 text-white',
                        'CONSENSUS_SYNC': 'bg-amber-600 text-white'
                    };
                    const color = colors[log.action_type] || 'bg-slate-600 text-white';
                    const actionLabel = actionLabels[log.action_type] || log.action_type;
                    const statusLabel = statusLabels[log.status] || log.status;

                    return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                            ${new Date(log.created_at).toLocaleString('id-ID')}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded ${color}">
                                ${actionLabel}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            ${log.action_type === 'CONSENSUS_CHECK' ? `
                                ${log.description.includes('minority') || log.description.includes('no-consensus') || log.description.includes('missing') ? `
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Manipulasi</span>
                                        <div class="text-xs text-slate-600 mt-1">${log.description}</div>
                                    </div>
                                ` : `
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">✓ Sehat</span>
                                        <div class="text-xs text-slate-600 mt-1">${log.description}</div>
                                    </div>
                                `}
                            ` : `
                                ${log.block_id ? `<span class="font-mono text-slate-900">#${log.block_id}</span>` : ''}
                                ${log.identifier ? `<span class="text-slate-600 text-xs">(${log.identifier})</span>` : ''}
                            `}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded ${log.status === 'Manipulated' ? 'bg-red-600 text-white' : (log.status === 'Recovered' ? 'bg-green-600 text-white' : 'bg-slate-600 text-white')}">
                                ${statusLabel}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            ${log.action_type === 'MANIPULATE' && log.description.startsWith('Field yang dimanipulasi:') ? `
                                <div class="space-y-1">
                                    <p class="font-semibold text-red-700">⚠️ Anomali Terdeteksi - Manipulasi:</p>
                                    <div class="text-xs text-slate-700 bg-red-50 border border-red-200 rounded p-2 mt-1">
                                        ${log.description.replace('Field yang dimanipulasi: ', '').split(', ').map(field => 
                                            `<div class="py-0.5"><span class="font-mono text-red-800">→</span> ${field}</div>`
                                        ).join('')}
                                    </div>
                                </div>
                            ` : log.action_type === 'CONSENSUS_CHECK' ? `
                                ${log.description.includes('minority') || log.description.includes('no-consensus') || log.description.includes('missing') ? `
                                    <div class="space-y-1">
                                        <p class="font-semibold text-amber-700">Manipulasi Terdeteksi</p>
                                        <div class="text-xs text-slate-700 bg-amber-50 border border-amber-200 rounded p-2 mt-1">
                                            <div class="py-0.5">${log.description}</div>
                                        </div>
                                    </div>
                                ` : `
                                    <div class="space-y-1">
                                        <p class="font-semibold text-green-700">✓ Tidak Ada Anomali</p>
                                        <div class="text-xs text-slate-700 bg-green-50 border border-green-200 rounded p-2 mt-1">
                                            <div class="py-0.5">${log.description}</div>
                                        </div>
                                    </div>
                                `}
                            ` : `
                                <span class="text-slate-600">${log.description}</span>
                            `}
                        </td>
                    </tr>
                `;
                }).join('');
            }
        } catch (error) {
            console.error('Error refreshing logs:', error);
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg border-2 z-50 ${
        type === 'success' ? 'bg-green-600 border-green-700 text-white' :
        type === 'warning' ? 'bg-amber-600 border-amber-700 text-white' :
        type === 'error' ? 'bg-red-600 border-red-700 text-white' :
        'bg-slate-900 border-slate-900 text-white'
    }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    <?= $this->endSection() ?>