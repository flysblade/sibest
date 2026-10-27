<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen - SIBEST</title>

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
                        jakarta: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-bg-page text-dark-text font-jakarta">

<div class="min-h-screen flex">

    <!-- ================ SIDEBAR =================== -->
        @include('partials.sidebar-mahasiswa')
    <!-- ===================================================== -->
    <!-- MAIN -->
    <!-- ===================================================== -->

    <main
        id="mainContent"
        class="w-full lg:ml-64 transition-all duration-300"
    >

        <!-- Topbar -->
        <header
            class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between"
        >

            <!-- Breadcrumb -->
            <div class="flex items-center min-w-0">
                @include('partials.mobile-menu')
                <div class="min-w-0">

                <div class="text-xs text-slate-400 truncate">

                    Pengajuan

                    <span class="mx-1">
                        ›
                    </span>

                    {{ $pengajuan->nama_program }}
                    {{ $pengajuan->nama_periode }}

                    <span class="mx-1">
                        ›
                    </span>

                    Upload Dokumen

                    </div>
                </div>

            </div>


            <!-- User Header -->
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


        <!-- Content -->
        <div class="p-5 sm:p-8">

            <!-- Page Header -->
            <div class="mb-7">

                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Upload Dokumen
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $pengajuan->nama_program }}
                    {{ $pengajuan->nama_periode }}
                    — Langkah 2 dari 3
                </p>

            </div>


            <!-- Stepper -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">

                <div class="flex items-center">

                    <!-- Step 1 -->
                    <div class="flex items-center">

                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center">
                            <i
                                data-lucide="check"
                                class="w-4 h-4"
                            ></i>
                        </div>

                        <div class="ml-2">

                            <div class="text-xs font-bold text-slate-900">
                                Isi Data
                            </div>

                            <div class="text-[10px] text-slate-400">
                                Selesai
                            </div>

                        </div>

                    </div>


                    <div class="flex-1 h-px bg-navy mx-4"></div>


                    <!-- Step 2 -->
                    <div class="flex items-center">

                        <div class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center text-xs font-bold">
                            2
                        </div>

                        <div class="ml-2">

                            <div class="text-xs font-bold text-navy">
                                Upload Dokumen
                            </div>

                            <div class="text-[10px] text-slate-400">
                                Sedang dikerjakan
                            </div>

                        </div>

                    </div>


                    <div class="flex-1 h-px bg-slate-200 mx-4"></div>


                    <!-- Step 3 -->
                    <div class="flex items-center">

                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold">
                            3
                        </div>

                        <div class="ml-2">

                            <div class="text-xs font-semibold text-slate-400">
                                Review & Finalisasi
                            </div>

                            <div class="text-[10px] text-slate-400">
                                Belum
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Dokumen -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-sm font-bold text-slate-900">
                        Dokumen Persyaratan
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Upload dokumen sesuai persyaratan beasiswa.
                    </p>

                    {{-- INFO KHUSUS KIP-K --}}
                    @if(strtolower($pengajuan->nama_program) === 'kip-k')
                        <div class="mt-3 p-3 rounded-lg bg-blue-50 border border-blue-100 flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-navy shrink-0 mt-0.5"></i>
                            <div class="text-xs text-slate-600 leading-relaxed">
                                <strong class="text-navy">KIP-K:</strong> Wajib upload <strong>Bukti Pendaftaran KIP-K</strong> dari portal pusat.
                                Dokumen lain bersifat opsional — akan diverifikasi fisik saat wawancara di kampus.
                            </div>
                        </div>
                    @endif

                </div>


                <!-- List Dokumen -->
                <div class="divide-y divide-slate-100">

                    @forelse($dokumen as $item)

                        <div class="px-6 py-5 flex items-center justify-between gap-6">

                            <!-- Info Dokumen -->
                            <div class="flex items-start gap-4 min-w-0">

                                <div class="w-10 h-10 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center">

                                    <i
                                        data-lucide="file-text"
                                        class="w-5 h-5 text-slate-500"
                                    ></i>

                                </div>


                                <div class="min-w-0">

                                    <div class="flex items-center gap-2 flex-wrap">

                                        <h3 class="text-sm font-semibold text-slate-900">
                                            {{ $item->nama_dokumen }}
                                        </h3>


                                        @if($item->required)

                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-600">
                                                Wajib
                                            </span>

                                        @else

                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                                Opsional
                                            </span>

                                        @endif

                                    </div>


                                    @if($item->keterangan)

                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $item->keterangan }}
                                        </p>

                                    @endif


                                    <!-- Format File -->
                                    <p class="text-xs text-muted mt-1">

                                        {{ str_contains(strtolower($item->nama_dokumen), 'foto')
                                            ? 'JPG, JPEG, PNG — Maks. 2MB'
                                            : 'PDF — Maks. 2MB' }}

                                    </p>


                                    <!-- File Sudah Upload -->
                                    @if($item->original_filename)

                                        <div class="mt-2 flex items-center gap-2 text-xs text-green-600">

                                            <i
                                                data-lucide="check-circle"
                                                class="w-4 h-4"
                                            ></i>

                                            <span class="truncate max-w-md">
                                                {{ $item->original_filename }}
                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </div>


                            <!-- Form Upload -->
                            <form
                                action="{{ route('mahasiswa.pengajuan.upload.store', $pengajuan->id) }}"
                                method="POST"
                                enctype="multipart/form-data"
                                class="shrink-0"
                            >

                                @csrf


                                <input
                                    type="hidden"
                                    name="id_template"
                                    value="{{ $item->id }}"
                                >


                                <label
                                    class="cursor-pointer inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors"
                                >

                                    <i
                                        data-lucide="upload"
                                        class="w-4 h-4"
                                    ></i>

                                    {{ $item->original_filename ? 'Ganti File' : 'Pilih File' }}


                                    <input
                                        type="file"
                                        name="file"
                                        class="hidden"
                                        accept="{{ str_contains(strtolower($item->nama_dokumen), 'foto') ? '.jpg,.jpeg,.png' : '.pdf' }}"
                                        onchange="this.form.submit()"
                                    >

                                </label>

                            </form>

                        </div>

                    @empty

                        <div class="px-6 py-12 text-center">

                            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-3">

                                <i
                                    data-lucide="file-x"
                                    class="w-6 h-6 text-slate-400"
                                ></i>

                            </div>

                            <p class="text-sm font-semibold text-slate-700">
                                Belum ada dokumen persyaratan
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                Tidak ada dokumen yang perlu diupload untuk pengajuan ini.
                            </p>

                        </div>

                    @endforelse

                </div>


                <!-- Warning -->
                <div class="px-6 py-4 bg-amber-50 border-t border-amber-100">

                    <div class="flex items-start gap-3">

                        <i
                            data-lucide="triangle-alert"
                            class="w-5 h-5 text-amber-600 shrink-0"
                        ></i>

                        <p class="text-xs text-amber-800 leading-relaxed">

                            @if($semuaDokumenWajibLengkap)

                                Semua dokumen wajib sudah terupload.
                                Kamu bisa melanjutkan ke tahap review.

                            @else

                                Pastikan semua dokumen wajib sudah terupload
                                sebelum melanjutkan ke tahap review.

                            @endif

                        </p>

                    </div>

                </div>


                <!-- Footer -->
                <div class="px-6 py-5 border-t border-slate-200 flex items-center justify-between">

                    <a
                        href="{{ route('mahasiswa.pengajuan.create', $pengajuan->id_offering) }}"
                        class="h-11 px-4 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 flex items-center gap-2 text-xs font-bold text-slate-700 transition-colors"
                    >

                        <i
                            data-lucide="arrow-left"
                            class="w-4 h-4"
                        ></i>

                        Kembali

                    </a>


                    @if($semuaDokumenWajibLengkap)

                        <!-- Sementara belum diarahkan ke route review -->
                        <a
                            href="{{ route('mahasiswa.pengajuan.review', $pengajuan->id) }}"
                            class="h-11 px-5 rounded-lg bg-navy hover:bg-navy-dark text-white flex items-center gap-2 text-xs font-bold transition-colors"
                        >
                            Lanjut ke Review
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>

                    @else

                        <button
                            type="button"
                            disabled
                            class="h-11 px-5 rounded-lg bg-slate-200 text-slate-400 flex items-center gap-2 text-xs font-bold cursor-not-allowed"
                        >

                            Lanjut ke Review

                            <i
                                data-lucide="arrow-right"
                                class="w-4 h-4"
                            ></i>

                        </button>

                    @endif

                </div>

            </div>

        </div>

    </main>

</div>


<!--JAVASCRIPT -->

<script>
    lucide.createIcons();
</script>
</body>
</html>