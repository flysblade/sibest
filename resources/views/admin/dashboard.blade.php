<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SIBEST UNUJA</title>

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

    {{-- SIDEBAR ADMIN --}}
    @include('partials.sidebar-admin')

    {{-- MAIN --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- TOPBAR --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-navy">Dashboard</h1>
                <p class="text-xs text-muted mt-1">Ringkasan pengelolaan beasiswa</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if($perluVerifikasi > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500"></span>
                    @endif
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">
                    <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8 space-y-6">

            {{-- ============ STATISTIK CARDS ============ --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Total Offering --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-muted mt-4">Total Beasiswa</p>
                    <p class="text-3xl font-extrabold text-navy mt-1">{{ $offeringTotal }}</p>
                </div>

                {{-- Offering Aktif --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-green-50 text-green-700 flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-semibold text-green-600">Aktif</span>
                    </div>
                    <p class="text-xs text-muted mt-4">Offering Aktif</p>
                    <p class="text-3xl font-extrabold text-navy mt-1">{{ $offeringPublished }}</p>
                </div>

                {{-- Total Pengajuan --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-muted mt-4">Total Pengajuan</p>
                    <p class="text-3xl font-extrabold text-navy mt-1">{{ $totalPengajuan }}</p>
                </div>

                {{-- Perlu Verifikasi --}}
                <a href="{{ route('admin.pengajuan.index', ['status' => 'diajukan']) }}"
                   class="bg-white rounded-xl border border-amber-200 p-5 hover:bg-amber-50/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        </div>
                        @if($perluVerifikasi > 0)
                            <span class="text-xs font-semibold text-amber-600">Butuh aksi</span>
                        @endif
                    </div>
                    <p class="text-xs text-muted mt-4">Menunggu Verifikasi</p>
                    <p class="text-3xl font-extrabold text-amber-700 mt-1">{{ $perluVerifikasi }}</p>
                </a>

            </section>

            {{-- ============ PENGAJUAN TERBARU ============ --}}
            <section class="bg-white rounded-xl border border-border-light overflow-hidden">

                <div class="p-5 border-b border-border-light flex items-center justify-between">
                    <h3 class="font-extrabold text-navy">Pengajuan Terbaru</h3>
                    <a href="{{ route('admin.pengajuan.index') }}"
                       class="text-xs font-bold text-navy hover:underline inline-flex items-center gap-1">
                        Lihat Semua
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if($pengajuanTerbaru->isEmpty())
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <p class="font-bold text-navy mt-4">Belum ada pengajuan</p>
                        <p class="text-sm text-muted mt-1">Pengajuan mahasiswa akan muncul di sini.</p>
                    </div>
                @else
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="text-left px-6 py-3 text-[11px] font-bold uppercase text-muted">Nama Mahasiswa</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Beasiswa</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Periode</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Tanggal</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Status</th>
                                    <th class="text-right px-6 py-3 text-[11px] font-bold uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-light">
                                @foreach($pengajuanTerbaru as $row)
                                    @php
                                        $statusConfig = match($row->status) {
                                            'draft'    => ['bg' => 'bg-gray-100',    'text' => 'text-gray-600',    'label' => 'Draft'],
                                            'diajukan' => ['bg' => 'bg-blue-50',     'text' => 'text-blue-700',    'label' => 'Diajukan'],
                                            'diproses' => ['bg' => 'bg-amber-50',    'text' => 'text-amber-700',   'label' => 'Diproses'],
                                            'ditolak'  => ['bg' => 'bg-red-50',      'text' => 'text-red-700',     'label' => 'Ditolak'],
                                            'selesai'  => ['bg' => 'bg-green-50',    'text' => 'text-green-700',   'label' => 'Selesai'],
                                            default    => ['bg' => 'bg-gray-100',    'text' => 'text-gray-600',    'label' => ucfirst($row->status)],
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <p class="text-sm font-bold text-navy">{{ $row->nama_mahasiswa }}</p>
                                            <p class="text-[11px] text-muted">{{ $row->nim_mahasiswa }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-sm text-navy font-medium">{{ $row->nama_program }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-xs text-muted">{{ $row->nama_periode }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-xs text-muted">
                                                {{ \Carbon\Carbon::parse($row->submitted_at ?? $row->created_at)->translatedFormat('d M Y') }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('admin.pengajuan.show', $row->id) }}"
                                               class="inline-flex items-center gap-1 h-8 px-3 rounded-md border border-border-light text-[11px] font-bold text-navy hover:bg-gray-50">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile List --}}
                    <div class="md:hidden divide-y divide-border-light">
                        @foreach($pengajuanTerbaru as $row)
                            @php
                                $statusConfig = match($row->status) {
                                    'draft'    => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'Draft'],
                                    'diajukan' => ['bg' => 'bg-blue-50',  'text' => 'text-blue-700', 'label' => 'Diajukan'],
                                    'diproses' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700','label' => 'Diproses'],
                                    'ditolak'  => ['bg' => 'bg-red-50',   'text' => 'text-red-700',  'label' => 'Ditolak'],
                                    'selesai'  => ['bg' => 'bg-green-50', 'text' => 'text-green-700','label' => 'Selesai'],
                                    default    => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => ucfirst($row->status)],
                                };
                            @endphp
                            <div class="p-4 space-y-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-navy">{{ $row->nama_mahasiswa }}</p>
                                        <p class="text-[11px] text-muted">{{ $row->nim_mahasiswa }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} shrink-0">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <p class="text-muted">{{ $row->nama_program }}</p>
                                    <p class="text-muted">{{ \Carbon\Carbon::parse($row->submitted_at ?? $row->created_at)->translatedFormat('d M Y') }}</p>
                                </div>
                                <a href="{{ route('admin.pengajuan.show', $row->id) }}"
                                   class="w-full inline-flex items-center justify-center gap-1 h-9 rounded-md border border-border-light text-xs font-bold text-navy hover:bg-gray-50">
                                    Lihat Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- ============ OFFERING AKTIF ============ --}}
            @if($offeringAktif->isNotEmpty())
                <section>
                    <h3 class="font-extrabold text-navy mb-4">Offering Aktif</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($offeringAktif as $off)
                            <div class="bg-white rounded-xl border border-border-light p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-navy flex items-center justify-center shrink-0">
                                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-navy truncate">{{ $off->nama_program }}</p>
                                            <p class="text-[11px] text-muted truncate">{{ $off->nama_periode }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                </div>

                                <div class="mt-4 space-y-2 text-xs text-muted">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span><strong class="text-navy">{{ $off->jumlah_pengajuan }}</strong> pengajuan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                                        <span>Batas akhir:
                                            <strong class="text-navy">
                                                {{ $off->tanggal_tutup
                                                    ? \Carbon\Carbon::parse($off->tanggal_tutup)->translatedFormat('d M Y')
                                                    : 'Belum ditentukan' }}
                                            </strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-border-light flex justify-end">
                                    <a href="{{ route('admin.pengajuan.index', ['offering' => $off->id]) }}"
                                       class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md bg-navy text-white text-[11px] font-bold hover:bg-navy-dark">
                                        Kelola
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
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