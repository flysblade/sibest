<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — SIBEST Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            navy: { DEFAULT: '#1B2F5B', dark: '#152447' }, gold: '#F5A623',
            'dark-text': '#172B4D', muted: '#6B7280', 'border-light': '#E5E7EB', 'bg-page': '#F5F7FA',
        }, fontFamily: { jakarta: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'] } } } };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-bg-page text-dark-text font-jakarta">
<div class="min-h-screen flex">
    @include('partials.sidebar-admin')

    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.user.index') }}"
                   class="w-9 h-9 rounded-lg border border-border-light flex items-center justify-center text-navy hover:bg-gray-50">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-navy">Edit User</h1>
                    <p class="text-xs text-muted mt-0.5">{{ $user->nama }}</p>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 max-w-2xl">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
                    <ul class="text-sm text-red-800 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-border-light p-5">
                <form method="POST" action="{{ route('admin.user.update', $user->id) }}">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">
                                Username / NIM / No. Pendaftaran
                            </label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            <p class="text-[11px] text-muted mt-1">
                                Untuk mahasiswa KIP-K: ganti dari Nomor Pendaftaran ke NIM setelah camaba lolos.
                            </p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Role</label>
                            <select name="role" required
                                    class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                <option value="mahasiswa" @selected(old('role', $user->role) === 'mahasiswa')>Mahasiswa</option>
                                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                                <option value="penyeleksi" @selected(old('role', $user->role) === 'penyeleksi')>Penyeleksi</option>
                            </select>
                        </div>

                        <div class="pt-3 border-t border-border-light">
                            <p class="text-xs text-muted mb-3">Kosongkan kalau gak mau ganti password.</p>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Password Baru</label>
                                    <input type="password" name="password"
                                           class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation"
                                           class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-border-light">
                        <a href="{{ route('admin.user.index') }}"
                           class="h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-muted hover:bg-gray-50 inline-flex items-center">
                            Batal
                        </a>
                        <button type="submit"
                                class="h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark inline-flex items-center gap-1.5">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) window.lucide.createIcons();
    });
</script>
</body>
</html>