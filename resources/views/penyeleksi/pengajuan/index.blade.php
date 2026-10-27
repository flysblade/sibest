<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajuan — SIBEST Penyeleksi</title>

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
                    <p class="text-xs text-muted">
                        <a href="{{ route('penyeleksi.dashboard') }}" class="hover:underline">Dashboard</a>
                        <span class="mx-1">›</span>
                        <span class="text-navy font-semibold">Daftar Pengajuan</span>
                    </p>
                    <h1 class="text-xl font-extrabold text-navy mt-1">Daftar Pengajuan</h1>
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

        <div class="p-5 sm:p-8">

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('penyeleksi.pengajuan.index') }}"
                class="bg-white rounded-xl border border-border-light p-5 mb-5">
                <div class="flex flex-wrap items-end gap-4">

                    {{-- Offering --}}
                    <div class="w-full sm:w-[220px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Offering</label>
                        <select name="offering"
                                onchange="this.form.submit()"
                                class="w-full h-10 px-3 rounded-md border border-border-light bg-white text-sm text-navy focus:outline-none focus:border-navy">
                            <option value="">Semua Offering</option>
                            @foreach($offerings as $off)
                                <option value="{{ $off->id }}" @selected(request('offering') == $off->id)>
                                    {{ $off->nama_program }} {{ $off->nama_periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tahapan --}}
                    <div class="w-full sm:w-[180px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Tahapan</label>
                        <select name="tahapan"
                                onchange="this.form.submit()"
                                class="w-full h-10 px-3 rounded-md border border-border-light bg-white text-sm text-navy focus:outline-none focus:border-navy">
                            <option value="">Semua</option>
                            @foreach($tahapanList as $tahap)
                                <option value="{{ $tahap->nama_tahapan }}" @selected(request('tahapan') === $tahap->nama_tahapan)>
                                    {{ $tahap->nama_tahapan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="w-full sm:w-[160px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Status</label>
                        <select name="status"
                                onchange="this.form.submit()"
                                class="w-full h-10 px-3 rounded-md border border-border-light bg-white text-sm text-navy focus:outline-none focus:border-navy">
                            <option value="semua" @selected(request('status', 'semua') === 'semua')>Semua</option>
                            <option value="diajukan" @selected(request('status') === 'diajukan')>Diajukan</option>
                            <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
                            <option value="selesai"  @selected(request('status') === 'selesai')>Selesai</option>
                            <option value="ditolak"  @selected(request('status') === 'ditolak')>Ditolak</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Cari</label>
                        <div class="h-10 flex items-center gap-2 px-3 rounded-md border border-border-light bg-white">
                            <i data-lucide="search" class="w-4 h-4 text-muted shrink-0"></i>
                            <input type="text" name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama / NIM / No. Pendaftaran..."
                                class="flex-1 outline-none text-sm text-navy placeholder:text-muted">
                        </div>
                    </div>

                    {{-- Reset --}}
                    @if(request()->anyFilled(['offering', 'tahapan', 'status', 'search']))
                        <div>
                            <a href="{{ route('penyeleksi.pengajuan.index') }}"
                            class="inline-flex items-center gap-1.5 h-10 px-3 rounded-md border border-border-light bg-white text-xs font-bold text-muted hover:bg-gray-50">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                Reset
                            </a>
                        </div>
                    @endif

                </div>
            </form>

            {{-- INFO --}}
            <div class="text-xs font-semibold text-muted mb-3">
                Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
                dari {{ $pengajuans->total() }} pengajuan yang perlu direview
            </div>

            {{-- TABEL --}}
            @if($pengajuans->isEmpty())

                <div class="bg-white rounded-xl border border-border-light p-12 text-center">
                    <div class="w-14 h-14 rounded-xl bg-green-50 text-green-700 flex items-center justify-center mx-auto">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Tidak ada pengajuan yang perlu direview</h3>
                    <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                        @if(request()->anyFilled(['offering', 'search']))
                            Tidak ada pengajuan yang sesuai filter.
                        @else
                            Belum ada pengajuan yang dapat direview saat ini.
                        @endif
                    </p>
                    @if(request()->anyFilled(['offering', 'search']))
                        <a href="{{ route('penyeleksi.pengajuan.index') }}"
                           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy mt-4 hover:bg-gray-50">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Reset Filter
                        </a>
                    @endif
                </div>

            @else

                <div class="bg-white rounded-xl border border-border-light overflow-hidden">

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full min-w-[1000px]">
                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="text-left px-5 py-3 text-[11px] font-bold uppercase text-muted w-12">No</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[200px]">Nama Mahasiswa</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[130px]">NIM / No. Pendaftaran</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[190px]">Offering</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[140px]">Tgl Pengajuan</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[160px]">Progress Tahapan</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-bold uppercase text-muted w-[120px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-light">
                                @foreach($pengajuans as $index => $row)
                                    @php
                                        $totalTahap   = $row->total_tahapan ?? 0;
                                        $selesaiTahap = $row->tahapan_selesai ?? 0;
                                        $progress = $totalTahap > 0 ? round(($selesaiTahap / $totalTahap) * 100) : 0;
                                        $isDone = ($totalTahap > 0 && $selesaiTahap >= $totalTahap);
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-4 text-sm text-muted">
                                            {{ $pengajuans->firstItem() + $index }}
                                        </td>

                                        <td class="px-3 py-4">
                                            <p class="text-sm font-bold text-navy truncate">{{ $row->nama_mahasiswa }}</p>
                                        </td>

                                        <td class="px-3 py-4">
                                            <p class="text-sm text-dark-text font-medium">{{ $row->nim_mahasiswa }}</p>
                                        </td>

                                        <td class="px-3 py-4">
                                            <p class="text-sm text-navy font-medium">{{ $row->nama_program }}</p>
                                            <p class="text-[11px] text-muted">{{ $row->nama_periode }}</p>
                                        </td>

                                        <td class="px-3 py-4">
                                            <p class="text-xs text-muted">
                                                {{ $row->submitted_at
                                                    ? \Carbon\Carbon::parse($row->submitted_at)->translatedFormat('d M Y')
                                                    : \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}
                                            </p>
                                        </td>

                                        <td class="px-3 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold {{ $isDone ? 'text-green-700' : 'text-amber-700' }}">
                                                    {{ $selesaiTahap }}/{{ $totalTahap }}
                                                </span>
                                                <span class="text-[10px] text-muted">dinilai</span>
                                            </div>
                                            <div class="mt-1.5 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden max-w-[100px]">
                                                <div class="h-full {{ $isDone ? 'bg-green-500' : 'bg-gold' }} rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            @if($row->status === 'diajukan')
                                                <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md bg-blue-600 text-white text-[11px] font-bold hover:bg-blue-700">
                                                    <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
                                                    Verifikasi
                                                </a>
                                            @elseif($row->status === 'diproses')
                                                <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md bg-navy text-white text-[11px] font-bold hover:bg-navy-dark">
                                                    Review
                                                </a>
                                            @else
                                                <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md border border-border-light text-[11px] font-bold text-navy hover:bg-gray-50">
                                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                    Lihat
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile List --}}
                    <div class="md:hidden divide-y divide-border-light">
                        @foreach($pengajuans as $row)
                            @php
                                $totalTahap   = $row->total_tahapan ?? 0;
                                $selesaiTahap = $row->tahapan_selesai ?? 0;
                                $progress = $totalTahap > 0 ? round(($selesaiTahap / $totalTahap) * 100) : 0;
                                $isDone = ($totalTahap > 0 && $selesaiTahap >= $totalTahap);
                            @endphp

                            <div class="p-4 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-navy">{{ $row->nama_mahasiswa }}</p>
                                        <p class="text-[11px] text-muted">{{ $row->nim_mahasiswa }}</p>
                                    </div>
                                    <span class="text-[10px] font-bold {{ $isDone ? 'text-green-700 bg-green-50' : 'text-amber-700 bg-amber-50' }} px-2 py-1 rounded-md shrink-0">
                                        {{ $selesaiTahap }}/{{ $totalTahap }}
                                    </span>
                                </div>

                                <p class="text-xs text-muted">{{ $row->nama_program }} · {{ $row->nama_periode }}</p>

                                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $isDone ? 'bg-green-500' : 'bg-gold' }} rounded-full" style="width: {{ $progress }}%"></div>
                                </div>

                                @if($row->status === 'diajukan')
                                   <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                      class="w-full inline-flex items-center justify-center gap-1.5 h-9 rounded-md bg-blue-600 text-white text-xs font-bold hover:bg-blue-700">
                                       <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
                                       Verifikasi
                                   </a>
                                @elseif($row->status === 'diproses')
                                   <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                      class="w-full inline-flex items-center justify-center gap-1.5 h-9 rounded-md bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                                       Review
                                   </a>
                                @else
                                   <a href="{{ route('penyeleksi.pengajuan.show', $row->id) }}"
                                      class="w-full inline-flex items-center justify-center gap-1.5 h-9 rounded-md border border-border-light text-navy text-xs font-bold hover:bg-gray-50">
                                       <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                       Lihat
                                   </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($pengajuans->hasPages())
                        <div class="px-5 py-4 border-t border-border-light bg-gray-50">
                            {{ $pengajuans->links() }}
                        </div>
                    @endif

                </div>

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