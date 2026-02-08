<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="mb-6 bg-<?= $chainIntegrity['is_valid'] ? 'white' : 'red-600' ?> border-2 border-<?= $chainIntegrity['is_valid'] ? 'slate-200' : 'red-700' ?> rounded-lg p-6">
    <div class="flex items-center gap-4">
        <div class="bg-<?= $chainIntegrity['is_valid'] ? 'slate-900' : 'white' ?> rounded-lg p-4">
            <svg class="w-10 h-10 text-<?= $chainIntegrity['is_valid'] ? 'white' : 'red-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                <?php if ($chainIntegrity['is_valid']): ?>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                <?php else: ?>
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                <?php endif; ?>
            </svg>
        </div>
        <div class="flex-grow">
            <h2 class="text-2xl font-bold text-<?= $chainIntegrity['is_valid'] ? 'slate-900' : 'white' ?>">
                <?= $chainIntegrity['is_valid'] ? 'Rantai Blok Valid' : 'Rantai Blok Tidak Valid' ?>
            </h2>
            <?php if (!$chainIntegrity['is_valid']): ?>
                <p class="text-red-100 mt-1">
                    Ditemukan <strong><?= $chainIntegrity['invalid_count'] ?> blok tidak valid</strong> dari total <?= $chainIntegrity['total_blocks'] ?> blok.
                </p>
            <?php endif; ?>
        </div>
        <div class="text-right">
            <p class="text-3xl font-bold text-<?= $chainIntegrity['is_valid'] ? 'slate-900' : 'white' ?>">
                <?= $chainIntegrity['total_blocks'] ?>
            </p>
            <p class="text-sm text-<?= $chainIntegrity['is_valid'] ? 'slate-600' : 'red-200' ?>">Total Blok</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="bg-slate-900 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Total Blok</span>
        </div>
        <p class="text-3xl font-bold text-slate-900"><?= number_format($stats['total_blocks']) ?></p>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="bg-slate-900 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Blok Valid</span>
        </div>
        <p class="text-3xl font-bold text-slate-900"><?= number_format($stats['total_blocks'] - $chainIntegrity['invalid_count']) ?></p>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="bg-slate-900 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Blok Tidak Valid</span>
        </div>
        <p class="text-3xl font-bold text-slate-900"><?= number_format($chainIntegrity['invalid_count']) ?></p>
    </div>

    <div class="bg-white border-2 border-slate-200 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="bg-slate-900 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Blok Terbaru</span>
        </div>
        <p class="text-lg font-bold text-slate-900">
            <?= $stats['latest_block_time'] ? date('H:i', strtotime($stats['latest_block_time'])) : 'N/A' ?>
        </p>
        <p class="text-xs text-slate-500 mt-1">
            <?= $stats['latest_block_time'] ? date('d M Y', strtotime($stats['latest_block_time'])) : '' ?>
        </p>
    </div>
</div>

<div class="bg-white border-2 border-slate-200 rounded-lg">
    <div class="border-b-2 border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Semua Blok Blockchain</h2>
                <p class="mt-1 text-sm text-slate-600">Daftar lengkap semua blok dalam rantai blok</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold border border-slate-200">
                <?= count($blocks) ?> Blok
            </span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">ID Blok</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Permohonan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Dokumen</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Tanggal Dokumen</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Timestamp</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Hash Blok</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Hash Sebelumnya</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                <?php if (!empty($blocks)): ?>
                    <?php
                    $invalidBlockIds = array_column($chainIntegrity['invalid_blocks'], 'block_number');

                    foreach ($blocks as $block):
                        $isValid = !in_array($block['id'], $invalidBlockIds);
                    ?>
                        <tr class="hover:bg-slate-50 transition-colors <?= !$isValid ? 'bg-red-50' : '' ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-bold text-slate-900">#<?= $block['id'] ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-slate-900"><?= esc($block['nomor_permohonan']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-slate-700"><?= esc($block['nomor_dokumen']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y', strtotime($block['tanggal_dokumen'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= date('d/m/Y H:i:s', strtotime($block['timestamp'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded"><?= substr($block['block_hash'], 0, 20) ?>...</span>
                                    <button onclick="copyToClipboard('<?= $block['block_hash'] ?>')"
                                        class="text-slate-600 hover:text-slate-900" title="Copy full hash">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded"><?= substr($block['previous_hash'], 0, 20) ?>...</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($isValid): ?>
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-slate-900 text-white">Valid</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-red-600 text-white border border-red-700">Invalid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-lg font-semibold">Belum ada blok dalam blockchain</p>
                            <p class="text-sm mt-1">Blok akan muncul setelah dokumen pertama di-upload</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Hash copied to clipboard!');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>

<?= $this->endSection() ?>