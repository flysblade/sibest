<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ajukan {{ $beasiswa->nama_program }} — SIBEST UNUJA</title>

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

<body class="bg-bg-page text-dark-text font-sans">

@php

    $tanggalBuka = $beasiswa->tanggal_buka
        ? \Carbon\Carbon::parse($beasiswa->tanggal_buka)
        : null;

    $tanggalTutup = $beasiswa->tanggal_tutup
        ? \Carbon\Carbon::parse($beasiswa->tanggal_tutup)
        : null;

    $nim = $user->nim ?? $user->username ?? '-';

    $namaProdi = $profile->prodi ?? '-';
    $angkatan = $profile->angkatan ?? '-';
    $noHp = $profile->no_hp ?? '-';

    $dokumenWajib = $dokumen->where('required', 1);
    $dokumenOpsional = $dokumen->where('required', 0);

@endphp


<div class="min-h-screen flex">

        {{-- SIDEBAR --}}

        @include('partials.sidebar-mahasiswa')

        {{-- NAVIGATION --}}
        <nav class="flex-1 p-4 space-y-1">

            <p class="px-3 pt-2 pb-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Menu Utama
            </p>


            <a
                href="{{ route('mahasiswa.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-200 hover:bg-white/5 font-medium text-sm"
            >
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>


            <a
                href="{{ route('mahasiswa.beasiswa.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-200 hover:bg-white/5 font-medium text-sm"
            >
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                <span>Daftar Beasiswa</span>
            </a>


            {{-- ACTIVE --}}
            <a
                href="{{ route('mahasiswa.pengajuan.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-tr-lg rounded-br-lg border-l-[3px] border-gold bg-cyan-900 pl-[9px] text-white font-bold text-sm"
            >
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Pengajuan</span>
            </a>


            <a
                href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-200 hover:bg-white/5 font-medium text-sm"
            >
                <i data-lucide="route" class="w-5 h-5"></i>
                <span>Tracking</span>
            </a>


            <a
                href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-200 hover:bg-white/5 font-medium text-sm"
            >
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Riwayat</span>
            </a>


            <a
                href="#"
                class="flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-200 hover:bg-white/5 font-medium text-sm"
            >
                <span class="flex items-center gap-3">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                    <span>Pengumuman</span>
                </span>

                <span class="min-w-5 h-5 px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold inline-flex items-center justify-center">
                    2
                </span>
            </a>

        </nav>


        {{-- ACCOUNT --}}
        <div class="p-4 border-t border-white/10 relative">

            <button
                type="button"
                id="accountMenuButton"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/5 text-left transition-colors"
            >

                <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr($user->nama, 0, 2)) }}
                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-bold text-white truncate">
                        {{ $user->nama }}
                    </p>

                    <p class="text-xs text-slate-300 truncate">
                        {{ $user->username ?? $user->nim ?? '-' }}
                    </p>

                </div>

                <i
                    data-lucide="chevron-up"
                    id="accountMenuChevron"
                    class="w-4 h-4 text-slate-300 shrink-0"
                ></i>

            </button>


            <div
                id="accountMenu"
                class="hidden absolute left-4 right-4 bottom-[72px] bg-white rounded-xl border border-border-light shadow-lg overflow-hidden z-50"
            >

                <a
                    href="#"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <i data-lucide="user-round" class="w-4 h-4 text-navy"></i>
                    Profil
                </a>


                <a
                    href="#"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <i data-lucide="settings" class="w-4 h-4 text-navy"></i>
                    Pengaturan
                </a>


                <div class="border-t border-border-light"></div>


                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 text-left"
                    >
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar
                    </button>

                </form>

            </div>

        </div>

    </aside>


    {{-- MAIN --}}
    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">


        {{-- TOPBAR --}}
        <header class="min-h-20 px-5 sm:px-7 lg:px-9 py-4 bg-white border-b border-gray-200 flex items-center">
            <div>
                <h1 class="text-xl font-extrabold text-navy">
                    Form Pengajuan Beasiswa
                </h1>

                <p class="text-xs text-gray-500 mt-1">
                    Lengkapi data diri dan pengajuan beasiswamu
                </p>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-5 sm:p-8">


            {{-- BREADCRUMB --}}
            <div class="flex items-center gap-1 text-xs mb-5">

                <a
                    href="{{ route('mahasiswa.beasiswa.index') }}"
                    class="text-gray-500 font-semibold hover:text-navy"
                >
                    Daftar Beasiswa
                </a>

                <span class="text-gray-400">›</span>

                <span class="text-gray-500">
                    {{ $beasiswa->nama_program }}
                </span>

                <span class="text-gray-400">›</span>

                <span class="text-navy font-bold">
                    Ajukan
                </span>

            </div>


            {{-- PAGE HEADER --}}
            <div class="mb-6">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-navy">
                        <span class="h-2 w-2 rounded-full bg-gold"></span>
                        SIBEST · UNUJA
                    </span>

                    <span class="text-xs text-gray-400">Pengajuan Beasiswa</span>
                </div>

                <h2 class="text-2xl font-semibold tracking-tight text-navy">
                    Form Pengajuan Beasiswa
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-500">
                    Lengkapi dan periksa informasi pengajuan beasiswa sebelum melanjutkan
                    ke tahap unggah dokumen.
                </p>
            </div>


            {{-- STEPPER --}}
            <section class="mb-6 rounded-xl border border-border-light bg-white px-5 py-5 sm:px-7">
                <div class="grid grid-cols-3">
                    {{-- STEP 1 --}}
                    <div class="relative">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy text-sm font-bold text-white ring-4 ring-blue-50">
                                1
                            </div>

                            <div>
                                <p class="text-xs font-bold text-navy sm:text-sm">Isi Data</p>
                                <p class="mt-0.5 hidden text-[11px] text-gray-500 sm:block">Data pengajuan</p>
                            </div>
                        </div>

                        <div class="absolute left-10 right-2 top-[18px] -z-0 h-0.5 bg-gold sm:left-12"></div>
                    </div>

                    {{-- STEP 2 --}}
                    <div class="relative pl-2 sm:pl-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-gray-200 bg-white text-sm font-semibold text-gray-400">
                                2
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-500 sm:text-sm">Upload Dokumen</p>
                                <p class="mt-0.5 hidden text-[11px] text-gray-400 sm:block">Dokumen persyaratan</p>
                            </div>
                        </div>

                        <div class="absolute left-12 right-0 top-[18px] -z-0 h-0.5 bg-gray-200 sm:left-16"></div>
                    </div>

                    {{-- STEP 3 --}}
                    <div class="pl-2 sm:pl-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-gray-200 bg-white text-sm font-semibold text-gray-400">
                                3
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-500 sm:text-sm">Review</p>
                                <p class="mt-0.5 hidden text-[11px] text-gray-400 sm:block">Finalisasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            {{-- MAIN GRID --}}
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-6">


                {{-- LEFT --}}
                <div class="space-y-6">


                    {{-- DATA DIRI --}}
                    <section class="overflow-hidden rounded-xl border border-border-light bg-white">
                        <div class="border-b border-border-light px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-navy">
                                    <i data-lucide="user-round" class="h-5 w-5"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-navy">Data Diri</h2>
                                    <p class="mt-1 text-xs text-muted">Informasi dari profil akun SIBEST</p>
                                </div>

                                <span class="ml-auto hidden items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-500 sm:inline-flex">
                                    <i data-lucide="lock-keyhole" class="h-3 w-3"></i>
                                    Data Profil
                                </span>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-x-5 gap-y-5 md:grid-cols-2 xl:grid-cols-3">
                                @foreach([
                                    ['Nama Lengkap', $user->nama, 'user-round'],
                                    ['NIM', $nim, 'id-card'],
                                    ['Program Studi', $namaProdi, 'graduation-cap'],
                                    ['Email', $user->email, 'mail'],
                                    ['Angkatan', $angkatan, 'calendar'],
                                    ['No. HP', $noHp, 'phone'],
                                ] as $field)
                                    <div class="min-w-0">
                                        <label class="mb-2 block text-[11px] font-semibold text-gray-500">
                                            {{ $field[0] }}
                                        </label>

                                        <div class="flex min-h-[46px] items-center gap-2.5 rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-3">
                                            <i data-lucide="{{ $field[2] }}" class="h-4 w-4 shrink-0 text-gray-400"></i>

                                            <p class="min-w-0 break-words text-xs font-medium leading-5 text-gray-700">
                                                {{ $field[1] ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-5 flex items-start gap-3 rounded-lg border border-blue-100 bg-blue-50/70 p-4">
                                <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0 text-navy"></i>

                                <p class="text-xs leading-5 text-gray-600">
                                    Data diri diambil dari profil akun kamu. Pastikan informasi profil sudah benar
                                    sebelum melanjutkan pengajuan.
                                </p>
                            </div>
                        </div>
                    </section>


                    {{-- DATA PENGAJUAN --}}
                    <section class="overflow-hidden rounded-xl border border-border-light bg-white">
                        <div class="border-b border-border-light px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-navy">
                                    <i data-lucide="file-text" class="h-5 w-5"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-navy">Data Pengajuan</h2>
                                    <p class="mt-1 text-xs text-muted">Periksa detail beasiswa yang dipilih</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="mb-5 flex items-start gap-3 rounded-lg border border-amber-100 bg-amber-50/70 p-4">
                                <i data-lucide="clipboard-check" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"></i>

                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Periksa pilihan beasiswa kamu</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-600">
                                        Pastikan program, periode, dan jalur yang dipilih sudah sesuai sebelum melanjutkan.
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="rounded-lg border border-border-light p-4">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                                        <p class="text-[11px] font-semibold">Nama Beasiswa</p>
                                    </div>

                                    <p class="mt-2 text-sm font-semibold leading-5 text-navy">
                                        {{ $beasiswa->nama_program }}
                                    </p>
                                </div>

                                <div class="rounded-lg border border-border-light p-4">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i data-lucide="calendar-range" class="h-4 w-4"></i>
                                        <p class="text-[11px] font-semibold">Periode</p>
                                    </div>

                                    <p class="mt-2 text-sm font-semibold text-navy">
                                        {{ $beasiswa->nama_periode ?: 'Belum ditentukan' }}
                                    </p>
                                </div>

                                <div class="rounded-lg border border-border-light p-4">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i data-lucide="git-branch" class="h-4 w-4"></i>
                                        <p class="text-[11px] font-semibold">Jalur Beasiswa</p>
                                    </div>

                                    <p class="mt-2 text-sm font-semibold text-navy">
                                        {{ ucfirst($beasiswa->jalur ?? '-') }}
                                    </p>
                                </div>

                                <div class="rounded-lg border border-border-light p-4">
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <i data-lucide="calendar-clock" class="h-4 w-4"></i>
                                        <p class="text-[11px] font-semibold">Batas Pendaftaran</p>
                                    </div>

                                    <p class="mt-2 text-sm font-semibold text-navy">
                                        {{ $tanggalTutup ? $tanggalTutup->translatedFormat('d M Y') : 'Jadwal menyusul' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-dashed border-gray-200 pt-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-navy">Persyaratan dokumen</p>
                                        <p class="mt-1 text-xs text-gray-500">Daftar lengkap tersedia pada langkah berikutnya.</p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <p class="text-lg font-semibold text-navy">
                                            {{ $dokumenWajib->count() }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">Dokumen wajib</p>
                                    </div>
                                </div>

                                @if($dokumenOpsional->count() > 0)
                                    <p class="mt-3 text-xs text-gray-500">
                                        {{ $dokumenOpsional->count() }} dokumen tambahan bersifat opsional.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </section>


                    {{-- ACTION --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-border-light pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <a
                            href="{{ route('mahasiswa.beasiswa.show', $beasiswa->id) }}"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-border-light bg-white px-5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50"
                        >
                            <i data-lucide="arrow-left" class="h-4 w-4"></i>
                            Batal
                        </a>

                        <form
                            action="{{ route('mahasiswa.pengajuan.store', $beasiswa->id) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-navy px-6 text-xs font-semibold text-white shadow-sm transition hover:bg-navy-dark focus:outline-none focus:ring-2 focus:ring-navy focus:ring-offset-2 sm:w-auto"
                            >
                                Simpan & Lanjut
                                <i data-lucide="arrow-right" class="h-4 w-4"></i>
                            </button>
                        </form>
                    </div>

                </div>


                {{-- RIGHT --}}
                <aside>

                    <section class="rounded-xl border border-border-light bg-white p-5 xl:sticky xl:top-6">
                        <div class="flex items-center justify-between mb-4">

                            <h2 class="text-base font-semibold text-navy">
                                Ringkasan Pengajuan
                            </h2>

                            <span class="px-2 py-1 rounded-full bg-blue-50 text-navy text-[10px] font-extrabold uppercase">
                                {{ ucfirst($beasiswa->jalur) }}
                            </span>

                        </div>


                        <div class="space-y-4">

                            <div class="flex items-start gap-2.5">

                                <i data-lucide="graduation-cap" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Beasiswa
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $beasiswa->nama_program }}
                                    </p>

                                </div>

                            </div>


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


                            <div class="flex items-start gap-2.5">

                                <i data-lucide="files" class="w-[17px] h-[17px] text-gray-500 shrink-0 mt-0.5"></i>

                                <div>

                                    <p class="text-[10px] font-bold text-gray-500 uppercase">
                                        Dokumen
                                    </p>

                                    <p class="text-xs font-bold text-navy mt-0.5">
                                        {{ $dokumenWajib->count() }} wajib

                                        @if($dokumenOpsional->count() > 0)
                                            · {{ $dokumenOpsional->count() }} tambahan
                                        @endif
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="border-t border-border-light my-4"></div>


                        <div class="mt-4 flex items-start gap-2.5 rounded-lg border border-amber-100 bg-amber-50 p-3.5">
                            <i data-lucide="triangle-alert"
                            class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"></i>

                            <p class="text-xs leading-5 text-gray-600">
                                Periksa kembali data profil dan pilihan beasiswa sebelum melanjutkan ke tahap upload dokumen.
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