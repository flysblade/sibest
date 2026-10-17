<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — SIBEST UNUJA</title>

    <!-- Tailwind CSS CDN — tetap sesuai stack SIBEST -->
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
                        error: '#DC2626',
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body class="bg-bg-page min-h-screen font-jakarta">

<div class="min-h-screen flex">

    <!-- KIRI: CAMPUS BRANDING -->
    <section class="hidden lg:flex lg:w-[58%] relative min-h-screen overflow-hidden flex-col justify-between">

        <!-- Background kampus -->
        <div class="absolute inset-0">
            <img
                src="{{ asset('assets/mohondiubah.jpg') }}"
                alt="Universitas Nurul Jadid"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-navy opacity-[0.78]"></div>
        </div>

        <!-- Branding atas -->
        <div class="relative z-10 p-12">
            <div class="flex items-center gap-4">
                <img
                    src="{{ asset('assets/logo.jpg') }}"
                    alt="Logo UNUJA"
                    class="w-14 h-14 rounded-full object-cover flex-shrink-0"
                >

                <div>
                    <p class="text-white text-3xl font-extrabold leading-tight">SIBEST</p>
                    <p class="text-gold text-xs font-bold uppercase tracking-wide">
                        Sistem Informasi Beasiswa Terpadu
                    </p>
                </div>
            </div>

            <div class="w-20 h-0.5 bg-gold mt-5"></div>

            <p class="text-slate-200 text-base font-medium leading-relaxed mt-5">
                Pusat Informasi &amp; Pengajuan Beasiswa UNUJA
            </p>
        </div>

        <!-- Supporting statement -->
        <div class="relative z-10 p-12">
            <p class="text-white/90 text-xl font-semibold leading-8 max-w-[510px]">
                Satu langkah untuk mengakses seluruh informasi dan peluang beasiswa UNUJA.
            </p>
        </div>
    </section>

    <!-- KANAN: REGISTRATION FORM -->
    <main class="w-full lg:w-[42%] min-h-screen bg-white px-8 py-8 sm:px-12 lg:px-16 flex flex-col justify-between">

        <!-- Mobile branding -->
        <div class="lg:hidden flex items-center gap-3 mb-8">
            <img
                src="{{ asset('assets/logo.jpg') }}"
                alt="Logo UNUJA"
                class="w-10 h-10 rounded-full object-cover"
            >
            <div>
                <p class="text-navy text-lg font-extrabold leading-none">SIBEST</p>
                <p class="text-gold text-[10px] font-bold uppercase tracking-wide">UNUJA</p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-[420px] py-6">

                <!-- Intro -->
                <div class="mb-8">
                    <p class="text-gold text-xs font-bold uppercase tracking-widest mb-2">
                        SIBEST • UNUJA
                    </p>

                    <h1 class="text-blue-950 text-3xl font-extrabold leading-8 mb-2">
                        Buat Akun SIBEST
                    </h1>

                    <p class="text-gray-500 text-sm font-medium leading-5">
                        Daftarkan diri sebagai mahasiswa Universitas Nurul Jadid.
                    </p>
                </div>

                <!-- Validation summary: tetap dipakai untuk error server Laravel -->
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <div class="flex items-start gap-2">
                            <i data-lucide="circle-alert" class="w-4 h-4 text-error mt-0.5 flex-shrink-0"></i>
                            <div>
                                <p class="text-error text-sm font-semibold mb-1">
                                    Pendaftaran belum bisa diproses.
                                </p>
                                <p class="text-red-700 text-xs leading-5">
                                    Periksa kembali field yang ditandai merah.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form
                    id="registerForm"
                    method="POST"
                    action="{{ route('register.store') }}"
                    novalidate
                    class="space-y-5"
                >
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama" class="block text-blue-950 text-sm font-semibold mb-2">
                            Nama Lengkap
                        </label>

                        <div class="relative">
                            <i
                                data-lucide="user-round"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
                                id="namaIcon"
                            ></i>

                            <input
                                id="nama"
                                name="nama"
                                type="text"
                                value="{{ old('nama') }}"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                maxlength="255"
                                required
                                class="w-full h-12 rounded-lg border bg-white pl-12 pr-4 text-gray-800 text-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-blue-950/10 placeholder:text-gray-400
                                @error('nama') border-red-600 ring-4 ring-red-600/10 @else border-gray-200 @enderror"
                            >
                        </div>

                        @error('nama')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Username / NIM -->
                    <div>
                        <label for="username" class="block text-blue-950 text-sm font-semibold mb-2">
                            Username (NIM / No. Pendaftaran)
                        </label>

                        <div class="relative">
                            <i
                                data-lucide="id-card"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
                            ></i>

                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                placeholder="Masukkan NIM / No. Pendaftaran"
                                autocomplete="username"
                                maxlength="100"
                                required
                                class="w-full h-12 rounded-lg border bg-white pl-12 pr-4 text-gray-800 text-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-blue-950/10 placeholder:text-gray-400
                                @error('username') border-red-600 ring-4 ring-red-600/10 @else border-gray-200 @enderror"
                            >
                        </div>

                        <p class="mt-1.5 text-xs text-gray-400">
                            Contoh: 2421500017
                        </p>

                        @error('username')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-blue-950 text-sm font-semibold mb-2">
                            Email
                        </label>

                        <div class="relative">
                            <i
                                data-lucide="mail"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
                            ></i>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email mahasiswa"
                                autocomplete="email"
                                maxlength="255"
                                required
                                class="w-full h-12 rounded-lg border bg-white pl-12 pr-4 text-gray-800 text-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-blue-950/10 placeholder:text-gray-400
                                @error('email') border-red-600 ring-4 ring-red-600/10 @else border-gray-200 @enderror"
                            >
                        </div>

                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-blue-950 text-sm font-semibold mb-2">
                            Password
                        </label>

                        <div class="relative">
                            <i
                                data-lucide="lock-keyhole"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
                            ></i>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Buat kata sandi"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="w-full h-12 rounded-lg border border-gray-200 bg-white pl-12 pr-12 text-gray-800 text-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-blue-950/10 placeholder:text-gray-400"
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-navy active:scale-95 transition-all"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i data-lucide="eye" id="passwordEye" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <p id="passwordClientError" class="hidden mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600"></p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-blue-950 text-sm font-semibold mb-2">
                            Konfirmasi Password
                        </label>

                        <div class="relative">
                            <i
                                data-lucide="lock-keyhole"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
                            ></i>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Ulangi kata sandi"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="w-full h-12 rounded-lg border border-gray-200 bg-white pl-12 pr-12 text-gray-800 text-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-blue-950/10 placeholder:text-gray-400"
                            >

                            <button
                                type="button"
                                id="togglePasswordConfirmation"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-navy active:scale-95 transition-all"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password"
                            >
                                <i data-lucide="eye" id="confirmationEye" class="w-5 h-5"></i>
                            </button>
                        </div>

                        @error('password')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror

                        <p id="confirmationClientError" class="hidden mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600"></p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3">
                        <button
                            type="submit"
                            id="submitButton"
                            class="w-full h-12 px-6 bg-blue-950 hover:bg-navy-dark active:bg-slate-900 active:scale-[0.99] text-white text-base font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-3 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:scale-100"
                        >
                            <i data-lucide="loader-circle" id="submitSpinner" class="hidden w-[18px] h-[18px] animate-spin"></i>
                            <span id="submitLabel">Daftar Sekarang</span>
                        </button>

                        <div class="mt-4 flex justify-center items-center gap-1 text-sm">
                            <span class="text-gray-500">Sudah punya akun?</span>
                            <a
                                href="{{ route('login') }}"
                                class="text-blue-950 font-bold underline hover:text-navy-dark transition-colors"
                            >
                                Masuk di sini
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="w-full max-w-[420px] mx-auto border-t border-gray-200 pt-4 mt-6">
            <p class="text-center text-xs text-gray-500 font-medium">
                Pendaftaran khusus mahasiswa Universitas Nurul Jadid
            </p>
        </footer>
    </main>
