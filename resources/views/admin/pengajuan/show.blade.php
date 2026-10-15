<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan — SIBEST Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#1B2F5B', dark: '#152447' },
                        gold: '#F5A623',
                        'dark-text': '#172B4D',
                        muted: '#6B7280',
                        'border-light': '#E5E7EB',
                        'bg-page': '#F5F7FA',
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body class="bg-bg-page text-dark-text font-jakarta">
<div class="min-h-screen flex">

    @include('partials.sidebar-admin')

    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.pengajuan.index') }}"
                   class="w-9 h-9 rounded-lg border border-border-light flex items-center justify-center text-navy hover:bg-gray-50 shrink-0">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div class="min-w-0">
                    <p class="text-xs text-muted truncate">
                        <a href="{{ route('admin.pengajuan.index') }}" class="hover:underline">Pengajuan</a>
                        <span class="mx-1">›</span>
                        <span class="text-navy font-semibold">{{ $pengajuan->nama_mahasiswa }}</span>
                    </p>
                    <h1 class="text-lg font-extrabold text-navy mt-0.5 truncate">Detail Pengajuan</h1>
                </div>
            </div>

            @php
                $statusConfig = match($pengajuan->status) {
                    'draft'    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'label' => 'Draft'],
                    'diajukan' => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'label' => 'Diajukan'],
                    'diproses' => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',  'label' => 'Diproses'],
                    'ditolak'  => ['bg' => 'bg-red-50',     'text' => 'text-red-700',    'label' => 'Ditolak'],
                    'selesai'  => ['bg' => 'bg-green-50',   'text' => 'text-green-700',  'label' => 'Selesai'],
                    default    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'label' => ucfirst($pengajuan->status)],
                };
            @endphp

            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} shrink-0">
                {{ $statusConfig['label'] }}
            </span>
        </header>

        <div class="p-5 sm:p-8 space-y-5">

            {{-- FLASH MESSAGE --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- HERO CARD --}}
            <section class="bg-white rounded-xl border border-border-light p-5">
                <div class="flex flex-wrap items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-navy text-white flex items-center justify-center font-extrabold text-lg shrink-0">
                        {{ strtoupper(substr($pengajuan->nama_mahasiswa, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-extrabold text-navy">{{ $pengajuan->nama_mahasiswa }}</h2>
                        <p class="text-sm text-muted mt-0.5">NIM {{ $pengajuan->nim_mahasiswa }} · {{ $pengajuan->email_mahasiswa }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            <span class="px-2.5 py-1 rounded-md bg-blue-50 text-navy text-[11px] font-bold">
                                {{ $pengajuan->nama_program }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-[11px] font-bold">
                                {{ $pengajuan->nama_periode }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-[11px] font-bold">
                                {{ ucfirst($pengajuan->jalur ?? '-') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] font-bold uppercase text-muted">Diajukan</p>
                        <p class="text-sm font-bold text-navy mt-1">
                            {{ $pengajuan->submitted_at
                                ? \Carbon\Carbon::parse($pengajuan->submitted_at)->translatedFormat('d F Y')
                                : 'Belum disubmit' }}
                        </p>
                    </div>
                </div>

                @if($pengajuan->admin_note)
                    <div class="mt-4 p-3 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-[10px] font-bold uppercase text-red-600 mb-1">Catatan Admin</p>
                        <p class="text-sm text-red-800">{{ $pengajuan->admin_note }}</p>
                    </div>
                @endif
            </section>

            {{-- TAB NAV (ANCHOR) --}}
            <div class="bg-white rounded-xl border border-border-light overflow-hidden sticky top-0 z-10">
                <nav class="flex">
                    <a href="#data-diri"
                       class="flex-1 text-center py-3.5 text-sm font-bold text-navy border-b-2 border-navy">
                        Data Diri
                    </a>
                    <a href="#dokumen"
                       class="flex-1 text-center py-3.5 text-sm font-medium text-muted border-b-2 border-transparent hover:text-navy">
                        Dokumen
                        <span class="ml-1 text-[10px] bg-gray-100 px-1.5 py-0.5 rounded-full">
                            {{ $totalWajibVerified }}/{{ $totalWajib }}
                        </span>
                    </a>
                    <a href="#tahapan"
                       class="flex-1 text-center py-3.5 text-sm font-medium text-muted border-b-2 border-transparent hover:text-navy">
                        Tahapan
                    </a>
                </nav>
            </div>

            {{-- SECTION 1: DATA DIRI --}}
            <section id="data-diri" class="bg-white rounded-xl border border-border-light p-5 scroll-mt-24">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                        <i data-lucide="user-round" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-extrabold text-navy">Data Diri Mahasiswa</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                    @php
                        $dataDiri = [
                            ['Nama Lengkap', $pengajuan->nama_mahasiswa],
                            ['NIM', $pengajuan->nim_mahasiswa],
                            ['Email', $pengajuan->email_mahasiswa],
                            ['Program Studi', $profile->prodi ?? '-'],
                            ['Angkatan', $profile->angkatan ?? '-'],
                            ['No. HP', $profile->no_hp ?? '-'],
                        ];
                    @endphp

                    @foreach($dataDiri as $field)
                        <div class="flex justify-between items-start gap-4 py-3 border-b border-border-light last:border-b-0">
                            <p class="text-xs text-muted shrink-0">{{ $field[0] }}</p>
                            <p class="text-sm font-medium text-navy text-right break-all">{{ $field[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- SECTION 2: DOKUMEN --}}
            <section id="dokumen" class="bg-white rounded-xl border border-border-light p-5 scroll-mt-24">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                        <i data-lucide="files" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-extrabold text-navy">Verifikasi Dokumen</h3>
                </div>

                <div class="p-3 rounded-lg bg-gray-50 mb-4 flex items-center justify-between flex-wrap gap-2">
                    <p class="text-xs text-muted">
                        <strong class="text-navy">{{ $totalWajibVerified }}/{{ $totalWajib }}</strong> dokumen wajib terverifikasi
                        · <strong class="text-navy">{{ $totalWajibUpload }}/{{ $totalWajib }}</strong> terupload
                    </p>
                    @if($semuaWajibLengkap && $totalWajibVerified === $totalWajib)
                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md">LENGKAP</span>
                    @endif
                </div>

                <div class="divide-y divide-border-light">
                    @forelse($dokumens as $dok)
                        @php
                            $statusVerif = match($dok->verified_status) {
                                'verified' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => 'circle-check',     'label' => 'Verified'],
                                'rejected' => ['bg' => 'bg-red-50',   'text' => 'text-red-700',   'icon' => 'circle-x',         'label' => 'Rejected'],
                                default    => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'clock',            'label' => 'Pending'],
                            };
                            $sudahUpload = !is_null($dok->original_filename);
                        @endphp

                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-bold text-navy">{{ $dok->nama_dokumen }}</p>
                                        @if($dok->required)
                                            <span class="text-[9px] font-bold uppercase bg-red-50 text-red-600 px-1.5 py-0.5 rounded">Wajib</span>
                                        @else
                                            <span class="text-[9px] font-bold uppercase bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">Opsional</span>
                                        @endif
                                    </div>

                                    @if($dok->keterangan)
                                        <p class="text-[11px] text-muted mt-1">{{ $dok->keterangan }}</p>
                                    @endif

                                    @if($sudahUpload)
                                        <div class="flex items-center gap-2 mt-2 text-xs text-muted">
                                            <i data-lucide="file" class="w-3.5 h-3.5 shrink-0"></i>
                                            <a href="{{ $dok->file_url }}" target="_blank"
                                               class="text-navy font-medium hover:underline truncate max-w-xs">
                                                {{ $dok->original_filename }}
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-xs text-muted mt-2 italic">Belum diupload</p>
                                    @endif

                                    @if($dok->verified_note)
                                        <p class="text-[11px] text-red-700 mt-2">Catatan: {{ $dok->verified_note }}</p>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold {{ $statusVerif['bg'] }} {{ $statusVerif['text'] }}">
                                        <i data-lucide="{{ $statusVerif['icon'] }}" class="w-3 h-3"></i>
                                        {{ $statusVerif['label'] }}
                                    </span>

                                    @if($sudahUpload && $dok->id_dokumen_pengajuan)
                                        <div class="flex gap-1.5">
                                            {{-- Verifikasi --}}
                                            <form method="POST"
                                                  action="{{ route('admin.dokumen.verify', $dok->id_dokumen_pengajuan) }}"
                                                  class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 h-8 px-2.5 rounded-md border border-green-200 text-[11px] font-bold text-green-700 hover:bg-green-50">
                                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                    Verifikasi
                                                </button>
                                            </form>

                                            {{-- Tolak (buka modal) --}}
                                            <button type="button"
                                                    data-dokumen-id="{{ $dok->id_dokumen_pengajuan }}"
                                                    data-dokumen-nama="{{ $dok->nama_dokumen }}"
                                                    onclick="bukaModalTolak(this)"
                                                    class="inline-flex items-center gap-1 h-8 px-2.5 rounded-md border border-red-200 text-[11px] font-bold text-red-700 hover:bg-red-50">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                Tolak
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-sm text-muted italic">Tidak ada dokumen untuk pengajuan ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- SECTION 3: TAHAPAN --}}
            <section id="tahapan" class="bg-white rounded-xl border border-border-light p-5 scroll-mt-24">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                        <i data-lucide="route" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-extrabold text-navy">Progress Tahapan</h3>
                </div>

                @forelse($tahapans as $tahap)
                    @php
                        $statusTahap = match($tahap->status) {
                            'lolos' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => 'circle-check', 'label' => 'Lolos'],
                            'gagal' => ['bg' => 'bg-red-50',   'text' => 'text-red-700',   'icon' => 'circle-x',     'label' => 'Gagal'],
                            default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'clock',        'label' => 'Menunggu'],
                        };
                    @endphp

                    <div class="flex items-center justify-between py-3 border-b border-border-light last:border-b-0">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-navy">{{ $tahap->urutan }}. {{ $tahap->nama_tahapan }}</p>
                            @if($tahap->catatan)
                                <p class="text-xs text-muted mt-1">{{ $tahap->catatan }}</p>
                            @endif
                            @if($tahap->nama_pemutus)
                                <p class="text-[10px] text-muted mt-1">Oleh: {{ $tahap->nama_pemutus }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold {{ $statusTahap['bg'] }} {{ $statusTahap['text'] }} shrink-0">
                            <i data-lucide="{{ $statusTahap['icon'] }}" class="w-3 h-3"></i>
                            {{ $statusTahap['label'] }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-muted italic text-center py-4">Belum ada tahapan.</p>
                @endforelse
            </section>

            {{-- AKSI PENGAJUAN --}}
            @if(in_array($pengajuan->status, ['diajukan', 'diproses']))
                <section class="bg-white rounded-xl border border-border-light p-5">
                    <h3 class="font-extrabold text-navy mb-3">Aksi Pengajuan</h3>
                    <div class="flex flex-wrap gap-2">

                        @if($pengajuan->status === 'diajukan')
                            <form method="POST" action="{{ route('admin.pengajuan.updateStatus', $pengajuan->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="diproses">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                                    <i data-lucide="arrow-right-circle" class="w-4 h-4"></i>
                                    Proses Pengajuan
                                </button>
                            </form>
                        @endif

                        <button type="button"
                                onclick="bukaModalTolakPengajuan()"
                                class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-red-200 text-red-700 text-xs font-bold hover:bg-red-50">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            Tolak Pengajuan
                        </button>
                    </div>
                </section>
            @endif

        </div>
    </main>
</div>

{{-- ============================================================
     MODAL: TOLAK DOKUMEN
     ============================================================ --}}
<div id="modalTolakDokumen" class="hidden fixed inset-0 z-50 bg-navy/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <form method="POST" id="formTolakDokumen" action="">
            @csrf
            <input type="hidden" name="status" value="rejected">

            <div class="p-5 border-b border-border-light">
                <h3 class="font-bold text-navy">Tolak Dokumen</h3>
                <p class="text-xs text-muted mt-1" id="namaDokumenTolak">-</p>
            </div>

            <div class="p-5">
                <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Catatan (wajib)</label>
                <textarea name="note" rows="3" required
                          placeholder="Jelaskan alasan dokumen ditolak..."
                          class="w-full px-3 py-2 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy resize-none"></textarea>
            </div>

            <div class="p-5 border-t border-border-light flex justify-end gap-2">
                <button type="button" onclick="tutupModalTolak()"
                        class="h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-muted hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="h-10 px-4 rounded-lg bg-red-600 text-white text-xs font-bold hover:bg-red-700">
                    Tolak Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     MODAL: TOLAK PENGAJUAN
     ============================================================ --}}
<div id="modalTolakPengajuan" class="hidden fixed inset-0 z-50 bg-navy/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <form method="POST" action="{{ route('admin.pengajuan.updateStatus', $pengajuan->id) }}">
            @csrf
            <input type="hidden" name="status" value="ditolak">

            <div class="p-5 border-b border-border-light">
                <h3 class="font-bold text-navy">Tolak Pengajuan</h3>
                <p class="text-xs text-muted mt-1">{{ $pengajuan->nama_mahasiswa }} — {{ $pengajuan->nama_program }}</p>
            </div>

            <div class="p-5">
                <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Catatan (wajib)</label>
                <textarea name="note" rows="3" required
                          placeholder="Jelaskan alasan pengajuan ditolak..."
                          class="w-full px-3 py-2 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy resize-none"></textarea>
            </div>

            <div class="p-5 border-t border-border-light flex justify-end gap-2">
                <button type="button" onclick="tutupModalTolakPengajuan()"
                        class="h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-muted hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="h-10 px-4 rounded-lg bg-red-600 text-white text-xs font-bold hover:bg-red-700">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
});

function bukaModalTolak(el) {
    const idDokumen  = el.dataset.dokumenId;
    const namaDokumen = el.dataset.dokumenNama;

    const modal = document.getElementById('modalTolakDokumen');
    const form  = document.getElementById('formTolakDokumen');
    const label = document.getElementById('namaDokumenTolak');

    form.action = `/admin/dokumen/${idDokumen}/verify`;
    label.textContent = namaDokumen;
    modal.classList.remove('hidden');
}

function tutupModalTolak() {
    document.getElementById('modalTolakDokumen').classList.add('hidden');
}

function bukaModalTolakPengajuan() {
    document.getElementById('modalTolakPengajuan').classList.remove('hidden');
}

function tutupModalTolakPengajuan() {
    document.getElementById('modalTolakPengajuan').classList.add('hidden');
}
</script>

</body>
</html>