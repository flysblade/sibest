<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengumuman — SIBEST Admin</title>
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
            <a href="{{ route('admin.pengumuman.index') }}"
               class="w-9 h-9 rounded-lg border border-border-light flex items-center justify-center text-navy hover:bg-gray-50">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-lg font-extrabold text-navy">Edit Pengumuman</h1>
                <p class="text-xs text-muted">{{ $pengumuman->judul }}</p>
            </div>
        </header>

        <div class="p-5 sm:p-8 max-w-3xl">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
                    <ul class="text-sm text-red-800 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pengumuman.update', $pengumuman->id) }}"
                  class="bg-white rounded-xl border border-border-light p-5 space-y-4">
                @csrf

                <div>
                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Offering Beasiswa</label>
                    <select name="id_offering" required
                            class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        @foreach($offerings as $off)
                            <option value="{{ $off->id }}" @selected($pengumuman->id_offering == $off->id)>
                                {{ $off->nama_program }} {{ $off->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required maxlength="255"
                           class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                </div>

                <div>
                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Isi</label>
                    <textarea name="isi" rows="8" required
                              class="w-full px-3 py-2 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy resize-none">{{ old('isi', $pengumuman->isi) }}</textarea>
                </div>

                <div>
                    <label class="text-[11px] font-bold uppercase text-muted mb-1.5 block">Tanggal Publish</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ $pengumuman->published_at ? \Carbon\Carbon::parse($pengumuman->published_at)->format('Y-m-d\TH:i') : '' }}"
                           class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-border-light">
                    <a href="{{ route('admin.pengumuman.index') }}"
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
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) window.lucide.createIcons();
    });
</script>
</body>
</html>