</div>

<script>
    lucide.createIcons();

    function setupPasswordToggle(buttonId, inputId, iconId) {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (!button || !input || !icon) return;

        button.addEventListener('click', function () {
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            lucide.createIcons();
        });
    }

    setupPasswordToggle('togglePassword', 'password', 'passwordEye');
    setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation', 'confirmationEye');

    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    const passwordClientError = document.getElementById('passwordClientError');
    const confirmationClientError = document.getElementById('confirmationClientError');

    function setClientError(element, message, input) {
        element.innerHTML = '<i data-lucide="circle-alert" class="w-3.5 h-3.5"></i>' + message;
        element.classList.remove('hidden');
        element.classList.add('flex');

        input.classList.remove('border-gray-200');
        input.classList.add('border-red-600', 'ring-4', 'ring-red-600/10');

        lucide.createIcons();
    }

    function clearClientError(element, input) {
        element.classList.add('hidden');
        element.classList.remove('flex');

        input.classList.remove('border-red-600', 'ring-4', 'ring-red-600/10');
        input.classList.add('border-gray-200');
    }

    password.addEventListener('input', function () {
        if (password.value.length >= 8 || password.value.length === 0) {
            clearClientError(passwordClientError, password);
        }
    });

    confirmation.addEventListener('input', function () {
        if (confirmation.value === password.value || confirmation.value.length === 0) {
            clearClientError(confirmationClientError, confirmation);
        }
    });

    form.addEventListener('submit', function (event) {
        let valid = true;

        clearClientError(passwordClientError, password);
        clearClientError(confirmationClientError, confirmation);

        if (password.value.length < 8) {
            setClientError(
                passwordClientError,
                'Password minimal 8 karakter.',
                password
            );
            valid = false;
        }

        if (password.value !== confirmation.value) {
            setClientError(
                confirmationClientError,
                'Konfirmasi password tidak sama.',
                confirmation
            );
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
            return;
        }

        // Loading state sesuai specimen Figma.
        const submitButton = document.getElementById('submitButton');
        const submitLabel = document.getElementById('submitLabel');
        const submitSpinner = document.getElementById('submitSpinner');

        submitButton.disabled = true;
        submitSpinner.classList.remove('hidden');
        submitLabel.textContent = 'Mendaftarkan...';
        lucide.createIcons();
    });
</script>

</body>
</html>