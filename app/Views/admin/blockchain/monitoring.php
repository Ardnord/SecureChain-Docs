<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3" title="Total Blocks">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4v8l-9 4-9-4V7z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= $stats['total_blocks'] ?? 0 ?></p>
        <p class="text-sm text-slate-600">Total Data Diamankan</p>
        <div class="mt-3 pt-3 border-t border-slate-200">
            <p class="text-xs text-slate-500">24 jam terakhir: +<?= $stats['blocks_24h'] ?? 0 ?></p>
        </div>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3" title="Total Backups">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= $stats['total_backups'] ?? 0 ?></p>
        <p class="text-sm text-slate-600">Total Cadangan</p>
        <div class="mt-3 pt-3 border-t border-slate-200">
            <p class="text-xs text-slate-500">Cadangan terakhir: <?= $stats['last_backup_time'] ?? 'N/A' ?></p>
        </div>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-slate-900 rounded-lg p-3" title="Active IPs">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zM2 12h20M12 2v20" />
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-900 mb-1"><?= $stats['active_ips'] ?? 0 ?></p>
        <p class="text-sm text-slate-600">IP Aktif</p>
        <div class="mt-3 pt-3 border-t border-slate-200">
            <p class="text-xs text-slate-500">Total: <?= $stats['total_ips'] ?? 0 ?> IP</p>
        </div>
    </div>

    <?php
    $totalIssues = $stats['total_issues'] ?? 0;
    $hasManipulation = $totalIssues > 0;
    ?>
    <div class="<?= $hasManipulation ? 'bg-red-600 border-2 border-red-800 text-white' : 'bg-white border-2 border-slate-200 text-slate-900' ?> rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="<?= $hasManipulation ? 'bg-white' : 'bg-slate-900' ?> rounded-lg p-3" title="Data Issues Detected">
                <?php if ($hasManipulation): ?>
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                <?php else: ?>
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                <?php endif; ?>
            </div>
        </div>
        <p class="text-3xl font-bold mb-1"><?= $totalIssues ?></p>
        <p class="text-sm <?= $hasManipulation ? 'text-white/90' : 'text-slate-600' ?>">Isu Data Terdeteksi</p>
        <div class="mt-3 pt-3 border-t <?= $hasManipulation ? 'border-red-700' : 'border-slate-200' ?>">
            <p class="text-xs <?= $hasManipulation ? 'text-white' : 'text-slate-500' ?>">
                <?php if ($hasManipulation): ?>
                    <strong>PERINGATAN!</strong> Sistem mendeteksi <strong><?= $totalIssues ?></strong> isu pada integritas data. Periksa panel di bawah untuk detail.
                <?php else: ?>
                    Tidak ada isu terdeteksi
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<div class="bg-white border-2 border-slate-200 rounded-lg mb-8 overflow-hidden">
    <div class="border-b-2 border-slate-200 p-6 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Kesehatan Sinkronisasi Database
                </h2>
                <p class="mt-1 text-sm text-slate-600">Memastikan data konsisten di 3 database (Utama, Cadangan, Verifikasi).</p>
            </div>
            <div class="flex gap-2">
                <button onclick="runQuickCheck()" id="quickCheckBtn"
                    class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 font-semibold text-sm transition-colors">
                    Pemeriksaan Cepat
                </button>
                <a href="<?= base_url('/admin/consensus/check') ?>"
                    class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-semibold text-sm transition-colors">
                    Pemeriksaan Lengkap
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div id="consensusHealthPanel" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">
                <div class="animate-pulse">
                    <div class="h-8 bg-slate-200 rounded mb-2"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">
                <div class="animate-pulse">
                    <div class="h-8 bg-slate-200 rounded mb-2"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-center">
                <div class="animate-pulse">
                    <div class="h-8 bg-slate-200 rounded mb-2"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-slate-600">
                <span id="lastCheckTime">Memeriksa...</span>
            </div>
            <div class="flex gap-2">
                <form action="<?= base_url('/admin/consensus/recover') ?>" method="POST" onsubmit="return confirm('Anda yakin ingin memulihkan data yang tidak sinkron secara otomatis? Sistem akan menggunakan data dari mayoritas database yang cocok.')">
                    <?= csrf_field() ?>
                    <button type="submit"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-semibold text-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Pulihkan Otomatis
                    </button>
                </form>
                <a href="<?= base_url('/admin/consensus/history') ?>"
                    class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 font-semibold text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat
                </a>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        runQuickCheck();
    });

    function runQuickCheck() {
        const btn = document.getElementById('quickCheckBtn');
        const panel = document.getElementById('consensusHealthPanel');
        const lastCheckTime = document.getElementById('lastCheckTime');

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Memeriksa...';

        fetch('<?= base_url('/admin/consensus/quick-check') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.data;
                    const healthColor = stats.health_percentage >= 95 ? 'emerald' : stats.health_percentage >= 80 ? 'amber' : 'red';
                    const healthBg = stats.health_percentage >= 95 ? 'bg-emerald-50 border-emerald-200' : stats.health_percentage >= 80 ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200';
                    const healthText = stats.health_percentage >= 95 ? 'text-emerald-700' : stats.health_percentage >= 80 ? 'text-amber-700' : 'text-red-700';

                    const minorityIsAlert = (parseInt(stats.minority_corrupt) || 0) > 0;
                    const noConsensusIsAlert = (parseInt(stats.no_consensus) || 0) > 0;

                    const minorityClasses = minorityIsAlert ? 'bg-red-600 text-white border-2 border-red-800' : 'bg-red-50 border border-red-200 text-red-700';
                    const minorityInnerText = minorityIsAlert ? 'text-white' : 'text-red-700';

                    const noConsensusClasses = noConsensusIsAlert ? 'bg-red-600 text-white border-2 border-red-800' : 'bg-amber-50 border border-amber-200 text-amber-700';
                    const noConsensusInnerText = noConsensusIsAlert ? 'text-white' : 'text-amber-700';

                    panel.innerHTML = `
                    <div class="${healthBg} border rounded-lg p-4">
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <svg class="w-5 h-5 ${healthText}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-3xl font-bold ${healthText}">${stats.health_percentage}%</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Skor Sinkronisasi</p>
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <p class="text-xs text-slate-600">${stats.healthy} / ${stats.total_checked} healthy</p>
                            </div>
                        </div>
                    </div>

                    <div class="${minorityClasses} rounded-lg p-4">
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <svg class="w-5 h-5 ${minorityInnerText}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                </svg>
                                <p class="text-3xl font-bold ${minorityInnerText}">${stats.minority_corrupt}</p>
                            </div>
                            <p class="text-sm font-semibold ${minorityInnerText === 'text-white' ? 'text-white/90' : 'text-slate-700'}">Data Tidak Sinkron</p>
                            <div class="mt-3 pt-3 border-t ${minorityIsAlert ? 'border-red-700' : 'border-slate-200'}">
                                <p class="text-xs ${minorityInnerText === 'text-white' ? 'text-white/90' : 'text-slate-600'}">Bisa dipulihkan otomatis</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="${noConsensusClasses} rounded-lg p-4">
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <svg class="w-5 h-5 ${noConsensusInnerText}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-3xl font-bold ${noConsensusInnerText}">${stats.no_consensus}</p>
                            </div>
                            <p class="text-sm font-semibold ${noConsensusInnerText === 'text-white' ? 'text-white/90' : 'text-slate-700'}">Perlu Tinjauan Manual</p>
                            <div class="mt-3 pt-3 border-t ${noConsensusIsAlert ? 'border-red-700' : 'border-slate-200'}">
                                <p class="text-xs ${noConsensusInnerText === 'text-white' ? 'text-white/90' : 'text-slate-600'}">Data berbeda di semua DB</p>
                            </div>
                        </div>
                    </div>
                `;

                    lastCheckTime.textContent = `Last check: ${new Date().toLocaleString('id-ID')}`;
                } else {
                    panel.innerHTML = '<div class="col-span-3 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg p-4">Error: ' + data.error + '</div>';
                }
            })
            .catch(error => {
                panel.innerHTML = '<div class="col-span-3 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg p-4">Failed to load consensus health</div>';
                console.error('Consensus check error:', error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Quick Check';
            });
    }
