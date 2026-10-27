<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User — SIBEST Admin</title>
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
            <div>
                <h1 class="text-xl font-extrabold text-navy">Kelola User</h1>
                <p class="text-xs text-muted mt-1">Kelola akun mahasiswa, admin, dan penyeleksi</p>
            </div>
            <a href="{{ route('admin.user.create') }}"
               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah User
            </a>
        </header>

        <div class="p-5 sm:p-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-5 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Filter --}}
            <form method="GET" action="{{ route('admin.user.index') }}"
                  class="bg-white rounded-xl border border-border-light p-5 mb-5">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="w-full sm:w-[180px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Role</label>
                        <select name="role" onchange="this.form.submit()"
                                class="w-full h-10 px-3 rounded-md border border-border-light bg-white text-sm text-navy">
                            <option value="semua" @selected(request('role', 'semua') === 'semua')>Semua</option>
                            <option value="mahasiswa" @selected(request('role') === 'mahasiswa')>Mahasiswa</option>
                            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                            <option value="penyeleksi" @selected(request('role') === 'penyeleksi')>Penyeleksi</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[240px]">
                        <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Cari</label>
                        <div class="h-10 flex items-center gap-2 px-3 rounded-md border border-border-light bg-white">
                            <i data-lucide="search" class="w-4 h-4 text-muted shrink-0"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama / username / email..."
                                   class="flex-1 outline-none text-sm text-navy placeholder:text-muted">
                        </div>
                    </div>

                    @if(request()->anyFilled(['role', 'search']))
                        <a href="{{ route('admin.user.index') }}"
                           class="inline-flex items-center gap-1.5 h-10 px-3 rounded-md border border-border-light bg-white text-xs font-bold text-muted hover:bg-gray-50">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="text-xs font-semibold text-muted mb-3">
                Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
            </div>

            @if($users->isEmpty())
                <div class="bg-white rounded-xl border border-border-light p-12 text-center">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-extrabold text-navy mt-4">Tidak ada user</h3>
                </div>
            @else
                <div class="bg-white rounded-xl border border-border-light overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[800px]">
                            <thead class="bg-gray-50 border-b border-border-light">
                                <tr>
                                    <th class="text-left px-5 py-3 text-[11px] font-bold uppercase text-muted">Nama</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Username</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Email</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Role</th>
                                    <th class="text-left px-3 py-3 text-[11px] font-bold uppercase text-muted">Dibuat</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-bold uppercase text-muted">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-light">
                                @foreach($users as $u)
                                    @php
                                        $roleConfig = match($u->role) {
                                            'mahasiswa'  => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',   'label' => 'Mahasiswa'],
                                            'admin'      => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700', 'label' => 'Admin'],
                                            'penyeleksi' => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',  'label' => 'Penyeleksi'],
                                            default      => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'label' => ucfirst($u->role)],
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-navy">{{ $u->nama }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-sm text-dark-text font-medium">{{ $u->username }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-xs text-muted truncate">{{ $u->email }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold {{ $roleConfig['bg'] }} {{ $roleConfig['text'] }}">
                                                {{ $roleConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-xs text-muted">
                                                {{ \Carbon\Carbon::parse($u->created_at)->translatedFormat('d M Y') }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('admin.user.edit', $u->id) }}"
                                               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md bg-navy text-white text-[11px] font-bold hover:bg-navy-dark">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="px-5 py-4 border-t border-border-light bg-gray-50">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            @endif
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