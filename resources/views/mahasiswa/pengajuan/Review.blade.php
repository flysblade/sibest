<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Review & Finalisasi - SIBEST</title>

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
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>


<body class="bg-bg-page text-dark-text font-jakarta">

@php

    $nim = $user->nim ?? $user->username ?? '-';

    $namaProdi = $profile->prodi ?? '-';
    $angkatan = $profile->angkatan ?? '-';
    $noHp = $profile->no_hp ?? '-';

    $dokumenWajib = $dokumen->where('required', 1);

    $dokumenOpsional = $dokumen->where('required', 0);

@endphp


<div class="min-h-screen flex">

    {{-- ============SIDEBAR============== --}}
    @include('partials.sidebar-mahasiswa')
    {{-- =============== MAIN ================ --}}

    <main
        id="mainContent"
        class="w-full lg:ml-64 transition-all duration-300"
    >


        {{-- TOPBAR --}}

        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">

            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div class="min-w-0">

                <div class="text-xs text-slate-400 truncate">

                    Pengajuan

                    <span class="mx-1">›</span>

                    {{ $pengajuan->nama_program }}

                    <span class="mx-1">›</span>

                    Review & Finalisasi

                    </div>
                </div>

            </div>


            <div class="flex items-center gap-3 shrink-0">

                <div class="text-right hidden sm:block">

                    <div class="text-xs font-semibold text-dark-text">
                        {{ $user->nama }}
                    </div>

                    <div class="text-[10px] text-slate-400">
                        Mahasiswa
                    </div>

                </div>


                <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($user->nama, 0, 2)) }}
                </div>

            </div>

        </header>


        {{-- CONTENT --}}

        <div class="p-5 sm:p-8">

            {{-- HEADER --}}

            <div class="mb-6">

                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Review & Finalisasi
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $pengajuan->nama_program }}
                    {{ $pengajuan->nama_periode }}
                    — Langkah 3 dari 3
                </p>

            </div>


            {{-- STEPPER --}}

            <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">

                <div class="flex items-center">

                    {{-- STEP 1 --}}

                    <div class="flex-1 flex items-center">

                        <div class="shrink-0">

                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center">

                                <i data-lucide="check" class="w-4 h-4"></i>

                            </div>

                            <p class="text-[10px] font-bold text-green-700 text-center mt-1.5">
                                Isi Data
                            </p>

                        </div>

                        <div class="flex-1 h-0.5 bg-green-500 mx-3"></div>

                    </div>


                    {{-- STEP 2 --}}

                    <div class="flex-1 flex items-center">

                        <div class="shrink-0">

                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center">

                                <i data-lucide="check" class="w-4 h-4"></i>

                            </div>

                            <p class="text-[10px] font-bold text-green-700 text-center mt-1.5">
                                Upload Dokumen
                            </p>

                        </div>

                        <div class="flex-1 h-0.5 bg-gold mx-3"></div>

                    </div>


                    {{-- STEP 3 --}}

                    <div class="shrink-0">

                        <div class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center text-xs font-bold">
                            3
                        </div>

                        <p class="text-[10px] font-bold text-navy text-center mt-1.5">
                            Review & Finalisasi
                        </p>

                    </div>

                </div>

            </div>


            {{-- GRID --}}

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-5">


                {{-- LEFT --}}

                <div class="space-y-5">


                    {{-- DATA DIRI --}}

                    <section class="bg-slate-50 rounded-xl border border-slate-200 p-5">

                        <div class="flex items-center gap-2 mb-5">

                            <div class="w-1 h-5 rounded-full bg-gold"></div>

                            <h2 class="text-sm font-extrabold uppercase text-navy">
                                Data Diri
                            </h2>

                            <div class="flex-1 h-px bg-slate-200"></div>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-5 gap-y-5">

                            <div>
                                <p class="text-[10px] text-slate-500">
                                    Nama
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $user->nama }}
                                </p>
                            </div>


                            <div>
                                <p class="text-[10px] text-slate-500">
                                    NIM / No. Pendaftaran
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $nim }}
                                </p>
                            </div>


                            <div>
                                <p class="text-[10px] text-slate-500">
                                    Program Studi
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $namaProdi }}
                                </p>
                            </div>


                            <div>
                                <p class="text-[10px] text-slate-500">
                                    Email
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1 break-all">
                                    {{ $user->email }}
                                </p>
                            </div>


                            <div>
                                <p class="text-[10px] text-slate-500">
                                    Angkatan
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $angkatan }}
                                </p>
                            </div>


                            <div>
                                <p class="text-[10px] text-slate-500">
                                    WhatsApp
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $noHp }}
                                </p>
                            </div>

                        </div>

                    </section>


                    {{-- DATA PENGAJUAN --}}

                    <section class="bg-slate-50 rounded-xl border border-slate-200 p-5">

                        <div class="flex items-center gap-2 mb-5">

                            <div class="w-1 h-5 rounded-full bg-gold"></div>

                            <h2 class="text-sm font-extrabold uppercase text-navy">
                                Data Pengajuan
                            </h2>

                            <div class="flex-1 h-px bg-slate-200"></div>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div class="bg-white border border-slate-200 rounded-lg p-3">

                                <p class="text-[10px] text-slate-500">
                                    Beasiswa
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $pengajuan->nama_program }}
                                </p>

                            </div>


                            <div class="bg-white border border-slate-200 rounded-lg p-3">

                                <p class="text-[10px] text-slate-500">
                                    Periode
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $pengajuan->nama_periode }}
                                </p>

                            </div>


                            <div class="bg-white border border-slate-200 rounded-lg p-3">

                                <p class="text-[10px] text-slate-500">
                                    Jalur
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ ucfirst($pengajuan->jalur) }}
                                </p>

                            </div>


                            <div class="bg-white border border-slate-200 rounded-lg p-3">

                                <p class="text-[10px] text-slate-500">
                                    Status
                                </p>

                                <p class="text-xs font-bold text-amber-600 mt-1">
                                    Draft
                                </p>

                            </div>

                        </div>

                    </section>


                    {{-- DOKUMEN --}}

                    <section class="bg-slate-50 rounded-xl border border-slate-200 p-5">

                        <div class="flex items-center gap-2 mb-4">

                            <div class="w-1 h-5 rounded-full bg-gold"></div>

                            <h2 class="text-sm font-extrabold uppercase text-navy">
                                Dokumen
                            </h2>

                            <div class="flex-1 h-px bg-slate-200"></div>

                            <a
                                href="{{ route('mahasiswa.pengajuan.upload', $pengajuan->id) }}"
                                class="text-[10px] font-bold text-navy hover:text-gold"
                            >
                                Edit · Langkah 2
                            </a>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                            @foreach($dokumen as $item)

                                <div
                                    class="h-10 flex items-center gap-2 px-3 rounded-lg bg-white border
                                    {{ $item->original_filename
                                        ? 'border-green-200'
                                        : 'border-red-200' }}"
                                >

                                    @if($item->original_filename)

                                        <i
                                            data-lucide="check-circle"
                                            class="w-4 h-4 text-green-600 shrink-0"
                                        ></i>

                                    @else

                                        <i
                                            data-lucide="circle-alert"
                                            class="w-4 h-4 text-red-500 shrink-0"
                                        ></i>

                                    @endif


                                    <p class="flex-1 min-w-0 text-[10px] font-semibold text-slate-700 truncate">

                                        {{ $item->nama_dokumen }}

                                        @if(!$item->required)
                                            <span class="text-slate-400">
                                                (opsional)
                                            </span>
                                        @endif

                                    </p>

                                </div>

                            @endforeach

                        </div>

                    </section>


                    {{-- WARNING --}}

                    <div class="flex items-start gap-3 p-3 rounded-lg bg-amber-50 border border-amber-200">

                        <i
                            data-lucide="triangle-alert"
                            class="w-5 h-5 text-amber-600 shrink-0"
                        ></i>

                        <p class="text-xs text-amber-800 leading-relaxed">

                            <strong>Perhatian:</strong>
                            Setelah finalisasi, data pengajuan tidak dapat diubah.
                            Pastikan semua informasi sudah benar.

                        </p>

                    </div>


                    {{-- AGREEMENT --}}

                    <form
                        action="{{ route('mahasiswa.pengajuan.finalize', $pengajuan->id) }}"
                        method="POST"
                    >

                        @csrf


                        <label class="flex items-start gap-3 p-4 rounded-lg bg-white border border-slate-200 cursor-pointer">

                            <input
                                type="checkbox"
                                name="pernyataan"
                                value="1"
                                required
                                class="mt-0.5 w-4 h-4 accent-navy"
                            >

                            <span class="text-xs text-slate-600 leading-relaxed">

                                Saya menyatakan bahwa seluruh data dan dokumen
                                yang saya submit adalah benar dan dapat
                                dipertanggungjawabkan.

                            </span>

                        </label>


                        @if(session('error'))

                            <div class="mt-3 text-xs text-red-600 font-semibold">
                                {{ session('error') }}
                            </div>

                        @endif


                        @if($errors->any())

                            <div class="mt-3 text-xs text-red-600 font-semibold">

                                {{ $errors->first() }}

                            </div>

                        @endif


                        {{-- ACTION --}}

                        <div class="flex items-center justify-between mt-4">

                            <a
                                href="{{ route('mahasiswa.pengajuan.upload', $pengajuan->id) }}"
                                class="h-11 px-4 flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-navy"
                            >

                                <i data-lucide="arrow-left" class="w-4 h-4"></i>

                                Kembali

                            </a>


                            @if($semuaDokumenWajibLengkap)

                                <button
                                    type="submit"
                                    class="h-11 px-6 rounded-lg bg-navy hover:bg-navy-dark text-white flex items-center gap-2 text-xs font-bold transition-colors"
                                >

                                    Finalisasi Pengajuan

                                    <i
                                        data-lucide="send"
                                        class="w-4 h-4"
                                    ></i>

                                </button>

                            @else

                                <button
                                    type="button"
                                    disabled
                                    class="h-11 px-6 rounded-lg bg-slate-200 text-slate-400 flex items-center gap-2 text-xs font-bold cursor-not-allowed"
                                >

                                    Finalisasi Pengajuan

                                    <i
                                        data-lucide="send"
                                        class="w-4 h-4"
                                    ></i>

                                </button>

                            @endif

                        </div>

                    </form>

                </div>


                {{-- RIGHT --}}

                <aside class="space-y-4">


                    {{-- SUMMARY --}}

                    <section class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                        <div class="flex items-center justify-between mb-5">

                            <h2 class="text-sm font-extrabold text-navy">
                                Ringkasan Pengajuan
                            </h2>

                            <span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase">
                                {{ ucfirst($pengajuan->jalur) }}
                            </span>

                        </div>


                        <div class="space-y-4">

                            <div>

                                <p class="text-[10px] uppercase font-bold text-slate-400">
                                    Beasiswa
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $pengajuan->nama_program }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase font-bold text-slate-400">
                                    Periode
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $pengajuan->nama_periode }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase font-bold text-slate-400">
                                    Dokumen Wajib
                                </p>

                                <p class="text-xs font-bold text-slate-800 mt-1">
                                    {{ $dokumenWajib->count() }} dokumen
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase font-bold text-slate-400">
                                    Kelengkapan
                                </p>

                                @if($semuaDokumenWajibLengkap)

                                    <span class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-green-600">

                                        <i data-lucide="check-circle" class="w-4 h-4"></i>

                                        Lengkap

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1 mt-1 text-xs font-bold text-red-500">

                                        <i data-lucide="circle-alert" class="w-4 h-4"></i>

                                        Belum lengkap

                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="border-t border-slate-200 my-5"></div>


                        <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-100">

                            <p class="text-[11px] text-slate-600 leading-relaxed">

                                Periksa kembali seluruh data dan dokumen
                                sebelum melakukan finalisasi.

                            </p>

                        </div>

                    </section>

                </aside>

            </div>

        </div>

    </main>

</div>


<script>
    lucide.createIcons();
</script>

</body>
</html>