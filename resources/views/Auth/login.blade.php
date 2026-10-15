<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Masuk — SIBEST UNUJA</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Konfigurasi warna SIBEST -->
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
                        error: '#D32F2F',
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

    <!-- Plus Jakarta Sans -->
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

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', Inter, sans-serif;
        }

        .input-field:focus {
            outline: none;
            border-color: #1B2F5B;
            box-shadow: 0 0 0 3px rgba(27, 47, 91, 0.12);
        }

        .btn-masuk:hover {
            background-color: #152447;
        }

        .btn-masuk:active {
            background-color: #0f1a33;
            transform: scale(0.99);
        }
    </style>
</head>


<body class="bg-bg-page min-h-screen font-jakarta">

    <div class="flex min-h-screen">

        <!-- =================================
             PANEL KIRI
        ================================== -->

        <div class="hidden lg:flex lg:w-[58%] relative flex-col justify-between overflow-hidden">

            <!-- Foto gedung -->
            <div class="absolute inset-0">

                <img
                    src="{{ asset('assets/mohondiubah.jpg') }}"
                    alt="Universitas Nurul Jadid"
                    class="w-full h-full object-cover"
                >

                <!-- Overlay navy -->
                <div
                    class="absolute inset-0"
                    style="background: rgba(27, 47, 91, 0.78);"
                ></div>

            </div>


            <!-- Konten branding -->
            <div class="relative z-10 p-12">

                <div class="flex items-center gap-4 mb-5">

                    <img
                        src="{{ asset('assets/logo.jpg') }}"
                        alt="Logo UNUJA"
                        class="w-14 h-14 rounded-full object-cover flex-shrink-0"
                    >

                    <div>

                        <p class="text-white text-2xl font-extrabold leading-tight">
                            SIBEST
                        </p>

                        <p class="text-gold text-xs font-bold tracking-wide uppercase">
                            Sistem Informasi Beasiswa Terpadu
                        </p>

                    </div>

                </div>


                <!-- Gold accent -->
                <div class="w-20 h-0.5 bg-gold mb-5"></div>


                <p class="text-slate-200 text-sm font-medium leading-relaxed">
                    Pusat Informasi &amp; Pengajuan Beasiswa UNUJA
                </p>

            </div>


            <!-- Teks bawah -->
            <div class="relative z-10 p-12">

                <p class="text-white text-[15px] font-semibold leading-relaxed">
                    Temukan peluang beasiswa,<br>
                    ajukan dengan mudah,<br>
                    dan pantau prosesnya dalam satu platform.
                </p>

            </div>

        </div>


        <!-- =================================
             PANEL KANAN
        ================================== -->

        <div class="w-full lg:w-[42%] flex flex-col justify-between bg-white px-8 py-10 sm:px-16">

            <!-- Branding mobile -->
            <div class="flex lg:hidden items-center gap-3 mb-8">

                <img
                    src="{{ asset('assets/logo.jpg') }}"
                    alt="Logo UNUJA"
                    class="w-9 h-9 rounded-full object-cover"
                >

                <div>

                    <p class="text-navy font-extrabold text-base leading-none">
                        SIBEST
                    </p>

                    <p class="text-gold text-[10px] font-bold uppercase tracking-wide">
                        UNUJA
                    </p>

                </div>

            </div>


            <!-- Form area -->
            <div class="flex-1 flex items-center justify-center">

                <div class="w-full max-w-[420px]">

                    <!-- Eyebrow -->
                    <p class="text-gold text-xs font-bold uppercase tracking-widest mb-2">
                        SIBEST • UNUJA
                    </p>


                    <!-- Heading -->
                    <h1 class="text-dark-text text-3xl font-extrabold mb-2">
                        Masuk ke SIBEST
                    </h1>

                    <p class="text-muted text-sm font-medium leading-relaxed mb-8">
                        Gunakan akun civitas akademika Anda untuk melanjutkan.
                    </p>

                    <!-- Success setelah register -->
                    @if (session('success'))
                        <div class="flex items-center gap-2 mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                            <i data-lucide="circle-check" class="w-4 h-4 text-green-700 flex-shrink-0"></i>
                            <p class="text-green-700 text-sm font-semibold">
                                {{ session('success') }}
                            </p>
                        </div>
                    @endif


                    <!-- ERROR -->
                    @if ($errors->has('username'))

                        <div class="flex items-center gap-2 mb-4">

                            <i
                                data-lucide="alert-circle"
                                class="w-4 h-4 text-error flex-shrink-0"
                            ></i>

                            <p class="text-error text-sm font-semibold">
                                {{ $errors->first('username') }}
                            </p>

                        </div>

                    @endif


                    <!-- FORM -->
                    <form
                        id="loginForm"
                        method="POST"
                        action="{{ route('login.attempt') }}"
                    >

                        @csrf


                        <!-- Username -->
                        <div class="mb-5">

                            <label
                                for="username"
                                class="block text-dark-text text-sm font-semibold mb-2"
                            >
                                Identitas Pengguna
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted">

                                    <i
                                        data-lucide="user"
                                        class="w-5 h-5"
                                    ></i>

                                </span>

                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="NIM / NIDN / Username"
                                    autocomplete="username"
                                    required
                                    autofocus
                                    class="input-field w-full h-12 pl-11 pr-4 border border-border-light rounded-lg text-dark-text text-sm placeholder-muted transition-all duration-200"
                                >

                            </div>

                        </div>


                        <!-- Password -->
                        <div class="mb-5">

                            <label
                                for="password"
                                class="block text-dark-text text-sm font-semibold mb-2"
                            >
                                Kata Sandi
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted">

                                    <i
                                        data-lucide="lock"
                                        class="w-5 h-5"
                                    ></i>

                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan kata sandi"
                                    autocomplete="current-password"
                                    required
                                    class="input-field w-full h-12 pl-11 pr-12 border border-border-light rounded-lg text-dark-text text-sm placeholder-muted transition-all duration-200"
                                >


                                <!-- Tombol lihat password -->
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-navy transition-colors"
                                    aria-label="Tampilkan kata sandi"
                                >

                                    <i
                                        data-lucide="eye"
                                        class="w-5 h-5"
                                    ></i>

                                </button>

                            </div>

                        </div>


                        <!-- Tombol login -->
                        <button
                            type="submit"
                            id="loginButton"
                            class="btn-masuk w-full h-12 bg-navy hover:bg-navy-dark active:bg-slate-900 active:scale-[0.99] disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:scale-100 text-white text-base font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-3"
                        >
                            <i
                                data-lucide="loader-circle"
                                id="loginSpinner"
                                class="hidden w-[18px] h-[18px] animate-spin"
                            ></i>
                            <span id="loginButtonText">Masuk</span>
                        </button>


                        <!-- Register -->
                        <p class="text-center text-sm text-muted mt-4">

                            Belum punya akun?

                            <a
                                href="{{ route('register') }}"
                                class="text-navy font-bold underline hover:text-navy-dark"
                            >
                                Daftar di sini
                            </a>

                        </p>

                    </form>

                </div>

            </div>


            <!-- Footer -->
            <div class="w-full max-w-[420px] mx-auto border-t border-border-light pt-4 mt-6">

                <p class="text-center text-xs text-muted font-medium">
                    Hanya untuk civitas akademika Universitas Nurul Jadid
                </p>

            </div>

        </div>

    </div>


    <!-- =================================
         JAVASCRIPT
    ================================== -->

    <script>

        // Render semua icon Lucide
        lucide.createIcons();


        // Ambil tombol toggle password
        const toggleButton =
            document.getElementById('togglePassword');


        // Ambil input password
        const passwordInput =
            document.getElementById('password');


        // Status awal password
        let isVisible = false;


        // Saat tombol eye diklik
        toggleButton.addEventListener('click', function () {

            // Balik status password
            isVisible = !isVisible;


            // Tentukan tipe input
            passwordInput.type =
                isVisible
                    ? 'text'
                    : 'password';


            // Ganti icon eye
            toggleButton.innerHTML =
                isVisible
                    ? '<i data-lucide="eye-off" class="w-5 h-5"></i>'
                    : '<i data-lucide="eye" class="w-5 h-5"></i>';


            // Render icon yang baru
            lucide.createIcons();

        });


        // Loading state saat form login dikirim
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const loginButtonText = document.getElementById('loginButtonText');
        const loginSpinner = document.getElementById('loginSpinner');

        loginForm.addEventListener('submit', function () {
            loginButton.disabled = true;
            loginButtonText.textContent = 'Memverifikasi...';
            loginSpinner.classList.remove('hidden');
            lucide.createIcons();
        });

    </script>

</body>

</html>