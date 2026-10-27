<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beasiswa Saya — SIBEST Penyeleksi</title>

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

    @include('partials.sidebar-penyeleksi')

    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div>
                    <h1 class="text-xl font-extrabold text-navy">Beasiswa Saya</h1>
                    <p class="text-xs text-muted mt-1">Offering beasiswa yang Anda tangani sebagai penyeleksi</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">
                    <div class="w-9 h-9 rounded-full bg-gold text-navy flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 space-y-5">

            @if($offerings->isEmpty())

                <div class="bg-white rounded-xl border border-border-light p-12 text-center">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                        <i data-lucide="briefcase-business" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Belum ada offering yang ditugaskan</h3>
                    <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                        Admin belum menugaskan Anda ke offering manapun. Hubungi admin untuk informasi lebih lanjut.
                    </p>
                </div>

            @else

                @foreach($offerings as $off)

                    <div class="bg-white rounded-xl border border-border-light shadow-sm overflow-hidden">

                        {{-- Header Card --}}
                        <div class="p-5 border-b border-border-light">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-lg font-extrabold text-navy">{{ $off->nama_program }}</h2>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        Published
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-muted">Periode</p>
                                    <p class="text-sm font-medium text-navy mt-0.5">{{ $off->nama_periode }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-muted">Deadline</p>
                                    <p class="text-sm font-medium text-navy mt-0.5">
                                        {{ $off->tanggal_tutup
                                            ? \Carbon\Carbon::parse($off->tanggal_tutup)->translatedFormat('d M Y')
                                            : 'Belum ditentukan' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-muted">Jalur</p>
                                    <p class="text-sm font-medium text-navy mt-0.5">{{ ucfirst($off->jalur) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Statistik --}}
                        <div class="grid grid-cols-3 border-b border-border-light">
                            <div class="p-4 text-center border-r border-border-light">
                                <p class="text-[11px] text-muted">Total Pengajuan</p>
                                <p class="text-2xl font-extrabold text-navy mt-1">{{ $off->total_pengajuan }}</p>
                            </div>
                            <div class="p-4 text-center border-r border-border-light">
                                <p class="text-[11px] text-muted">Menunggu Review</p>
                                <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $off->menunggu_review }}</p>
                            </div>
                            <div class="p-4 text-center">
                                <p class="text-[11px] text-muted">Selesai Direview</p>
                                <p class="text-2xl font-extrabold text-green-700 mt-1">{{ $off->selesai_review }}</p>
                            </div>
                        </div>

                        {{-- Stepper Tahapan --}}
                        @if($off->tahapan->isNotEmpty())
                            <div class="p-5">
                                <p class="text-[11px] font-bold uppercase text-muted mb-4">Tahapan Seleksi</p>

                                <div class="flex items-start">
                                    @foreach($off->tahapan as $tahap)
                                        @php
                                            // Tentukan status visual per tahap
                                            if ($tahap->sudah_dinilai > 0 && $tahap->sudah_dinilai >= $off->total_pengajuan) {
                                                // Semua pengajuan udah dinilai di tahap ini
                                                $statusTahap = 'selesai';
                                            } elseif ($tahap->sudah_dinilai > 0) {
                                                // Sebagian udah dinilai
                                                $statusTahap = 'berlangsung';
                                            } else {
                                                $statusTahap = 'belum';
                                            }
                                        @endphp

                                        {{-- Marker --}}
                                        <div class="flex-1 flex flex-col items-center relative">
                                            {{-- Garis horizontal (kecuali terakhir) --}}
                                            @if(!$loop->last)
                                                <div class="absolute top-4 left-1/2 w-full h-0.5
                                                    {{ $statusTahap === 'selesai' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                            @endif

                                            {{-- Marker bulat --}}
                                            <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                                @if($statusTahap === 'selesai')
                                                    bg-green-500 text-white
                                                @elseif($statusTahap === 'berlangsung')
                                                    bg-navy text-white
                                                @else
                                                    bg-gray-100 text-gray-400 border border-gray-200
                                                @endif">
                                                @if($statusTahap === 'selesai')
                                                    <i data-lucide="check" class="w-4 h-4"></i>
                                                @else
                                                    {{ $tahap->urutan }}
                                                @endif
                                            </div>

                                            {{-- Label --}}
                                            <div class="mt-2 text-center max-w-[120px]">
                                                <p class="text-xs font-bold text-navy">{{ $tahap->nama_tahapan }}</p>
                                                <p class="text-[10px] mt-0.5
                                                    @if($statusTahap === 'selesai') text-green-600
                                                    @elseif($statusTahap === 'berlangsung') text-navy font-semibold
                                                    @else text-muted
                                                    @endif">
                                                    @if($statusTahap === 'selesai')
                                                        Selesai
                                                    @elseif($statusTahap === 'berlangsung')
                                                        Berlangsung ({{ $tahap->sudah_dinilai }}/{{ $off->total_pengajuan }})
                                                    @else
                                                        Belum dimulai
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Footer Aksi --}}
                        <div class="p-5 border-t border-border-light bg-gray-50 flex justify-end">
                            <a href="{{ route('penyeleksi.pengajuan.index', ['offering' => $off->id]) }}"
                               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                                Lihat Daftar Pengajuan
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>

                    </div>

                @endforeach

            @endif

        </div>
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