<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pengajuan Saya — SIBEST UNUJA</title>

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
                        jakarta: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

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

                <p class="text-xs font-semibold text-muted">
                    Mahasiswa
                </p>

                <h1 class="text-xl sm:text-2xl font-extrabold text-navy mt-1">
                    Pengajuan Saya
                </h1>

                </div>
            </div>


            <div class="flex items-center gap-3">

                <button
                    class="relative w-10 h-10 rounded-lg border border-border-light flex items-center justify-center text-gray-600 hover:bg-gray-50"
                >
                    <i data-lucide="bell" class="w-5 h-5"></i>

                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-red-500"></span>
                </button>


                <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-border-light">

                    <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>

                </div>

            </div>

        </header>


        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">


            {{-- INTRO --}}
            <div class="mb-6">

                <p class="text-gold text-xs font-bold uppercase tracking-widest mb-2">
                    SIBEST • UNUJA
                </p>

                <h2 class="text-2xl sm:text-3xl font-extrabold text-navy">
                    Pantau pengajuan beasiswa kamu.
                </h2>

                <p class="text-sm text-gray-500 mt-2 max-w-2xl">
                    Lihat status pengajuan yang sedang berjalan dan lanjutkan proses yang belum selesai.
                </p>

            </div>


            {{-- EMPTY --}}
            @if($pengajuans->isEmpty())

                <section class="bg-white rounded-xl border border-border-light p-10 text-center">

                    <div class="mx-auto w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>

                    <h3 class="font-extrabold text-navy mt-4">
                        Belum ada pengajuan
                    </h3>

                    <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
                        Kamu belum memiliki pengajuan beasiswa yang sedang berjalan.
                    </p>

                    <a
                        href="{{ route('mahasiswa.beasiswa.index') }}"
                        class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold mt-5 hover:bg-navy-dark"
                    >
                        Lihat Beasiswa

                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                </section>


            @else


                {{-- STAT --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                    <section class="bg-white rounded-xl border border-border-light p-5">
                        <p class="text-sm text-muted">
                            Total Aktif
                        </p>

                        <p class="text-2xl font-extrabold text-navy mt-1">
                            {{ $pengajuans->count() }}
                        </p>
                    </section>


                    <section class="bg-white rounded-xl border border-border-light p-5">
                        <p class="text-sm text-muted">
                            Diproses
                        </p>

                        <p class="text-2xl font-extrabold text-navy mt-1">
                            {{ $pengajuans->where('status', 'diproses')->count() }}
                        </p>
                    </section>


                    <section class="bg-white rounded-xl border border-border-light p-5">
                        <p class="text-sm text-muted">
                            Draft
                        </p>

                        <p class="text-2xl font-extrabold text-navy mt-1">
                            {{ $pengajuans->where('status', 'draft')->count() }}
                        </p>
                    </section>

                </div>


                {{-- LIST --}}
                <section class="space-y-4">

                    @foreach($pengajuans as $pengajuan)

                        @php

                            $statusConfig = match($pengajuan->status) {

                                'draft' => [
                                    'label' => 'Draft',
                                    'class' => 'bg-gray-100 text-gray-600',
                                    'icon' => 'file-edit'
                                ],

                                'diajukan' => [
                                    'label' => 'Diajukan',
                                    'class' => 'bg-yellow-50 text-yellow-700',
                                    'icon' => 'send'
                                ],

                                'diproses' => [
                                    'label' => 'Diproses',
                                    'class' => 'bg-indigo-50 text-indigo-700',
                                    'icon' => 'loader-circle'
                                ],

                                default => [
                                    'label' => ucfirst($pengajuan->status),
                                    'class' => 'bg-gray-100 text-gray-600',
                                    'icon' => 'file-text'
                                ],

                            };

                        @endphp


                        <article class="bg-white rounded-xl border border-border-light p-5">

                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                                <div class="flex items-start gap-4 min-w-0">

                                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center shrink-0">
                                        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                                    </div>


                                    <div class="min-w-0">

                                        <h3 class="text-base font-extrabold text-navy">
                                            {{ $pengajuan->nama_program }}
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-2 mt-2">

                                            <span class="px-2 py-1 rounded-full bg-navy text-white text-[10px] font-extrabold uppercase">
                                                {{ $pengajuan->jalur }}
                                            </span>

                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-extrabold">
                                                {{ $pengajuan->nama_kategori }}
                                            </span>

                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-extrabold">
                                                {{ $pengajuan->nama_periode }}
                                            </span>

                                        </div>

                                    </div>

                                </div>


                                <span class="inline-flex items-center gap-1.5 shrink-0 px-2.5 py-1.5 rounded-full {{ $statusConfig['class'] }} text-[10px] font-extrabold">

                                    <i
                                        data-lucide="{{ $statusConfig['icon'] }}"
                                        class="w-3.5 h-3.5"
                                    ></i>

                                    {{ $statusConfig['label'] }}

                                </span>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5 pt-4 border-t border-border-light">

                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        ID Pengajuan
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-1">
                                        #{{ $pengajuan->id }}
                                    </p>
                                </div>


                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Dibuat
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-1">
                                        {{ \Carbon\Carbon::parse($pengajuan->created_at)->translatedFormat('d M Y') }}
                                    </p>
                                </div>


                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Batas Pendaftaran
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-1">
                                        {{ $pengajuan->tanggal_tutup ? \Carbon\Carbon::parse($pengajuan->tanggal_tutup)->translatedFormat('d M Y') : 'Jadwal menyusul' }}
                                    </p>
                                </div>

                            </div>


                            <div class="flex justify-end mt-5">

                                @if($pengajuan->status === 'draft')

                                    <a
                                        href="{{ route('mahasiswa.pengajuan.upload', $pengajuan->id) }}"
                                        class="h-10 px-4 rounded-lg bg-navy text-white flex items-center gap-2 text-xs font-bold hover:bg-navy-dark"
                                    >
                                        Lanjutkan Pengajuan

                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>

                                @else

                                    <span class="h-10 px-4 rounded-lg bg-gray-100 text-gray-500 flex items-center text-xs font-bold">
                                        Sedang diproses
                                    </span>

                                @endif

                            </div>

                        </article>

                    @endforeach

                </section>

            @endif

        </div>

    </main>

</div>


<script>
    lucide.createIcons();

</script>
</body>
</html>