</script>

<div class="bg-white border-2 border-slate-200 rounded-lg mb-8">
    <div class="border-b-2 border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900">Recent Activity</h2>
        <p class="mt-1 text-sm text-slate-600">Aktivitas terbaru dalam sistem</p>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <?php if (!empty($latestBlocks)): ?>
                <?php foreach (array_slice($latestBlocks, 0, 5) as $block): ?>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors">
                        <div class="bg-slate-900 rounded-lg p-2 mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-grow">
                            <p class="text-sm font-semibold text-slate-900">Block #<?= $block['id'] ?> Created</p>
                            <p class="text-xs text-slate-600 mt-1"><?= esc($block['nomor_permohonan']) ?></p>
                            <p class="text-xs text-slate-500 mt-1"><?= date('d/m/Y H:i:s', strtotime($block['timestamp'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-sm text-slate-500 text-center py-8">Belum ada aktivitas</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="bg-white border-2 border-slate-200 rounded-lg">
    <div class="border-b-2 border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Latest Backups</h2>
                <p class="mt-1 text-sm text-slate-600">5 backup terbaru dalam sistem</p>
            </div>
            <a href="<?= base_url('/admin/backups') ?>"
                class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-700 font-semibold text-sm transition-colors">
                View All
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Permohonan</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-700">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <?php if (!empty($latestBackups)): ?>
                    <?php foreach ($latestBackups as $backup): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-bold text-slate-900">#<?= $backup['id'] ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-slate-900"><?= esc($backup['nomor_permohonan']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($backup['backup_type'] === 'auto'): ?>
                                    <span class="px-2 py-1 text-xs font-bold rounded-lg bg-slate-900 text-white">Auto</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-bold rounded-lg bg-slate-200 text-slate-900 border border-slate-900">Manual</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y H:i:s', strtotime($backup['backup_timestamp'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                            Belum ada backup
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>