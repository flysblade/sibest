<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Beasiswa — SIBEST UNUJA</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#1B2F5B',
                            dark: '#152447'
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

<div class="min-h-screen flex">

    {{-- ================= SIDEBAR ================= --}}
    @include('partials.sidebar-mahasiswa')

    {{-- ================= MAIN ================= --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        {{-- Top bar --}}
        <header class="min-h-20 px-5 sm:px-7 lg:px-9 py-4 bg-white border-b border-gray-200 flex items-center">

            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div>

                <h1 class="text-xl font-extrabold text-navy">
                    Daftar Beasiswa
                </h1>

                <p class="text-xs text-gray-500 mt-1">
                    Temukan beasiswa yang sesuai dengan kebutuhanmu
                </p>

                </div>
            </div>

        </header>


        {{-- Content --}}
        <section class="px-5 sm:px-7 lg:px-9 pt-5 lg:pt-7 pb-8">

            {{-- Filter --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">

                <div class="flex items-center gap-2">

                    <a
                        href="{{ route('mahasiswa.beasiswa.index') }}"
                        class="h-10 px-4 rounded-full flex items-center text-xs font-bold
                        {{ !request('jalur') ? 'bg-navy text-white' : 'bg-white text-gray-500 border border-gray-200' }}"
                    >
                        Semua
                    </a>


                    <a
                        href="{{ route('mahasiswa.beasiswa.index', ['jalur' => 'internal']) }}"
                        class="h-10 px-4 rounded-full flex items-center text-xs font-bold
                        {{ request('jalur') === 'internal' ? 'bg-navy text-white' : 'bg-white text-gray-500 border border-gray-200' }}"
                    >
                        Internal
                    </a>


                    <a
                        href="{{ route('mahasiswa.beasiswa.index', ['jalur' => 'eksternal']) }}"
                        class="h-10 px-4 rounded-full flex items-center text-xs font-bold
                        {{ request('jalur') === 'eksternal' ? 'bg-navy text-white' : 'bg-white text-gray-500 border border-gray-200' }}"
                    >
                        Eksternal
                    </a>

                </div>


                <form
                    action="{{ route('mahasiswa.beasiswa.index') }}"
                    method="GET"
                    class="w-full lg:w-72"
                >

                    @if(request('jalur'))

                        <input
                            type="hidden"
                            name="jalur"
                            value="{{ request('jalur') }}"
                        >

                    @endif


                    <div class="h-11 bg-white border border-gray-200 rounded-lg flex items-center px-3.5 gap-2.5">

                        <i
                            data-lucide="search"
                            class="w-[18px] h-[18px] text-gray-500"
                        ></i>


                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari beasiswa..."
                            class="flex-1 outline-none text-xs text-gray-700"
                        >


                        <i
                            data-lucide="sliders-horizontal"
                            class="w-[18px] h-[18px] text-navy"
                        ></i>

                    </div>

                </form>

            </div>


            {{-- Result count --}}
            <div class="text-xs font-bold text-gray-500 mb-4">
                {{ $beasiswas->count() }} beasiswa tersedia
            </div>


            {{-- ================= CARDS ================= --}}
            @if($beasiswas->isEmpty())

                <div class="min-h-[520px] bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col items-center justify-center text-center px-6">

                    <div class="w-32 h-32 rounded-full bg-indigo-50 flex items-center justify-center mb-5">

                        <div class="w-20 h-20 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-center">

                            <i
                                data-lucide="search-x"
                                class="w-9 h-9 text-navy"
                            ></i>

                        </div>

                    </div>


                    <h2 class="text-xl font-extrabold text-navy">
                        Tidak ada beasiswa yang sesuai filter
                    </h2>


                    <p class="text-xs text-gray-500 mt-2 max-w-[520px] leading-5">
                        Coba ubah kategori atau kata kunci pencarian untuk melihat beasiswa lainnya.
                    </p>


                    <a
                        href="{{ route('mahasiswa.beasiswa.index') }}"
                        class="mt-5 h-10 px-4 bg-navy text-white rounded-lg flex items-center gap-2 text-xs font-bold"
                    >
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset filter
                    </a>

                </div>

            @else

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                    @foreach($beasiswas as $beasiswa)

                        @php

                            $jalur = strtolower($beasiswa->program->jalur);

                            $tanggalBuka = $beasiswa->tanggal_buka;
                            $tanggalTutup = $beasiswa->tanggal_tutup;


                            if (!$tanggalBuka && !$tanggalTutup) {

                                $statusTampilan = 'Akan Datang';

                            } elseif (
                                $tanggalBuka &&
                                now()->lt($tanggalBuka->copy()->startOfDay())
                            ) {

                                $statusTampilan = 'Akan Datang';

                            } elseif (
                                $tanggalTutup &&
                                now()->startOfDay()->lte(
                                    $tanggalTutup->copy()->startOfDay()
                                )
                            ) {

                                $statusTampilan = 'Aktif';

                            } else {

                                $statusTampilan = 'Selesai';

                            }


                            $sisaHari = null;


                            if (
                                $tanggalTutup &&
                                $statusTampilan === 'Aktif'
                            ) {

                                $sisaHari = now()->startOfDay()
                                    ->diffInDays(
                                        $tanggalTutup->copy()->startOfDay(),
                                        false
                                    );

                            }

                        @endphp


                        {{-- ================= CARD ================= --}}
                        <article class="p-5 bg-white rounded-xl border border-gray-200 shadow-sm min-h-[240px] flex flex-col">

                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-4">

                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">

                                    <i
                                        data-lucide="graduation-cap"
                                        class="w-5 h-5 text-navy"
                                    ></i>

                                </div>


                                <div class="flex items-center gap-1.5 shrink-0">

                                    <span class="px-2.5 py-[5px] rounded-full text-[10px] font-extrabold uppercase bg-navy text-white">
                                        {{ ucfirst($jalur) }}
                                    </span>


                                    <span
                                        class="px-2.5 py-[5px] rounded-full text-[10px] font-extrabold uppercase
                                        {{
                                            $statusTampilan === 'Akan Datang'
                                                ? 'bg-gray-100 text-gray-500'
                                                : (
                                                    $statusTampilan === 'Aktif'
                                                        ? 'bg-emerald-50 text-teal-700'
                                                        : 'bg-gray-100 text-gray-500'
                                                )
                                        }}"
                                    >
                                        {{ $statusTampilan }}
                                    </span>

                                </div>

                            </div>


                            {{-- Isi --}}
                            <div class="mt-4 min-w-0">

                                <h2 class="text-base font-extrabold text-navy leading-6">
                                    {{ $beasiswa->program->nama }}
                                </h2>


                                <p class="text-xs text-gray-500 font-medium leading-5 mt-2 line-clamp-3">
                                    {{ $beasiswa->program->deskripsi }}
                                </p>

                            </div>


                            {{-- Footer --}}
                            <div class="mt-auto pt-4 border-t border-gray-200 grid grid-cols-[minmax(0,1fr)_auto] items-end gap-4">

                                <div class="min-w-0">

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Batas pendaftaran
                                    </p>


                                    @if($beasiswa->tanggal_tutup)

                                        <p class="text-xs font-bold text-navy mt-1">
                                            {{ $beasiswa->tanggal_tutup->format('d M Y') }}
                                        </p>


                                        @if($sisaHari !== null && $sisaHari >= 0)

                                            <p class="text-xs font-extrabold text-gold mt-0.5">
                                                {{ $sisaHari }} hari lagi
                                            </p>

                                        @endif

                                    @else

                                        <p class="text-xs font-bold text-gray-500 mt-1">
                                            Belum dikonfirmasi
                                        </p>

                                    @endif

                                </div>


                                <a
                                    href="{{ route('mahasiswa.beasiswa.show', $beasiswa->id) }}"
                                    class="h-10 px-4 rounded-lg bg-navy text-white flex items-center justify-center gap-2 text-xs font-bold whitespace-nowrap shrink-0"
                                >
                                    <span>Lihat Detail</span>

                                    <i
                                        data-lucide="arrow-right"
                                        class="w-4 h-4"
                                    ></i>

                                </a>

                            </div>

                        </article>

                    @endforeach

                </div>

            @endif

        </section>

    </main>

</div>


<script>
    lucide.createIcons();
</script>
</body>

</html>