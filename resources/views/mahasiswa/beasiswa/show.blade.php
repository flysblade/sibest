<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $beasiswa->nama_program }} — SIBEST UNUJA</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#1B2F5B',
                            dark: '#152447',
                        },
                        gold: '#F5A623',
                        'dark-text': '#172B4D',
                        muted: '#6B7280',
                        'border-light': '#E5E7EB',
                        'bg-page': '#F5F7FA',
                    },

                    fontFamily: {
                        jakarta: [
                            '"Plus Jakarta Sans"',
                            'Inter',
                            'sans-serif'
                        ],
                    },
                },
            },
        };
    </script>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body class="bg-bg-page text-dark-text font-jakarta">

@php
    $tanggalBuka = $beasiswa->tanggal_buka
        ? \Carbon\Carbon::parse($beasiswa->tanggal_buka)
        : null;

    $tanggalTutup = $beasiswa->tanggal_tutup
        ? \Carbon\Carbon::parse($beasiswa->tanggal_tutup)
        : null;

    $today = now()->startOfDay();

    if ($tanggalBuka && $today->lt($tanggalBuka->copy()->startOfDay())) {
        $statusLabel = 'Akan Datang';
        $statusClass = 'bg-gray-100 text-gray-600';
    } elseif ($tanggalTutup && $today->gt($tanggalTutup->copy()->startOfDay())) {
        $statusLabel = 'Selesai';
        $statusClass = 'bg-gray-100 text-gray-600';
    } else {
        $statusLabel = 'Aktif';
        $statusClass = 'bg-emerald-50 text-teal-700';
    }

    $sisaHari = null;

    if ($tanggalTutup && $statusLabel === 'Aktif') {
        $sisaHari = $today->diffInDays(
            $tanggalTutup->copy()->startOfDay(),
            false
        );
    }

    $jalur = strtolower($beasiswa->jalur);

    $dokumenWajib = $dokumen->where('required', 1);
    $dokumenOpsional = $dokumen->where('required', 0);

    $nim = $user->nim ?? $user->username ?? '-';
@endphp


