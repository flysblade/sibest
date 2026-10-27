<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User — SIBEST Admin</title>
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
        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center gap-3">
            <a href="{{ route('admin.user.index') }}"
               class="w-9 h-9 rounded-lg border border-border-light flex items-center justify-center text-navy hover:bg-gray-50">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-lg font-extrabold text-navy">Tambah User</h1>
                <p class="text-xs text-muted">Buat akun baru</p>
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

            <form method="POST" action="{{ route('admin.user.store') }}"
                  class="bg-white rounded-xl border border-border-light p-5 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required
                               class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Role</label>
                        <select name="role" required
                                class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            <option value="">Pilih role...</option>
                            <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="penyeleksi" @selected(old('role') === 'penyeleksi')>Penyeleksi</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Username (NIM/PMB/ID)</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                               class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Password</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-border-light">
                    <a href="{{ route('admin.user.index') }}"
                       class="h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-muted hover:bg-gray-50 inline-flex items-center">
                        Batal
                    </a>
                    <button type="submit"
                            class="h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark inline-flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        Simpan User
                    </button>
                </div>
            </form>
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