<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — SIBEST UNUJA</title>

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

    {{-- ============ SIDEBAR ============ --}}
    @include('partials.sidebar-mahasiswa')

    {{-- ============ MAIN CONTENT ============ --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- Header --}}
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div>
                <p class="text-xs font-semibold text-muted">Dashboard Mahasiswa</p>
                <h1 class="text-xl sm:text-2xl font-extrabold text-navy mt-1">Selamat datang, {{ $user->nama }} 👋</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500"></span>
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">
                    <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">{{ strtoupper(substr($user->nama, 0, 2)) }}</div>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8">

            {{-- Hero --}}
            <section class="rounded-2xl bg-navy text-white p-6 sm:p-7 mb-6 overflow-hidden relative">
                <div class="relative z-10 max-w-2xl">
                    <p class="text-gold text-xs font-bold uppercase tracking-widest mb-2">SIBEST • UNUJA</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight">Temukan dan pantau beasiswa kamu dalam satu tempat.</h2>
                    <p class="text-blue-100 text-sm sm:text-base mt-3 leading-relaxed">Lihat beasiswa yang sedang dibuka, pantau pengajuan, dan cek pengumuman terbaru.</p>
                    <a href="{{ route('mahasiswa.beasiswa.index') }}" class="inline-flex items-center gap-2 mt-5 px-4 py-2.5 rounded-lg bg-gold text-navy font-bold text-sm hover:opacity-90">
                        Lihat Beasiswa <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </section>

            {{-- Statistik Cards --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-navy flex items-center justify-center"><i data-lucide="graduation-cap" class="w-5 h-5"></i></div>
                        <span class="text-xs font-semibold text-green-600">Aktif</span>
                    </div>
                    <p class="text-sm text-muted mt-4">Beasiswa Dibuka</p>
                    <p class="text-2xl font-extrabold text-navy mt-1">{{ $beasiswaDibuka }}</p>
                </div>

                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="file-check-2" class="w-5 h-5"></i></div>
                        <span class="text-xs font-semibold text-amber-600">Proses</span>
                    </div>
                    <p class="text-sm text-muted mt-4">Pengajuan Aktif</p>
                    <p class="text-2xl font-extrabold text-navy mt-1">{{ $pengajuanAktif->count() }}</p>
                </div>

                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center"><i data-lucide="check-circle-2" class="w-5 h-5"></i></div>
                        <span class="text-xs font-semibold text-gray-500">Total</span>
                    </div>
                    <p class="text-sm text-muted mt-4">Tahap Selesai</p>
                    <p class="text-2xl font-extrabold text-navy mt-1">{{ $tahapSelesai }} / {{ $totalTahapan }}</p>
                </div>

                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 flex items-center justify-center"><i data-lucide="megaphone" class="w-5 h-5"></i></div>
                        <span class="text-xs font-semibold text-red-600">Baru</span>
                    </div>
                    <p class="text-sm text-muted mt-4">Pengumuman</p>
                    <p class="text-2xl font-extrabold text-navy mt-1">{{ $jumlahPengumuman }}</p>
                </div>
            </section>

            {{-- Pengajuan Saya --}}
            <section class="bg-white rounded-xl border border-border-light mb-6">
                <div class="p-5 border-b border-border-light flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-navy">Pengajuan Saya</h3>
                        <p class="text-xs text-muted mt-1">Seluruh pengajuan yang masih aktif</p>
                    </div>
                    <a href="{{ route('mahasiswa.pengajuan.index') }}" class="text-xs font-bold text-navy hover:underline">Lihat semua</a>
                </div>

                <div class="p-5 space-y-4">
                    @forelse ($pengajuanAktif as $pengajuan)
                        <div class="border border-border-light rounded-xl p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center shrink-0">
                                        <i data-lucide="award" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-navy">{{ $pengajuan->nama_program ?? 'Beasiswa' }}</h4>
                                        <p class="text-xs text-muted mt-1">
                                            {{ ucfirst($pengajuan->jalur ?? 'Beasiswa') }}
                                            • Periode {{ $pengajuan->nama_periode ?? $pengajuan->id_periode }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">
                                    {{ ucfirst($pengajuan->status_pengajuan) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="border border-dashed border-border-light rounded-xl p-6 text-center">
                            <p class="font-bold text-navy">Belum ada pengajuan aktif</p>
                            <p class="text-sm text-muted mt-1">Pengajuan beasiswa aktif akan muncul di sini.</p>
                            <a href="{{ route('mahasiswa.beasiswa.index') }}" class="inline-block mt-3 text-sm font-bold text-navy hover:underline">Lihat beasiswa</a>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Pengumuman Terbaru --}}
            <section id="pengumuman-terbaru" class="bg-white rounded-xl border border-border-light">
                <div class="p-5 border-b border-border-light flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-navy">Pengumuman Terbaru</h3>
                        <p class="text-xs text-muted mt-1">Info resmi yang dipublikasikan</p>
                    </div>
                    <span class="text-xs font-bold text-red-600">{{ $jumlahPengumuman }} tersedia</span>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        @forelse ($pengumumans as $pengumuman)
                            @php
                                $judul = data_get($pengumuman, 'judul')
                                    ?? data_get($pengumuman, 'title')
                                    ?? 'Informasi Beasiswa';

                                $isi = data_get($pengumuman, 'isi')
                                    ?? data_get($pengumuman, 'konten')
                                    ?? data_get($pengumuman, 'deskripsi')
                                    ?? data_get($pengumuman, 'content')
                                    ?? '';

                                $tanggalTutup = $pengumuman->tanggal_tutup
                                    ?? null;

                                $sisaHari = null;

                                if ($tanggalTutup) {
                                    $sisaHari = now()->startOfDay()->diffInDays(
                                        \Carbon\Carbon::parse($tanggalTutup)->startOfDay(),
                                        false
                                    );
                                }

                                $pengingatTenggat =
                                    $tanggalTutup
                                    && $sisaHari >= 0
                                    && $sisaHari <= 3;
                            @endphp

                            <div class="border-b border-border-light pb-4 last:border-b-0">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg
                                        {{ $pengingatTenggat ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-800' }}">
                                        @if ($pengingatTenggat)
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor"
                                                stroke-width="1.8">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="M12 7v5l3 2"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor"
                                                stroke-width="1.8">
                                                <path d="M4 11V5l16-2v18L4 15v-4Z"/>
                                                <path d="M4 11h16M7 16l2 5"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-bold text-dark-text">
                                            {{ $judul }}
                                        </h4>

                                        <p class="mt-1 text-xs leading-5 text-muted">
                                            @if ($pengingatTenggat)
                                                @if ($sisaHari === 0)
                                                    Batas pendaftaran
                                                    {{ $pengumuman->nama_program ?? 'beasiswa ini' }}
                                                    adalah hari ini. Segera selesaikan pengajuan Anda.
                                                @elseif ($sisaHari === 1)
                                                    Pendaftaran
                                                    {{ $pengumuman->nama_program ?? 'beasiswa ini' }}
                                                    ditutup besok. Segera lengkapi persyaratan Anda.
                                                @else
                                                    Pendaftaran
                                                    {{ $pengumuman->nama_program ?? 'beasiswa ini' }}
                                                    ditutup {{ $sisaHari }} hari lagi.
                                                    Segera ajukan permohonan sebelum batas waktu berakhir.
                                                @endif
                                            @elseif ($isi !== '')
                                                {{ \Illuminate\Support\Str::limit(strip_tags($isi), 150) }}
                                            @elseif (!empty($pengumuman->nama_program))
                                                Informasi terbaru mengenai
                                                {{ $pengumuman->nama_program }}.
                                                Silakan periksa persyaratan dan jadwal pendaftarannya.
                                            @else
                                                Silakan periksa informasi beasiswa dan jadwal
                                                pendaftaran melalui SIBEST.
                                            @endif
                                        </p>

                                        <p class="mt-2 text-[11px] text-muted">
                                            {{ \Carbon\Carbon::parse($pengumuman->published_at)->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center">
                                <p class="text-sm font-medium text-dark-text">
                                    Belum ada pengumuman terbaru
                                </p>
                                <p class="mt-1 text-xs text-muted">
                                    Informasi beasiswa akan ditampilkan di sini.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

        </div> {{-- End p-5 sm:p-8 --}}
    </main>
</div> {{-- End min-h-screen --}}

<script>
    lucide.createIcons();

</script>

</body>
</html>