<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan — SIBEST UNUJA</title>

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

    {{-- MAIN --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div>
                <p class="text-xs text-muted">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="hover:underline">Dashboard</a>
                    <span class="mx-1">›</span>
                    <span class="text-navy font-semibold">Riwayat</span>
                </p>
                <h1 class="text-xl font-extrabold text-navy mt-1">Riwayat Pengajuan</h1>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">

            @php
                $tahunList = $riwayats->map(function ($r) {
                    return \Carbon\Carbon::parse($r->submitted_at ?? $r->finalized_at)->year;
                })->unique()->sortDesc()->values();

                $filterStatus = request('status', 'semua');
                $filterTahun  = request('tahun', null);

                $filtered = $riwayats->filter(function ($r) use ($filterStatus, $filterTahun) {
                    $hasil = $r->hasil_akhir ?? $r->status;

                    if ($filterStatus !== 'semua' && $hasil !== $filterStatus) {
                        return false;
                    }

                    if ($filterTahun) {
                        $tahun = \Carbon\Carbon::parse($r->submitted_at ?? $r->finalized_at)->year;
                        if ((string) $tahun !== (string) $filterTahun) {
                            return false;
                        }
                    }

                    return true;
                })->values();
            @endphp

            <section class="bg-white rounded-xl border border-border-light shadow-sm overflow-hidden">

                {{-- FILTER BAR --}}
                <div class="p-5 border-b border-border-light flex flex-wrap items-end justify-between gap-4">

                    <div>
                        <p class="text-[10px] font-bold uppercase text-muted mb-2">Status Pengajuan</p>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @php
                                $tabs = [
                                    'semua'       => 'Semua',
                                    'lulus'       => 'Lulus',
                                    'tidak_lulus' => 'Tidak Lulus',
                                    'dibatalkan'  => 'Dibatalkan',
                                ];
                            @endphp

                            @foreach($tabs as $key => $label)
                                @php
                                    $isActive = $filterStatus === $key;
                                    $url = request()->fullUrlWithQuery(['status' => $key]);
                                @endphp

                                <a href="{{ $url }}"
                                   class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors
                                   {{ $isActive
                                       ? 'bg-navy text-white'
                                       : 'bg-white border border-border-light text-muted hover:bg-gray-50' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if($tahunList->count() > 1)
                        <div>
                            <p class="text-[10px] font-bold uppercase text-muted mb-2">Tahun</p>
                            <form method="GET" action="{{ route('mahasiswa.riwayat.index') }}">
                                <input type="hidden" name="status" value="{{ $filterStatus }}">
                                <select name="tahun"
                                        onchange="this.form.submit()"
                                        class="h-9 px-3 pr-8 rounded-md border border-border-light bg-white text-[11px] font-semibold text-navy focus:outline-none focus:border-navy">
                                    <option value="">Semua Tahun</option>
                                    @foreach($tahunList as $tahun)
                                        <option value="{{ $tahun }}" @selected((string) $filterTahun === (string) $tahun)>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif

                </div>

                {{-- KONTEN --}}
                @if($filtered->isEmpty())

                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-extrabold text-navy mt-4">Belum ada riwayat pengajuan</h3>
                        <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                            @if($filterStatus !== 'semua' || $filterTahun)
                                Tidak ada pengajuan dengan filter yang dipilih.
                            @else
                                Kamu belum memiliki pengajuan yang telah selesai.
                            @endif
                        </p>

                        @if($filterStatus !== 'semua' || $filterTahun)
                            <a href="{{ route('mahasiswa.riwayat.index') }}"
                               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy mt-4 hover:bg-gray-50">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                Reset Filter
                            </a>
                        @endif
                    </div>

                @else

                    {{-- ================= TABEL DESKTOP ================= --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full table-fixed">
                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="w-[40%] text-left px-5 py-3 text-[10px] font-bold uppercase text-muted">Beasiswa</th>
                                    <th class="w-[13%] text-left px-3 py-3 text-[10px] font-bold uppercase text-muted">Periode</th>
                                    <th class="w-[15%] text-left px-3 py-3 text-[10px] font-bold uppercase text-muted">Diajukan</th>
                                    <th class="w-[18%] text-left px-3 py-3 text-[10px] font-bold uppercase text-muted">Status / Hasil</th>
                                    <th class="w-[14%] text-right px-5 py-3 text-[10px] font-bold uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border-light">
                                @foreach($filtered as $riwayat)
                                    @php
                                        $hasil = $riwayat->hasil_akhir ?? $riwayat->status;

                                        $hasilConfig = match($hasil) {
                                            'lulus' => [
                                                'bg'    => 'bg-green-50',
                                                'text'  => 'text-green-700',
                                                'dot'   => 'bg-green-500',
                                                'label' => 'Lulus',
                                            ],
                                            'tidak_lulus' => [
                                                'bg'    => 'bg-red-50',
                                                'text'  => 'text-red-700',
                                                'dot'   => 'bg-red-500',
                                                'label' => 'Tidak Lulus',
                                            ],
                                            'dibatalkan' => [
                                                'bg'    => 'bg-gray-100',
                                                'text'  => 'text-gray-600',
                                                'dot'   => 'bg-gray-400',
                                                'label' => 'Dibatalkan',
                                            ],
                                            default => [
                                                'bg'    => 'bg-amber-50',
                                                'text'  => 'text-amber-700',
                                                'dot'   => 'bg-amber-500',
                                                'label' => ucfirst(str_replace('_', ' ', $hasil)),
                                            ],
                                        };
                                    @endphp

                                    <tr class="hover:bg-gray-50 transition-colors">

                                        {{-- Beasiswa --}}
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-navy flex items-center justify-center shrink-0">
                                                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-navy truncate">{{ $riwayat->nama_program }}</p>
                                                    <p class="text-[11px] text-muted truncate">{{ ucfirst($riwayat->jalur ?? '-') }} · {{ $riwayat->nama_kategori }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Periode --}}
                                        <td class="px-3 py-4">
                                            <p class="text-xs font-semibold text-navy truncate">{{ $riwayat->nama_periode }}</p>
                                        </td>

                                        {{-- Diajukan --}}
                                        <td class="px-3 py-4">
                                            <div class="flex items-center gap-1.5">
                                                <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-muted shrink-0"></i>
                                                <p class="text-xs font-semibold text-navy whitespace-nowrap">
                                                    {{ $riwayat->submitted_at
                                                        ? \Carbon\Carbon::parse($riwayat->submitted_at)->translatedFormat('d M Y')
                                                        : '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-3 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $hasilConfig['bg'] }} {{ $hasilConfig['text'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $hasilConfig['dot'] }}"></span>
                                                {{ $hasilConfig['label'] }}
                                            </span>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-5 py-4 text-right">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-muted">
                                                <i data-lucide="check" class="w-3.5 h-3.5 text-green-600"></i>
                                                Selesai
                                            </span>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ================= LIST MOBILE ================= --}}
                    <div class="md:hidden divide-y divide-border-light">
                        @foreach($filtered as $riwayat)
                            @php
                                $hasil = $riwayat->hasil_akhir ?? $riwayat->status;

                                $hasilConfig = match($hasil) {
                                    'lulus' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'dot' => 'bg-green-500', 'label' => 'Lulus'],
                                    'tidak_lulus' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'Tidak Lulus'],
                                    'dibatalkan' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => 'Dibatalkan'],
                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => ucfirst(str_replace('_', ' ', $hasil))],
                                };
                            @endphp

                            <div class="p-4 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-navy flex items-center justify-center shrink-0">
                                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-navy">{{ $riwayat->nama_program }}</p>
                                        <p class="text-[11px] text-muted mt-0.5">{{ ucfirst($riwayat->jalur ?? '-') }} · {{ $riwayat->nama_periode }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-border-light">
                                    <div class="flex items-center gap-1.5 text-xs text-muted">
                                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                                        {{ $riwayat->submitted_at
                                            ? \Carbon\Carbon::parse($riwayat->submitted_at)->translatedFormat('d M Y')
                                            : '-' }}
                                    </div>

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $hasilConfig['bg'] }} {{ $hasilConfig['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $hasilConfig['dot'] }}"></span>
                                        {{ $hasilConfig['label'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- FOOTER --}}
                    <div class="px-5 py-3 bg-gray-50 border-t border-border-light">
                        <p class="text-[11px] text-muted">
                            Menampilkan {{ $filtered->count() }} dari {{ $riwayats->count() }} pengajuan
                        </p>
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