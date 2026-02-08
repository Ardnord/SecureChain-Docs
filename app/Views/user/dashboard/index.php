<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

    <div class="lg:col-span-1">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm sticky top-24">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-lg font-semibold text-slate-700">Unggah Dokumen Baru</h2>
                <p class="mt-1 text-sm text-slate-500">Amankan dokumen baru ke dalam rantai blok.</p>
            </div>
            <div class="p-5">
                <form action="<?= base_url('/create') ?>" method="post" enctype="multipart/form-data"
                    x-data="{ submitting: false }" @submit="submitting = true">
                    <?= csrf_field() ?>
                    <div class="space-y-4">
                        <div>
                            <label for="nomor_permohonan" class="block text-sm font-medium text-slate-700">Nomor Permohonan</label>
                            <input type="text" id="nomor_permohonan" name="nomor_permohonan" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="<?= old('nomor_permohonan') ?>" required>
                        </div>
                        <div>
                            <label for="nomor_dokumen" class="block text-sm font-medium text-slate-700">Nomor Dokumen</label>
                            <input type="text" id="nomor_dokumen" name="nomor_dokumen" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="<?= old('nomor_dokumen') ?>" required>
                        </div>
                        <div>
                            <label for="tanggal_dokumen" class="block text-sm font-medium text-slate-700">Tanggal Dokumen</label>
                            <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="<?= old('tanggal_dokumen') ?>" required>
                        </div>
                        <div>
                            <label for="tanggal_filing" class="block text-sm font-medium text-slate-700">Tanggal Filing</label>
                            <input type="date" id="tanggal_filing" name="tanggal_filing" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="<?= old('tanggal_filing') ?>" required>
                        </div>
                        <div>
                            <label for="dokumen" class="block text-sm font-medium text-slate-700">Pilih Dokumen</label>
                            <span class="text-xs text-slate-500">PDF, DOCX, JPG, PNG | Max: 5MB</span>
                            <input type="file" id="dokumen" name="dokumen" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100" required>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="submitting">
                            <span x-show="!submitting" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Unggah dan Amankan
                            </span>
                            <span x-show="submitting" style="display: none;" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengunggah...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col items-start gap-4 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-700">Dokumen Tersimpan</h2>
                    <p class="mt-1 text-sm text-slate-500">Daftar semua dokumen yang telah diamankan dalam rantai blok.</p>
                </div>
                <form action="<?= base_url('/') ?>" method="get" class="relative w-full sm:w-auto">
                    <input type="text" name="keyword" placeholder="Cari dokumen..." class="w-full rounded-lg border-slate-300 pl-4 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64" value="<?= esc(request()->getGet('keyword')) ?>">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-blue-600">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Detail Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Hash Kriptografi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-3">
                                            <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                                <?= esc($doc['id']) ?>
                                            </span>
                                            <div class="flex-grow">
                                                <div>
                                                    <span class="text-xs font-medium text-slate-500">No. Permohonan:</span>
                                                    <p class="text-sm font-semibold text-slate-900 leading-tight"><?= esc($doc['nomor_permohonan']) ?></p>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="text-xs font-medium text-slate-500">No. Dokumen:</span>
                                                    <p class="text-sm text-slate-700 leading-tight"><?= esc($doc['nomor_dokumen']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 pl-10 text-xs text-slate-500 space-y-1">
                                            <div>Tanggal. Dokumen: <strong class="font-medium text-slate-600"><?= esc($doc['tanggal_dokumen']) ?></strong></div>
                                            <div>Tanggal. Filing: <strong class="font-medium text-slate-600"><?= esc($doc['tanggal_filing']) ?></strong></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs font-medium text-slate-600">
                                                Hash Blok:
                                                <span class="font-mono text-slate-900"><?= substr(esc($doc['block_hash']), 0, 30) ?>...</span>
                                            </div>
                                            <div class="text-xs font-medium text-slate-500">
                                                Hash Sebelumnya:
                                                <span class="font-mono"><?= substr(esc($doc['previous_hash']), 0, 30) ?>...</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium">
                                        <a href="<?= base_url('/download/' . esc($doc['block_hash'])) ?>" class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 hover:bg-blue-200 transition-colors">
                                            Unduh
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                    Tidak ada dokumen yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pager) : ?>
                <div class="border-t border-slate-200 p-4">
                    <?php if (!request()->getGet('keyword')): ?>
                        <?= $pager->links('default', 'tailwind_pager') ?>
                    <?php endif; ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>