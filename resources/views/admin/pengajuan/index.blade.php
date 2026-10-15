<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan — SIBEST Admin</title>

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
                <p class="text-xs text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                    <span class="mx-1">›</span>
                    <span class="text-navy font-semibold">Pengajuan</span>
                </p>
                <h1 class="text-xl font-extrabold text-navy mt-1">Kelola Pengajuan</h1>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">
                    <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('admin.pengajuan.index') }}"
                  class="bg-white rounded-xl border border-border-light p-5 mb-5">
                <div class="flex flex-wrap items-end gap-4">

                    {{-- Offering --}}
                    <div class="w-full sm:w-[200px]">
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

                    {{-- Status --}}
                    <div class="w-full sm:w-[160px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Status</label>
                        <select name="status"
                                onchange="this.form.submit()"
                                class="w-full h-10 px-3 rounded-md border border-border-light bg-white text-sm text-navy focus:outline-none focus:border-navy">
                            <option value="semua" @selected(request('status', 'semua') === 'semua')>Semua</option>
                            <option value="draft"    @selected(request('status') === 'draft')>Draft</option>
                            <option value="diajukan" @selected(request('status') === 'diajukan')>Diajukan</option>
                            <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
                            <option value="ditolak"  @selected(request('status') === 'ditolak')>Ditolak</option>
                            <option value="selesai"  @selected(request('status') === 'selesai')>Selesai</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="flex-1 min-w-[240px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Cari</label>
                        <div class="h-10 flex items-center gap-2 px-3 rounded-md border border-border-light bg-white">
                            <i data-lucide="search" class="w-4 h-4 text-muted shrink-0"></i>
                            <input type="text" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama / NIM..."
                                   class="flex-1 outline-none text-sm text-navy placeholder:text-muted">
                        </div>
                    </div>

                    {{-- Tombol Reset --}}
                    @if(request()->anyFilled(['offering', 'status', 'search']))
                        <div>
                            <a href="{{ route('admin.pengajuan.index') }}"
                               class="inline-flex items-center gap-1.5 h-10 px-3 rounded-md border border-border-light bg-white text-xs font-bold text-muted hover:bg-gray-50">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                Reset
                            </a>
                        </div>
                    @endif

                </div>
            </form>

            {{-- INFO JUMLAH --}}
            <div class="text-xs font-semibold text-muted mb-3">
                Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
                dari {{ $pengajuans->total() }} pengajuan
            </div>

            {{-- TABEL --}}
            @if($pengajuans->isEmpty())

                <div class="bg-white rounded-xl border border-border-light p-12 text-center">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                        <i data-lucide="inbox" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Tidak ada pengajuan</h3>
                    <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                        @if(request()->anyFilled(['offering', 'status', 'search']))
                            Tidak ada pengajuan yang sesuai filter.
                        @else
                            Belum ada mahasiswa yang mengajukan beasiswa.
                        @endif
                    </p>
                    @if(request()->anyFilled(['offering', 'status', 'search']))
                        <a href="{{ route('admin.pengajuan.index') }}"
                           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy mt-4 hover:bg-gray-50">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Reset Filter
                        </a>
                    @endif
                </div>

            @else

                <div class="bg-white rounded-xl border border-border-light overflow-hidden">

                    {{-- Scroll horizontal (mobile + tablet, sesuai catatan Figma) --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1100px]">

                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="text-left px-5 py-3 text-[11px] font-bold uppercase text-muted w-12">No</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[180px]">Nama</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[130px]">NIM</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[190px]">Beasiswa</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[140px]">Tgl Pengajuan</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[150px]">Status</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted w-[100px]">Dokumen</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-bold uppercase text-muted w-[140px]">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border-light">
                                @foreach($pengajuans as $index => $row)
                                    @php
                                        $statusConfig = match($row->status) {
                                            'draft'    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'label' => 'Draft'],
                                            'diajukan' => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'label' => 'Diajukan'],
                                            'diproses' => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',  'label' => 'Diproses'],
                                            'ditolak'  => ['bg' => 'bg-red-50',     'text' => 'text-red-700',    'label' => 'Ditolak'],
                                            'selesai'  => ['bg' => 'bg-green-50',   'text' => 'text-green-700',  'label' => 'Selesai'],
                                            default    => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'label' => ucfirst($row->status)],
                                        };

                                        // Dokumen: verified/wajib
                                        $docOk = ($row->dokumen_verified === $row->dokumen_wajib) && $row->dokumen_wajib > 0;
                                        $docWarn = !$docOk && $row->dokumen_wajib > 0;
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        {{-- No --}}
                                        <td class="px-5 py-4 text-sm text-muted">
                                            {{ $pengajuans->firstItem() + $index }}
                                        </td>

                                        {{-- Nama --}}
                                        <td class="px-3 py-4">
                                            <p class="text-sm font-bold text-navy truncate">{{ $row->nama_mahasiswa }}</p>
                                            <p class="text-[11px] text-muted truncate">{{ $row->email_mahasiswa }}</p>
                                        </td>

                                        {{-- NIM --}}
                                        <td class="px-3 py-4">
                                            <p class="text-sm text-dark-text font-medium">{{ $row->nim_mahasiswa }}</p>
                                        </td>

                                        {{-- Beasiswa --}}
                                        <td class="px-3 py-4">
                                            <p class="text-sm text-navy font-medium">{{ $row->nama_program }}</p>
                                            <p class="text-[11px] text-muted">{{ $row->nama_periode }}</p>
                                        </td>

                                        {{-- Tgl Pengajuan --}}
                                        <td class="px-3 py-4">
                                            <p class="text-xs text-muted">
                                                {{ $row->submitted_at
                                                    ? \Carbon\Carbon::parse($row->submitted_at)->translatedFormat('d M Y')
                                                    : \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}
                                            </p>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-3 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>

                                        {{-- Dokumen --}}
                                        <td class="px-3 py-4">
                                            @if($row->dokumen_wajib > 0)
                                                <div class="flex items-center gap-1.5 {{ $docOk ? 'text-green-700' : 'text-amber-700' }}">
                                                    <i data-lucide="{{ $docOk ? 'circle-check' : 'triangle-alert' }}" class="w-3.5 h-3.5 shrink-0"></i>
                                                    <span class="text-xs font-bold">
                                                        {{ $row->dokumen_verified }}/{{ $row->dokumen_wajib }}
                                                    </span>
                                                </div>
                                            @else
                                                <p class="text-xs text-muted">—</p>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('admin.pengajuan.show', $row->id) }}"
                                               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md border border-border-light text-[11px] font-bold text-navy hover:bg-gray-50">
                                                <i data-lucide="panel-right" class="w-3.5 h-3.5"></i>
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if($pengajuans->hasPages())
                        <div class="px-5 py-4 border-t border-border-light bg-gray-50">
                            {{ $pengajuans->links() }}
                        </div>
                    @endif

                </div>

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