<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman — SIBEST Admin</title>
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
                <h1 class="text-xl font-extrabold text-navy">Pengumuman</h1>
                <p class="text-xs text-muted mt-1">Kelola pengumuman untuk mahasiswa</p>
            </div>
            <a href="{{ route('admin.pengumuman.create') }}"
               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Buat Pengumuman
            </a>
        </header>

        <div class="p-5 sm:p-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-border-light overflow-hidden">
                @if($pengumumans->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                        </div>
                        <p class="font-bold text-navy mt-4">Belum ada pengumuman</p>
                        <p class="text-sm text-muted mt-1">Klik "Buat Pengumuman" untuk mulai.</p>
                    </div>
                @else
                    <div class="divide-y divide-border-light">
                        @foreach($pengumumans as $p)
                            <div class="p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-bold text-navy">{{ $p->judul }}</h3>
                                        <p class="text-xs text-muted mt-1">
                                            {{ $p->nama_program }} {{ $p->nama_periode }}
                                            · {{ $p->published_at ? \Carbon\Carbon::parse($p->published_at)->translatedFormat('d M Y') : 'Draft' }}
                                        </p>
                                        <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ Str::limit(strip_tags($p->isi), 150) }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('admin.pengumuman.edit', $p->id) }}"
                                           class="inline-flex items-center gap-1 h-8 px-3 rounded-md border border-border-light text-[11px] font-bold text-navy hover:bg-gray-50">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.pengumuman.destroy', $p->id) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Hapus pengumuman ini?')"
                                                    class="inline-flex items-center gap-1 h-8 px-3 rounded-md border border-red-200 text-[11px] font-bold text-red-700 hover:bg-red-50">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($pengumumans->hasPages())
                        <div class="px-5 py-4 border-t border-border-light bg-gray-50">
                            {{ $pengumumans->links() }}
                        </div>
                    @endif
                @endif
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