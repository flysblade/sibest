<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — SIBEST</title>

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
                <h1 class="text-xl font-extrabold text-navy">Profil</h1>
                <p class="text-xs text-muted mt-1">Kelola informasi data diri Anda</p>
            </div>
            <a href="{{ route('settings.index') }}"
               class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-border-light text-xs font-bold text-navy hover:bg-gray-50">
                <i data-lucide="settings" class="w-4 h-4"></i>
                Pengaturan
            </a>
        </header>

        <div class="p-5 sm:p-8 max-w-4xl space-y-5">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            {{-- HERO --}}
            <div class="rounded-xl p-5 text-white" style="background: linear-gradient(96deg, #102f61 0%, #183a70 58%, #2c5086 100%);">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white text-navy flex items-center justify-center font-extrabold text-xl shrink-0">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold">{{ $user->nama }}</p>
                        <p class="text-gold text-sm font-bold">{{ $user->username }}</p>
                        <p class="text-blue-100 text-xs mt-1 capitalize">{{ $user->role }}</p>
                    </div>
                </div>
            </div>

            {{-- FORM DATA DIRI --}}
            <form method="POST" action="{{ route('profile.updateData') }}" class="space-y-5">
                @csrf

                {{-- SECTION: AKUN DASAR --}}
                <div class="bg-white rounded-xl border border-border-light p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                            <i data-lucide="user-round" class="w-4 h-4"></i>
                        </div>
                        <h2 class="font-extrabold text-navy">Data Akun</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Username (NIM/PMB)</label>
                            <input type="text" value="{{ $user->username }}" disabled
                                   class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-muted bg-gray-50 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                @if($user->role === 'mahasiswa')
                    {{-- SECTION: DATA PRIBADI --}}
                    <div class="bg-white rounded-xl border border-border-light p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                                <i data-lucide="id-card" class="w-4 h-4"></i>
                            </div>
                            <h2 class="font-extrabold text-navy">Data Pribadi</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $profile->nik ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">No. HP / WhatsApp</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $profile->no_hp ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                    <option value="">Pilih...</option>
                                    <option value="L" @selected(old('jenis_kelamin', $profile->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jenis_kelamin', $profile->jenis_kelamin ?? '') === 'P')>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Agama</label>
                                <select name="agama" class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                    <option value="">Pilih...</option>
                                    @foreach(['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama', $profile->agama ?? '') === $agama)>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3"
                                          class="w-full px-3 py-2 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy resize-none"
                                          placeholder="Jalan, RT/RW, Desa, Kecamatan, Kabupaten, Provinsi">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: DATA AKADEMIK --}}
                    <div class="bg-white rounded-xl border border-border-light p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                            </div>
                            <h2 class="font-extrabold text-navy">Data Akademik</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Program Studi</label>
                                <input type="text" name="prodi" value="{{ old('prodi', $profile->prodi ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Angkatan</label>
                                <input type="number" name="angkatan" value="{{ old('angkatan', $profile->angkatan ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: DATA ORANG TUA --}}
                    <div class="bg-white rounded-xl border border-border-light p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-navy flex items-center justify-center">
                                <i data-lucide="users" class="w-4 h-4"></i>
                            </div>
                            <h2 class="font-extrabold text-navy">Data Orang Tua</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Nama Ayah</label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $profile->nama_ayah ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $profile->pekerjaan_ayah ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Nama Ibu</label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $profile->nama_ibu ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $profile->pekerjaan_ibu ?? '') }}"
                                       class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold uppercase text-muted mb-1 block">Penghasilan Orang Tua per Bulan</label>
                                <select name="penghasilan_orang_tua" class="w-full h-10 px-3 rounded-md border border-border-light text-sm text-navy focus:outline-none focus:border-navy">
                                    <option value="">Pilih...</option>
                                    @foreach(['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000'] as $range)
                                        <option value="{{ $range }}" @selected(old('penghasilan_orang_tua', $profile->penghasilan_orang_tua ?? '') === $range)>{{ $range }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- TOMBOL SIMPAN --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-11 px-5 rounded-lg bg-navy text-white text-xs font-bold hover:bg-navy-dark">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

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