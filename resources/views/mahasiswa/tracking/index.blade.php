<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pengajuan — SIBEST UNUJA</title>

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

    {{-- SIDEBAR --}}
    @include('partials.sidebar-mahasiswa')

    {{-- KONTEN --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div>
                <p class="text-xs text-muted">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="hover:underline">Dashboard</a>
                    <span class="mx-1">›</span>
                    <span class="text-navy font-semibold">Tracking</span>
                </p>
                <h1 class="text-xl font-extrabold text-navy mt-1">Tracking Pengajuan</h1>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">

            {{-- INTRO --}}
            <div class="mb-6">
                <h2 class="text-lg font-bold text-navy">Progres Pengajuan Beasiswa</h2>
                <p class="text-sm text-muted mt-1 leading-relaxed">
                    Halaman ini menampilkan status dan tahapan pengajuan beasiswa Anda secara terperinci.
                </p>
            </div>

            {{-- PENGAJUAN AKTIF --}}
            @if ($pengajuanAktif)
                <section class="mb-6 rounded-lg border-l-4 border-[#183a70] bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="rounded-full bg-[#f3f5f8] px-3 py-1 text-xs font-semibold text-[#6c7890]">
                                Pengajuan Aktif
                            </span>
                            <h2 class="mt-3 text-lg font-bold">
                                {{ $pengajuanAktif->nama_program }}
                            </h2>
                            <p class="mt-1 text-sm text-[#6c7890]">
                                Periode: {{ $pengajuanAktif->nama_periode }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-[#9aa5b7]">Tanggal pengajuan</p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ $pengajuanAktif->submitted_at
                                    ? \Carbon\Carbon::parse($pengajuanAktif->submitted_at)->translatedFormat('d F Y')
                                    : 'Belum diajukan' }}
                            </p>
                            <span class="mt-2 inline-block rounded-full bg-[#fff7e2] px-3 py-1 text-xs font-bold text-[#725a18]">
                                {{ ucfirst($pengajuanAktif->status) }}
                            </span>
                        </div>
                    </div>
                </section>

                {{-- TAHAPAN SELEKSI (TIMELINE) --}}
                @php
                    // Cari urutan tahap yang gagal (kalau ada) - logic biner: 110 = 0
                    $urutanGagal = null;
                    foreach ($pengajuanAktif->tahapan as $t) {
                        if ($t->status === 'gagal') {
                            $urutanGagal = $t->urutan;
                            break;
                        }
                    }
                @endphp

                <section class="mb-6 rounded-lg border border-[#dfe5ee] bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="h-5 w-1 rounded-full bg-[#f2a900]"></span>
                        <h2 class="text-sm font-bold uppercase tracking-wide text-[#183a70]">
                            Tahapan Seleksi
                        </h2>
                        <div class="h-px flex-1 bg-[#dfe5ee]"></div>
                    </div>

                    @forelse ($pengajuanAktif->tahapan as $tahap)
                        @php
                            // Kalau ada tahap gagal, semua tahap setelahnya = tidak dilanjutkan
                            $tidakDilanjutkan = $urutanGagal !== null && $tahap->urutan > $urutanGagal;

                            // Status efektif
                            $statusEfektif = $tidakDilanjutkan ? 'tidak_dilanjutkan' : $tahap->status;

                            // Konfigurasi per status
                            $config = match ($statusEfektif) {
                                'lolos' => [
                                    'marker_bg'   => 'bg-green-100',
                                    'marker_text' => 'text-green-700',
                                    'marker_icon' => 'check',
                                    'card_border' => 'border-[#c8e8d8]',
                                    'card_bg'     => 'bg-white',
                                    'badge_bg'    => 'bg-[#eaf8f1]',
                                    'badge_text'  => 'text-[#24915f]',
                                    'label'       => 'Lolos',
                                ],
                                'gagal' => [
                                    'marker_bg'   => 'bg-red-100',
                                    'marker_text' => 'text-red-700',
                                    'marker_icon' => 'x',
                                    'card_border' => 'border-[#f4caca]',
                                    'card_bg'     => 'bg-white',
                                    'badge_bg'    => 'bg-[#fdeeee]',
                                    'badge_text'  => 'text-[#c94343]',
                                    'label'       => 'Gagal',
                                ],
                                'tidak_dilanjutkan' => [
                                    'marker_bg'   => 'bg-gray-100',
                                    'marker_text' => 'text-gray-400',
                                    'marker_icon' => 'circle',
                                    'card_border' => 'border-[#dfe5ee]',
                                    'card_bg'     => 'bg-[#f7f9fc]',
                                    'badge_bg'    => 'bg-[#f3f5f8]',
                                    'badge_text'  => 'text-[#6c7890]',
                                    'label'       => 'Tidak Dilanjutkan',
                                ],
                                default => [ // menunggu
                                    'marker_bg'   => 'bg-[#fff7e2]',
                                    'marker_text' => 'text-[#725a18]',
                                    'marker_icon' => 'clock',
                                    'card_border' => 'border-[#f0d98f]',
                                    'card_bg'     => 'bg-[#fff7e2]',
                                    'badge_bg'    => 'bg-[#fff7e2]',
                                    'badge_text'  => 'text-[#725a18]',
                                    'label'       => 'Menunggu',
                                ],
                            };
                        @endphp

                        <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-5' : '' }}">

                            {{-- Marker + Garis Vertikal --}}
                            <div class="flex flex-col items-center shrink-0">
                                <div class="w-9 h-9 rounded-full {{ $config['marker_bg'] }} {{ $config['marker_text'] }} flex items-center justify-center border-2 border-white shadow-sm">
                                    <i data-lucide="{{ $config['marker_icon'] }}" class="w-4 h-4"></i>
                                </div>

                                @if(!$loop->last)
                                    <div class="w-px flex-1 min-h-[40px] bg-[#dfe5ee] mt-2"></div>
                                @endif
                            </div>

                            {{-- Card Tahap --}}
                            <div class="flex-1 min-w-0 border {{ $config['card_border'] }} {{ $config['card_bg'] }} rounded-md p-4">

                                {{-- Header Card --}}
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-[#172a46]">{{ $tahap->nama_tahapan }}</h3>
                                        <p class="text-xs text-[#6c7890] mt-1">
                                            @if($tidakDilanjutkan)
                                                Tahap tidak dilanjutkan karena tahap sebelumnya tidak lolos
                                            @elseif($tahap->decided_at)
                                                Diputuskan {{ \Carbon\Carbon::parse($tahap->decided_at)->translatedFormat('d F Y') }}
                                            @elseif($tahap->mulai_at)
                                                Dijadwalkan {{ \Carbon\Carbon::parse($tahap->mulai_at)->translatedFormat('d F Y') }}
                                            @else
                                                Belum ada keputusan
                                            @endif
                                        </p>
                                    </div>

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $config['badge_bg'] }} {{ $config['badge_text'] }} shrink-0">
                                        {{ $config['label'] }}
                                    </span>
                                </div>

                                {{-- Sub-cards: Lokasi / Catatan / Pemutus --}}
                                @if(!$tidakDilanjutkan && ($tahap->lokasi || $tahap->catatan || $tahap->nama_pemutus))
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">

                                        @if($tahap->lokasi)
                                            <div class="flex items-start gap-2 bg-white/80 rounded-md p-2.5">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#6c7890] shrink-0 mt-0.5"></i>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-semibold uppercase text-[#9aa5b7]">Lokasi</p>
                                                    <p class="text-xs text-[#172a46] font-medium break-words">{{ $tahap->lokasi }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($tahap->catatan)
                                            <div class="flex items-start gap-2 bg-white/80 rounded-md p-2.5">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#6c7890] shrink-0 mt-0.5"></i>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-semibold uppercase text-[#9aa5b7]">Catatan</p>
                                                    <p class="text-xs text-[#172a46] font-medium break-words">{{ $tahap->catatan }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($tahap->nama_pemutus)
                                            <div class="flex items-start gap-2 bg-white/80 rounded-md p-2.5">
                                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#6c7890] shrink-0 mt-0.5"></i>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-semibold uppercase text-[#9aa5b7]">Diputuskan oleh</p>
                                                    <p class="text-xs text-[#172a46] font-medium break-words">{{ $tahap->nama_pemutus }}</p>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                @endif

                            </div>
                        </div>

                    @empty
                        <p class="rounded-md bg-[#f7f9fc] p-4 text-sm text-[#6c7890]">
                            Belum ada tahapan seleksi untuk pengajuan ini.
                        </p>
                    @endforelse

                    @if ($pengajuanAktif->hasil_akhir)
                        <div class="mt-4 rounded-md bg-[#f7f9fc] p-4 text-sm text-[#172a46]">
                            Hasil akhir:
                            <strong>{{ ucfirst(str_replace('_', ' ', $pengajuanAktif->hasil_akhir)) }}</strong>
                        </div>
                    @else
                        <p class="mt-4 rounded-md bg-[#f7f9fc] p-4 text-sm text-[#6c7890]">
                            Hasil akhir akan ditampilkan setelah keputusan diumumkan.
                        </p>
                    @endif
                </section>
            @else
                <section class="mb-6 rounded-lg border border-dashed border-[#cbd5e1] bg-white p-8 text-center">
                    <h2 class="font-semibold">Belum ada pengajuan aktif</h2>
                    <p class="mt-2 text-sm text-[#6c7890]">
                        Pengajuan yang masih berjalan akan ditampilkan di halaman ini.
                    </p>
                    <a href="{{ route('mahasiswa.beasiswa.index') }}"
                       class="mt-4 inline-block rounded-md bg-[#183a70] px-4 py-2 text-sm font-semibold text-white">
                        Lihat Beasiswa
                    </a>
                </section>
            @endif


        </div> {{-- Tutup p-5 sm:p-8 --}}

    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>

</body>
</html>