<div class="min-h-screen flex">

    {{-- ============ SIDEBAR ============ --}}
     @include('partials.sidebar-script')

    {{-- ============ MAIN ============ --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-[margin] duration-300">

        {{-- ================= TOPBAR ================= --}}
        <header class="h-20 px-6 lg:px-9 bg-white border-b border-gray-200 flex items-center justify-between">

            <a
                href="{{ route('mahasiswa.beasiswa.index') }}"
                class="flex items-center gap-2 text-sm font-bold text-navy hover:text-gold transition-colors"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Beasiswa
            </a>


            <div class="flex items-center gap-4">

                <button
                    type="button"
                    class="relative w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-navy hover:bg-gray-50"
                >
                    <i data-lucide="bell" class="w-5 h-5"></i>

                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-gold"></span>
                </button>


                <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-gray-200">

                    <div class="w-9 h-9 rounded-full bg-yellow-50 text-navy flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>

                    <div>
                        <p class="text-xs font-bold text-navy">
                            {{ $user->nama }}
                        </p>

                        <p class="text-[10px] text-gray-500">
                            Mahasiswa
                        </p>
                    </div>

                </div>

            </div>

        </header>



        {{-- ================= CONTENT ================= --}}
        <section class="px-5 sm:px-7 lg:px-9 pt-5 pb-8">


            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1 text-xs mb-4">

                <a
                    href="{{ route('mahasiswa.beasiswa.index') }}"
                    class="text-gray-500 font-semibold hover:text-navy"
                >
                    Daftar Beasiswa
                </a>

                <span class="text-gray-400">›</span>

                <span class="text-navy font-bold">
                    {{ $beasiswa->nama_program }}
                </span>

            </div>



            {{-- ================= HERO ================= --}}
            <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] px-5 py-4">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                    <div class="flex items-start gap-4 min-w-0">

                        {{-- Icon --}}
                        <div class="w-12 h-12 rounded-lg bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                            <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                        </div>


                        <div class="min-w-0">

                            {{-- Badges --}}
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">

                                <span class="px-2 py-[5px] rounded-full bg-navy text-white text-[10px] font-extrabold uppercase">
                                    {{ ucfirst($jalur) }}
                                </span>

                                <span class="px-2 py-[5px] rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-extrabold uppercase">
                                    {{ $beasiswa->nama_kategori }}
                                </span>

                                <span class="px-2 py-[5px] rounded-full {{ $statusClass }} text-[10px] font-extrabold uppercase">
                                    {{ $statusLabel }}
                                </span>

                            </div>


                            <h1 class="text-2xl font-extrabold text-navy leading-tight">
                                {{ $beasiswa->nama_program }}
                            </h1>


                            <p class="text-xs text-gray-500 font-medium mt-1.5 leading-5">
                                {{ $beasiswa->deskripsi ?: 'Informasi beasiswa belum memiliki deskripsi.' }}
                            </p>

                        </div>

                    </div>


                    {{-- Deadline --}}
                    <div class="lg:min-w-[150px] lg:text-right shrink-0">

                        <p class="text-[10px] font-bold text-gray-500 uppercase">
                            Batas pendaftaran
                        </p>

                        <p class="text-base font-extrabold text-navy mt-1">

                            @if($tanggalTutup)
                                {{ $tanggalTutup->translatedFormat('d M Y') }}
                            @else
                                Jadwal menyusul
                            @endif

                        </p>

                        @if($sisaHari !== null && $sisaHari >= 0)

                            <p class="text-xs font-extrabold text-gold mt-0.5">
                                {{ $sisaHari }} hari lagi
                            </p>

                        @endif

                    </div>

                </div>

            </section>



            {{-- ================= MAIN GRID ================= --}}
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-4 mt-4">


                {{-- ================= LEFT ================= --}}
                <div class="space-y-4">


                    {{-- Tentang / Deskripsi --}}
                    <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                        <div class="flex items-center gap-2 mb-3">

                            <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                <i data-lucide="align-left" class="w-4 h-4"></i>
                            </div>

                            <h2 class="text-base font-extrabold text-navy">
                                Deskripsi
                            </h2>

                        </div>


                        <div class="text-xs sm:text-sm text-gray-500 leading-6 whitespace-pre-line">
                            {{ $beasiswa->deskripsi ?: 'Informasi deskripsi belum tersedia.' }}
                        </div>

                    </section>



                    {{-- Persyaratan --}}
                    <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                        <div class="flex items-center gap-2 mb-3">

                            <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                <i data-lucide="list-checks" class="w-4 h-4"></i>
                            </div>

                            <h2 class="text-base font-extrabold text-navy">
                                Persyaratan
                            </h2>

                        </div>


                        @if($beasiswa->syarat)

                            <div class="space-y-2.5">

                                @foreach(preg_split('/\r\n|\r|\n/', trim($beasiswa->syarat)) as $line)

                                    @php
                                        $line = trim($line);
                                    @endphp

                                    @continue($line === '')

                                    @if(
                                        str_ends_with($line, ':') ||
                                        preg_match('/^(Persyaratan|Ketentuan|Catatan|Dokumen|Prestasi|Afirmasi|Guru|Kompetisi|Santri|Khusus)/i', $line)
                                    )

                                        <div class="pt-1 first:pt-0">
                                            <p class="text-xs font-extrabold text-navy leading-5">
                                                {{ $line }}
                                            </p>
                                        </div>

                                    @else

                                        <div class="flex items-start gap-2.5">

                                            <span class="w-1.5 h-1.5 rounded-full bg-gold mt-2 shrink-0"></span>

                                            <p class="text-xs sm:text-sm text-gray-500 leading-5">
                                                {{ preg_replace('/^[•\-\d]+[.)]?\s*/', '', $line) }}
                                            </p>

                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        @else

                            <p class="text-xs text-gray-500">
                                Persyaratan belum tersedia.
                            </p>

                        @endif

                    </section>

                    {{-- Tahapan Seleksi --}}
                    @if($tahapan->count() > 0)

                        <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                            <div class="flex items-center gap-2 mb-5">
                                <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                    <i data-lucide="route" class="w-4 h-4"></i>
                                </div>

                                <h2 class="text-base font-extrabold text-navy">
                                    Tahapan Seleksi
                                </h2>
                            </div>

                            <div class="overflow-x-auto pb-2">
                                <div class="flex min-w-max">

                                    @foreach($tahapan as $index => $tahap)

                                        @php
                                            $tanggalMulai = $tahap->mulai_at
                                                ? \Carbon\Carbon::parse($tahap->mulai_at)->translatedFormat('d M Y')
                                                : null;

                                            $tanggalSelesai = $tahap->selesai_at
                                                ? \Carbon\Carbon::parse($tahap->selesai_at)->translatedFormat('d M Y')
                                                : null;

                                            $rentangTanggal = collect([
                                                $tanggalMulai,
                                                $tanggalSelesai,
                                            ])->filter()->join(' — ');
                                        @endphp

                                        <div class="w-[190px] shrink-0">

                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center text-[10px] font-extrabold shrink-0">
                                                    {{ $index + 1 }}
                                                </div>

                                                @unless($loop->last)
                                                    <div class="w-[155px] h-px bg-gray-200 mx-3"></div>
                                                @endunless
                                            </div>

                                            <div class="mt-4 pr-6">
                                                <p class="text-xs font-extrabold text-navy leading-5">
                                                    {{ $tahap->nama_tahapan }}
                                                </p>

                                                @if($rentangTanggal)
                                                    <p class="text-[10px] text-gray-500 mt-1.5 leading-4">
                                                        {{ $rentangTanggal }}
                                                    </p>
                                                @endif

                                                @if($tahap->lokasi)
                                                    <p class="text-[10px] text-gray-500 mt-1 leading-4">
                                                        {{ $tahap->lokasi }}
                                                    </p>
                                                @endif

                                                @if($tahap->keterangan)
                                                    <p class="text-[10px] text-gray-400 mt-1.5 leading-4">
                                                        {{ $tahap->keterangan }}
                                                    </p>
                                                @endif
                                            </div>

                                        </div>

                                    @endforeach

                                </div>
                            </div>

                        </section>

                    @elseif($jalur === 'eksternal')

                        <section class="bg-indigo-50 rounded-xl border border-indigo-100 p-4">

                            <div class="flex items-start gap-3">

                                <i data-lucide="info" class="w-5 h-5 text-navy mt-0.5 shrink-0"></i>

                                <div>
                                    <p class="text-xs font-extrabold text-navy">
                                        Proses seleksi dilakukan oleh penyelenggara
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1 leading-5">
                                        Beasiswa eksternal tidak diproses melalui tahapan seleksi SIBEST.
                                    </p>
                                </div>

                            </div>

                        </section>

                    @endif

                   


                    {{-- Dokumen --}}
                    @if($dokumen->count() > 0)

                        <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                            <div class="flex items-center gap-2 mb-4">

                                <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                    <i data-lucide="files" class="w-4 h-4"></i>
                                </div>

                                <h2 class="text-base font-extrabold text-navy">
                                    Dokumen yang Dibutuhkan
                                </h2>

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                                {{-- Wajib --}}
                                <div>

                                    <p class="text-[10px] font-extrabold text-navy uppercase mb-2">
                                        Wajib
                                    </p>


                                    @if($dokumenWajib->count() > 0)

                                        <div class="space-y-2.5">

                                            @foreach($dokumenWajib as $item)

                                                <div class="flex items-start gap-2">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-gold mt-1.5 shrink-0"></span>

                                                    <div>

                                                        <p class="text-xs text-gray-500 leading-5">
                                                            {{ $item->nama_dokumen }}
                                                        </p>

                                                        @if($item->keterangan)

                                                            <p class="text-[10px] text-gray-400 leading-4">
                                                                {{ $item->keterangan }}
                                                            </p>

                                                        @endif

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="text-xs text-gray-500">
                                            Tidak ada dokumen wajib.
                                        </p>

                                    @endif

                                </div>



                                {{-- Optional --}}
                                <div>

                                    <p class="text-[10px] font-extrabold text-yellow-700 uppercase mb-2">
                                        Jika Berlaku
                                    </p>


                                    @if($dokumenOpsional->count() > 0)

                                        <div class="space-y-2.5">

                                            @foreach($dokumenOpsional as $item)

                                                <div class="flex items-start gap-2">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mt-1.5 shrink-0"></span>

                                                    <div>

                                                        <p class="text-xs text-gray-500 leading-5">
                                                            {{ $item->nama_dokumen }}
                                                        </p>

                                                        @if($item->keterangan)

                                                            <p class="text-[10px] text-gray-400 leading-4">
                                                                {{ $item->keterangan }}
                                                            </p>

                                                        @endif

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="text-xs text-gray-500">
                                            Tidak ada dokumen tambahan.
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </section>

                    @endif



                    {{-- Pengumuman --}}
                    @if($pengumuman->count() > 0)

                        <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                            <div class="flex items-center gap-2 mb-3">

                                <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                    <i data-lucide="megaphone" class="w-4 h-4"></i>
                                </div>

                                <h2 class="text-base font-extrabold text-navy">
                                    Pengumuman Terkait
                                </h2>

                            </div>


                            <div>

                                @foreach($pengumuman as $item)

                                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">

                                        <div class="w-7 h-7 rounded-md bg-yellow-50 text-gold flex items-center justify-center shrink-0">
                                            <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>
                                        </div>


                                        <div class="min-w-0 flex-1">

                                            <p class="text-xs font-bold text-navy">
                                                {{ $item->judul }}
                                            </p>

                                            @if($item->published_at)

                                                <p class="text-[10px] text-gray-500 mt-0.5">
                                                    {{ \Carbon\Carbon::parse($item->published_at)->translatedFormat('d M Y') }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </section>

                    @endif

                </div>



                {{-- ================= RIGHT ================= --}}
                <aside class="space-y-4">


                    {{-- Ringkasan --}}
                    <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                        <div class="flex items-center justify-between mb-4">

                            <h2 class="text-base font-extrabold text-navy">
                                Ringkasan Beasiswa
                            </h2>

                            <span class="px-2 py-[5px] rounded-full {{ $statusClass }} text-[10px] font-extrabold uppercase">
                                {{ $statusLabel }}
                            </span>

                        </div>


                        <div class="space-y-4">


                            {{-- Deadline --}}
                            <div class="flex items-start gap-2.5">

                                <i data-lucide="calendar-days" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Deadline
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $tanggalTutup ? $tanggalTutup->translatedFormat('d M Y') : 'Belum dikonfirmasi' }}
                                    </p>

                                </div>

                            </div>



                            {{-- Periode --}}
                            <div class="flex items-start gap-2.5">

                                <i data-lucide="calendar-range" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Periode
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $beasiswa->nama_periode }}
                                    </p>

                                </div>

                            </div>



                            {{-- Jalur --}}
                            <div class="flex items-start gap-2.5">

                                <i data-lucide="route" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Jalur
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ ucfirst($jalur) }}
                                    </p>

                                </div>

                            </div>



                            {{-- Kategori --}}
                            <div class="flex items-start gap-2.5">

                                <i data-lucide="tag" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Kategori
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $beasiswa->nama_kategori }}
                                    </p>

                                </div>

                            </div>



                            {{-- PIC --}}
                            <div class="flex items-start gap-2.5">

                                <i data-lucide="user-round" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Kontak PIC
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $beasiswa->kontak_pic ?: 'Belum tersedia' }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="border-t border-gray-200 my-4"></div>


                        {{-- CTA --}}
                        <div class="space-y-2">
                            @if($jalur === 'hybrid' && strtolower($beasiswa->nama_program) === 'kip-k')

                                <a
                                    href="{{ $beasiswa->link_portal }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="w-full h-11 px-3.5 bg-white rounded-lg border border-navy flex items-center justify-center gap-2 text-navy text-xs font-bold hover:bg-gray-50 transition-colors"
                                >
                                    Daftar di Portal KIP-K
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>

                                <form
                                    action="{{ route('mahasiswa.pengajuan.kipk.store', $beasiswa->id) }}"
                                    method="POST"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="w-full h-11 px-3.5 bg-navy hover:bg-navy-dark rounded-lg flex items-center justify-center gap-2 text-white text-xs font-bold transition-colors"
                                    >
                                        Saya Sudah Daftar — Ajukan ke UNUJA
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </button>
                                </form>

                            @elseif(in_array($jalur, ['internal', 'hybrid']))


                                @if($statusLabel === 'Aktif')

                                    <a
                                        href="{{ route('mahasiswa.pengajuan.create', $beasiswa->id) }}"
                                        class="w-full h-11 px-3.5 bg-navy hover:bg-navy-dark rounded-lg flex items-center justify-center gap-2 text-white text-xs font-bold transition-colors"
                                    >
                                        {{ $jalur === 'hybrid' ? 'Ajukan di SIBEST' : 'Ajukan Sekarang' }}

                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>

                                @else

                                    <div class="w-full min-h-11 px-3.5 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 text-xs font-bold text-center">
                                        {{ $statusLabel === 'Akan Datang' ? 'Pendaftaran belum dibuka' : 'Pendaftaran telah berakhir' }}
                                    </div>

                                @endif


                                @if($jalur === 'hybrid' && $beasiswa->link_portal)

                                    <a
                                        href="{{ $beasiswa->link_portal }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="w-full h-11 px-3.5 bg-white rounded-lg border border-navy flex items-center justify-center gap-2 text-navy text-xs font-bold hover:bg-gray-50 transition-colors"
                                    >
                                        Portal Resmi

                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>

                                @endif


                            @elseif($jalur === 'eksternal')

                                @if($beasiswa->link_portal)

                                    <a
                                        href="{{ $beasiswa->link_portal }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="w-full h-11 px-3.5 bg-navy hover:bg-navy-dark rounded-lg flex items-center justify-center gap-2 text-white text-xs font-bold transition-colors"
                                    >
                                        Buka Website Resmi

                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>

                                @else

                                    <div class="w-full min-h-11 px-3.5 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 text-xs font-bold text-center">
                                        Link pendaftaran belum tersedia
                                    </div>

                                @endif

                            @endif

                        </div>

                    </section>



                    {{-- Contact --}}
                    <section class="bg-white rounded-xl border border-gray-200 shadow-[0px_4px_16px_0px_rgba(27,47,91,0.05)] p-5">

                        <div class="flex items-center gap-2 mb-3">

                            <div class="w-8 h-8 rounded-md bg-indigo-50 text-navy flex items-center justify-center shrink-0">
                                <i data-lucide="contact" class="w-4 h-4"></i>
                            </div>

                            <h2 class="text-base font-extrabold text-navy">
                                Kontak PIC
                            </h2>

                        </div>


                        @if($beasiswa->kontak_pic)

                            <p class="text-xs font-bold text-navy leading-5">
                                {{ $beasiswa->kontak_pic }}
                            </p>

                        @else

                            <p class="text-xs text-gray-500 leading-5">
                                Informasi kontak PIC belum tersedia.
                            </p>

                        @endif

                    </section>

                </aside>

            </div>

        </section>

    </main>

</div>


<script>
    lucide.createIcons();
</script>
</body>
</html>