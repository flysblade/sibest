<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan — SIBEST</title>

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

    @if($user->role === 'mahasiswa')
        @include('partials.sidebar-mahasiswa')
    @elseif($user->role === 'admin')
        @include('partials.sidebar-admin')
    @elseif($user->role === 'penyeleksi')
        @include('partials.sidebar-penyeleksi')
    @endif

    <main id="mainContent" class="w-full lg:ml-64 transition-all duration-300">

        <header class="h-20 bg-white border-b border-border-light px-5 sm:px-8 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-navy">Pengaturan</h1>
                <p class="text-xs text-muted mt-1">Kelola keamanan dan preferensi akun</p>
            </div>
            <a href="{{ route('profile.index') }}"
               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy hover:bg-gray-50">
                <i data-lucide="user-round" class="w-4 h-4"></i>
                Profil
            </a>
        </header>

        <div class="p-5 sm:p-8 max-w-4xl space-y-5">

            {{-- FLASH --}}
            @if(session('success_password'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success_password') }}</p>
                </div>
            @endif
            @if(session('error_password'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-red-800">{{ session('error_password') }}</p>
                </div>
            @endif
            @if(session('success_notif'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success_notif') }}</p>
                </div>
            @endif

            {{-- SECTION 1: KEAMANAN --}}
            <div class="bg-white rounded-xl border border-border-light p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-red-700 flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <h2 class="font-extrabold text-navy">Keamanan Akun</h2>
                </div>

                <form method="POST" action="{{ route('settings.updatePassword') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Password Lama</label>
                            <input type="password" name="password_lama" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Password Baru</label>
                            <input type="password" name="password_baru" required minlength="8"
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Konfirmasi</label>
                            <input type="password" name="password_baru_confirmation" required minlength="8"
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>
                    </div>
                    <p class="text-[11px] text-muted mt-2">Minimal 8 karakter.</p>

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- SECTION 2: NOTIFIKASI --}}
            <div class="bg-white rounded-xl border border-border-light p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </div>
                    <h2 class="font-extrabold text-navy">Preferensi Notifikasi</h2>
                </div>

                <form method="POST" action="{{ route('settings.updatePreferences') }}">
                    @csrf
                    <div class="space-y-3">
                        @php
                            $prefs = [
                                ['name' => 'notif_pengajuan',  'label' => 'Pengajuan Beasiswa',         'desc' => 'Notifikasi saat pengajuan beasiswa dikirim', 'checked' => $preferences->notif_pengajuan],
                                ['name' => 'notif_status',     'label' => 'Perubahan Status Pengajuan', 'desc' => 'Notifikasi saat status pengajuan berubah',    'checked' => $preferences->notif_status],
                                ['name' => 'notif_pengumuman', 'label' => 'Pengumuman Beasiswa',        'desc' => 'Notifikasi saat pengumuman beasiswa dipublikasikan', 'checked' => $preferences->notif_pengumuman],
                            ];
                        @endphp

                        @foreach($prefs as $pref)
                            <label class="flex items-center gap-4 p-3 rounded-lg border border-border-light hover:bg-gray-50 cursor-pointer">
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-navy">{{ $pref['label'] }}</p>
                                    <p class="text-[11px] text-muted">{{ $pref['desc'] }}</p>
                                </div>
                                <input type="checkbox" name="{{ $pref['name'] }}" value="1"
                                       {{ $pref['checked'] ? 'checked' : '' }}
                                       class="w-4 h-4 accent-navy">
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Preferensi
                        </button>
                    </div>
                </form>
            </div>

            {{-- SECTION 3: TELEGRAM --}}
            <div class="bg-white rounded-xl border border-border-light p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </div>
                    <h2 class="font-extrabold text-navy">Notifikasi Telegram</h2>
                </div>

                <div class="p-4 rounded-lg bg-gray-50 border border-border-light">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-navy">
                                {{ $preferences->telegram_chat_id ? 'Terhubung' : 'Belum terhubung' }}
                            </p>
                            <p class="text-[11px] text-muted">Start @SIBESTBot di Telegram untuk mendapatkan notifikasi deadline</p>
                        </div>
                    </div>

                    <div class="text-[11px] text-blue-700 bg-blue-50 border border-blue-200 rounded-md p-2 mt-2">
                        <strong>Info:</strong> Klik tombol di bawah untuk menghubungkan akun Telegram kamu dengan SIBEST.
                    </div>

                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('settings.telegram.connect') }}"
                           class="inline-flex items-center gap-2 h-9 px-3 rounded-md bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                            Hubungkan Telegram
                        </a>
                    </div>
                </div>
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