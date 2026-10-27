<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penyeleksi — SIBEST Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { DEFAULT: '#1B2F5B', dark: '#152447' },
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body class="bg-bg-page text-dark-text font-jakarta">
<div class="min-h-screen flex">
    @include('partials.sidebar-admin')

    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-navy">Kelola Penyeleksi</h1>
                <p class="text-xs text-muted mt-1">Assign penyeleksi ke offering beasiswa</p>
            </div>
        </header>

        <div class="p-5 sm:p-8 space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Info aturan --}}
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
                <i data-lucide="info" class="w-4 h-4 text-navy shrink-0 mt-0.5"></i>
                <div class="text-xs text-gray-700 leading-relaxed">
                    <strong>Aturan:</strong> 1 penyeleksi hanya boleh handle 1 offering. 
                    Kalau di-assign ke offering baru, assignment lama otomatis dicabut.
                    1 offering boleh di-handle banyak penyeleksi.
                </div>
            </div>

            {{-- List Penyeleksi --}}
            <div class="bg-white rounded-xl border border-border-light overflow-hidden">

                <div class="p-5 border-b border-border-light">
                    <h2 class="font-extrabold text-navy">Daftar Penyeleksi</h2>
                    <p class="text-xs text-muted mt-1">{{ $penyeleksis->count() }} penyeleksi terdaftar</p>
                </div>

                @if($penyeleksis->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-navy flex items-center justify-center mx-auto">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <p class="font-bold text-navy mt-4">Belum ada penyeleksi</p>
                        <p class="text-sm text-muted mt-1">Tambah user dengan role penyeleksi via Kelola User.</p>
                    </div>
                @else
                    <div class="divide-y divide-border-light">
                        @foreach($penyeleksis as $p)
                            <div class="p-5">
                                <div class="flex flex-wrap items-start justify-between gap-4">

                                    {{-- Info Penyeleksi --}}
                                    <div class="flex items-start gap-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 rounded-full bg-gold text-navy flex items-center justify-center font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($p->nama, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-navy">{{ $p->nama }}</p>
                                            <p class="text-[11px] text-muted">{{ $p->username }} · {{ $p->email }}</p>

                                            {{-- Offering assigned --}}
                                            <div class="mt-2">
                                                @if($p->offering)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-[11px] font-bold">
                                                        <i data-lucide="briefcase-business" class="w-3 h-3"></i>
                                                        {{ $p->offering->nama_program }} {{ $p->offering->nama_periode }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 text-[11px] font-bold">
                                                        <i data-lucide="minus-circle" class="w-3 h-3"></i>
                                                        Belum di-assign
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Aksi --}}
                                    <div class="flex items-center gap-2 shrink-0">

                                        {{-- Form Assign --}}
                                        <form method="POST" action="{{ route('admin.penyeleksi.assign', $p->id) }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            <select name="id_offering" required
                                                    class="h-9 px-3 rounded-md border border-border-light text-xs text-navy focus:outline-none focus:border-navy">
                                                <option value="">Pilih Offering...</option>
                                                @foreach($offerings as $off)
                                                    <option value="{{ $off->id }}"
                                                            @selected($p->offering && $p->offering->id_offering == $off->id)>
                                                        {{ $off->nama_program }} {{ $off->nama_periode }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @php
                                                $pesanKonfirmasi = $p->offering
                                                    ? 'Penyeleksi ini udah handle offering lain. Assign ke offering baru akan CABUT assignment lama. Lanjut?'
                                                    : 'Assign penyeleksi ke offering ini?';
                                            @endphp
                                            <button type="submit"
                                                    onclick='return confirm("{{ $pesanKonfirmasi }}")'
                                                    class="inline-flex items-center gap-1 h-9 px-3 rounded-md bg-navy text-white text-[11px] font-bold hover:bg-navy-dark">
                                                <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                                Assign
                                            </button>
                                        </form>

                                        {{-- Tombol Cabut --}}
                                        @if($p->offering)
                                            <form method="POST" action="{{ route('admin.penyeleksi.unassign', $p->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Cabut assignment penyeleksi {{ $p->nama }} dari {{ $p->offering->nama_program }}?')"
                                                        class="inline-flex items-center gap-1 h-9 px-3 rounded-md border border-red-200 text-red-700 text-[11px] font-bold hover:bg-red-50">
                                                    <i data-lucide="unlink" class="w-3.5 h-3.5"></i>
                                                    Cabut
                                                </button>
                                            </form>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>

</body>
</html>