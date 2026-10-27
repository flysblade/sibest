<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman — SIBEST UNUJA</title>

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
                    <span class="text-navy font-semibold">Pengumuman</span>
                </p>
                <h1 class="text-xl font-extrabold text-navy mt-1">Pengumuman</h1>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">

            @php
                $filterProgram = request('program', 'semua');
                $filterSearch  = request('search', '');

                // Hitung jumlah pengumuman yang "baru" (7 hari)
                $jumlahBaru = $pengumumans->where('is_baru', true)->count();
            @endphp

            {{-- FILTER BAR --}}
            <section class="bg-white rounded-xl border border-border-light shadow-sm mb-5 p-5">
                <div class="flex flex-wrap items-end justify-between gap-4">

                    {{-- Tab Program --}}
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase text-muted mb-2">Beasiswa</p>
                        <div class="flex flex-wrap items-center gap-1.5">

                            @php
                                $isSemua = $filterProgram === 'semua';
                                $urlSemua = request()->fullUrlWithQuery(['program' => 'semua']);
                            @endphp

                            <a href="{{ $urlSemua }}"
                               class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors
                               {{ $isSemua
                                   ? 'bg-navy text-white'
                                   : 'bg-white border border-border-light text-muted hover:bg-gray-50' }}">
                                Semua
                            </a>

                            @foreach($programs as $prog)
                                @php
                                    $isActive = (string) $filterProgram === (string) $prog->id;
                                    $url = request()->fullUrlWithQuery(['program' => $prog->id]);
                                @endphp

                                <a href="{{ $url }}"
                                   class="px-3 py-1.5 rounded-md text-[11px] font-bold transition-colors
                                   {{ $isActive
                                       ? 'bg-navy text-white'
                                       : 'bg-white border border-border-light text-muted hover:bg-gray-50' }}">
                                    {{ $prog->nama }}
                                </a>
                            @endforeach

                        </div>
                    </div>

                    {{-- Search --}}
                    <form method="GET" action="{{ route('mahasiswa.pengumuman.index') }}"
                          class="w-full lg:w-[300px]">
                        @if($filterProgram !== 'semua')
                            <input type="hidden" name="program" value="{{ $filterProgram }}">
                        @endif

                        <p class="text-[10px] font-bold uppercase text-muted mb-2">Cari Pengumuman</p>
                        <div class="h-9 bg-white border border-border-light rounded-md flex items-center px-3 gap-2">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-muted shrink-0"></i>
                            <input type="text" name="search"
                                   value="{{ $filterSearch }}"
                                   placeholder="Cari pengumuman..."
                                   class="flex-1 outline-none text-xs text-navy placeholder:text-muted">
                        </div>
                    </form>

                </div>
            </section>

            {{-- LIST PENGUMUMAN --}}
            @if($pengumumans->isEmpty())

                <section class="bg-white rounded-xl border border-border-light p-12 text-center">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                        <i data-lucide="megaphone-off" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Belum ada pengumuman</h3>
                    <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                        @if($filterSearch || $filterProgram !== 'semua')
                            Tidak ada pengumuman dengan filter yang dipilih.
                        @else
                            Belum ada pengumuman yang dipublikasikan.
                        @endif
                    </p>

                    @if($filterSearch || $filterProgram !== 'semua')
                        <a href="{{ route('mahasiswa.pengumuman.index') }}"
                           class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy mt-4 hover:bg-gray-50">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Reset Filter
                        </a>
                    @endif
                </section>

            @else

                <div class="space-y-3">
                    @foreach($pengumumans as $pengumuman)

                        @php
                            $isBaru = $pengumuman->is_baru;
                            $snippet = \Illuminate\Support\Str::limit(strip_tags($pengumuman->isi), 180);
                        @endphp

                        <article class="bg-white rounded-xl border {{ $isBaru ? 'border-amber-200' : 'border-border-light' }} shadow-sm overflow-hidden transition-all">

                            {{-- Header row (selalu keliatan) --}}
                            <button type="button"
                                    data-pengumuman-id="{{ $pengumuman->id }}"
                                    onclick="togglePengumuman(this.dataset.pengumumanId)"
                                    class="w-full flex items-start gap-4 p-5 text-left hover:bg-gray-50 transition-colors">

                                {{-- Icon --}}
                                <div class="w-11 h-11 rounded-lg {{ $isBaru ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-navy' }} flex items-center justify-center shrink-0">
                                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">

                                    {{-- Badge row --}}
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-navy text-[10px] font-bold">
                                            {{ $pengumuman->nama_program }} {{ $pengumuman->nama_periode }}
                                        </span>

                                        <span class="text-[10px] text-muted">
                                            {{ \Carbon\Carbon::parse($pengumuman->published_at)->translatedFormat('d M Y') }}
                                        </span>

                                        @if($isBaru)
                                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold">
                                                Baru
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Judul --}}
                                    <h3 class="font-bold text-navy text-sm sm:text-base leading-snug">
                                        {{ $pengumuman->judul }}
                                    </h3>

                                    {{-- Snippet --}}
                                    <p class="text-xs text-muted mt-1.5 leading-relaxed line-clamp-2">
                                        {{ $snippet }}
                                    </p>

                                    {{-- Link --}}
                                    <div class="flex items-center gap-1.5 mt-2 text-[11px] font-bold text-navy">
                                        <span id="link-teks-{{ $pengumuman->id }}">Baca Selengkapnya</span>
                                        <i data-lucide="chevron-down" class="w-3 h-3 transition-transform" id="link-icon-{{ $pengumuman->id }}"></i>
                                    </div>

                                </div>

                            </button>

                            {{-- Isi lengkap (hidden by default) --}}
                            <div id="isi-{{ $pengumuman->id }}" class="hidden px-5 pb-5 pt-0">
                                <div class="border-t border-border-light pt-4 pl-15">
                                    <div class="text-sm text-dark-text leading-relaxed whitespace-pre-line">{{ $pengumuman->isi }}</div>
                                </div>
                            </div>

                        </article>

                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="mt-5 text-center text-[11px] text-muted">
                    Menampilkan {{ $pengumumans->count() }} pengumuman
                    @if($jumlahBaru > 0)
                        · <span class="text-amber-700 font-semibold">{{ $jumlahBaru }} baru</span>
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

    function togglePengumuman(id) {
        const isi = document.getElementById('isi-' + id);
        const link = document.getElementById('link-teks-' + id);
        const icon = document.getElementById('link-icon-' + id);

        if (!isi) return;

        const isHidden = isi.classList.contains('hidden');

        if (isHidden) {
            isi.classList.remove('hidden');
            if (link) link.textContent = 'Tutup';
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            isi.classList.add('hidden');
            if (link) link.textContent = 'Baca Selengkapnya';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }
</script>

</body>
</html>