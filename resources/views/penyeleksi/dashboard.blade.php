<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyeleksi — SIBEST UNUJA</title>

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

    {{-- SIDEBAR PENYELEKSI --}}
    @include('partials.sidebar-penyeleksi')

    {{-- MAIN --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div>
                    <h1 class="text-xl font-extrabold text-navy">Dashboard</h1>
                    <p class="text-xs text-muted mt-1">Ringkasan tugas penyeleksian</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if($perluDireview > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500"></span>
                    @endif
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">
                    <div class="w-9 h-9 rounded-full bg-gold text-navy flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 space-y-6">

            {{-- WELCOME --}}
            <div>
                <h2 class="text-2xl font-extrabold text-navy">Selamat datang, {{ $user->nama }} 👋</h2>
                <p class="text-sm text-muted mt-1">
                    @if($totalOffering > 0)
                        Anda ditugaskan sebagai penyeleksi untuk {{ $totalOffering }} offering aktif
                    @else
                        Belum ada offering yang ditugaskan kepada Anda
                    @endif
                </p>
            </div>

            {{-- KPI CARDS --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- KPI 1: Offering Ditugaskan --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                            <i data-lucide="briefcase-business" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-muted mt-4">Offering Ditugaskan</p>
                    <p class="text-3xl font-extrabold text-navy mt-1">{{ $totalOffering }}</p>
                </div>

                {{-- KPI 2: Perlu Direview --}}
                <div class="bg-white rounded-xl border border-amber-200 p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        @if($perluDireview > 0)
                            <span class="text-xs font-semibold text-amber-600">Butuh aksi</span>
                        @endif
                    </div>
                    <p class="text-xs text-muted mt-4">Perlu Direview</p>
                    <p class="text-3xl font-extrabold text-amber-700 mt-1">{{ $perluDireview }}</p>
                </div>

                {{-- KPI 3: Sudah Direview --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-green-50 text-green-700 flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-muted mt-4">Sudah Direview</p>
                    <p class="text-3xl font-extrabold text-green-700 mt-1">{{ $sudahDireview }}</p>
                </div>

            </section>

            {{-- OFFERING YANG DITUGASKAN --}}
            @if($offerings->isNotEmpty())
                <section>
                    <h3 class="font-extrabold text-navy mb-4">Offering yang Ditugaskan</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($offerings as $off)
                            <div class="bg-white rounded-xl border border-border-light p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-bold text-navy text-lg truncate">{{ $off->nama_program }}</p>
                                        <p class="text-xs text-muted mt-0.5">Periode akademik {{ $off->nama_periode }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-6 mt-4 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="text-muted">Total pengajuan</span>
                                        <span class="font-bold text-navy">{{ $off->total_pengajuan }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-muted">Menunggu review</span>
                                        <span class="font-bold {{ $off->menunggu_review > 0 ? 'text-amber-700' : 'text-navy' }}">
                                            {{ $off->menunggu_review }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                @if($off->total_pengajuan > 0)
                                    @php
                                        $persen = round(($off->menunggu_review / $off->total_pengajuan) * 100);
                                    @endphp
                                    <div class="mt-4">
                                        <p class="text-xs text-muted mb-2">
                                            {{ $off->menunggu_review }} dari {{ $off->total_pengajuan }} pengajuan menunggu review
                                        </p>
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gold rounded-full" style="width: {{ $persen }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 pt-4 border-t border-border-light flex justify-end">
                                    <a href="{{ route('penyeleksi.pengajuan.index', ['offering' => $off->id]) }}"
                                       class="inline-flex items-center gap-1.5 h-9 px-3 rounded-md bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                                        Lihat Pengajuan
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @else
                {{-- Empty State Offering --}}
                <section class="bg-white rounded-xl border border-border-light p-8 text-center">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                        <i data-lucide="briefcase-business" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Belum ada offering yang ditugaskan</h3>
                    <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                        Admin belum menugaskan Anda ke offering manapun. Hubungi admin untuk informasi lebih lanjut.
                    </p>
                </section>
            @endif

            {{-- TABEL: PENGAJUAN PERLU DIREVIEW --}}
            <section class="bg-white rounded-xl border border-border-light overflow-hidden">

                <div class="p-5 border-b border-border-light flex items-center justify-between">
                    <h3 class="font-extrabold text-navy">Pengajuan Perlu Direview</h3>
                    @if($pengajuanPerlu->isNotEmpty())
                        <a href="{{ route('penyeleksi.pengajuan.index') }}"
                           class="text-xs font-bold text-navy hover:underline inline-flex items-center gap-1">
                            Lihat Semua
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>

                @if($pengajuanPerlu->isEmpty())

                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-700 flex items-center justify-center mx-auto">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <p class="font-bold text-navy mt-4">Tidak ada pengajuan yang perlu direview</p>
                        <p class="text-sm text-muted mt-1">Semua pengajuan sudah selesai direview. Kerja bagus! 🎉</p>
                    </div>

                @else

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase text-muted">Nama Mahasiswa</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Offering</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Tahap</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Status</th>
                                    <th class="text-right px-6 py-3 text-[11px] font-bold uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-light">
                                @foreach($pengajuanPerlu as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-navy">{{ $row->nama_mahasiswa }}</p>
                                            <p class="text-[11px] text-muted">{{ $row->nim_mahasiswa }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-sm text-navy font-medium">{{ $row->nama_program }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-xs text-muted">{{ $row->tahap_aktif ?? '-' }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 text-[11px] font-bold">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                Menunggu
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md border border-border-light text-[11px] font-bold text-navy hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile List --}}
                    <div class="md:hidden divide-y divide-border-light">
                        @foreach($pengajuanPerlu as $row)
                            <div class="p-4 space-y-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-navy">{{ $row->nama_mahasiswa }}</p>
                                        <p class="text-[11px] text-muted">{{ $row->nim_mahasiswa }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-amber-50 text-amber-700 text-[10px] font-bold shrink-0">
                                        Menunggu
                                    </span>
                                </div>
                                <p class="text-xs text-muted">{{ $row->nama_program }} · {{ $row->tahap_aktif ?? '-' }}</p>
                                <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                   class="w-full inline-flex items-center justify-center gap-1.5 h-9 rounded-md border border-border-light text-xs font-bold text-navy hover:bg-gray-50">
                                    Review
                                </a>
                            </div>
                        @endforeach
                    </div>

                @endif
            </section>

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