<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password');

        $users = [];
        foreach ([
            ['username' => 'admin_sibest', 'nama' => 'Admin SIBEST', 'email' => 'admin@sibest.test', 'role' => 'admin'],
            ['username' => '0705851234', 'nama' => 'Zainul Hasan', 'email' => 'zainul@sibest.test', 'role' => 'penyeleksi'],
            ['username' => '0708905678', 'nama' => 'Ahmad Khairi', 'email' => 'khairi@sibest.test', 'role' => 'penyeleksi'],
            ['username' => '2421500048', 'nama' => 'Nadzifatus Sariroh', 'email' => 'nadzifa@sibest.test', 'role' => 'mahasiswa'],
            ['username' => '2026101001', 'nama' => 'Enggar Diantoro', 'email' => 'enggar@sibest.test', 'role' => 'mahasiswa'],
            ['username' => '2421500049', 'nama' => 'Nurul Lailatuzzahro', 'email' => 'nurullaila12@gmail.com', 'role' => 'mahasiswa'],
        ] as $user) {
            $users[$user['username']] = DB::table('users')->insertGetId([
                ...$user,
                'password_hash' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (['2421500048', '2026101001', '2421500049'] as $username) {
            DB::table('mahasiswa_profile')->insert([
                'user_id' => $users[$username],
                'prodi' => 'Sistem Informasi',
                'angkatan' => str_starts_with($username, '2026') ? 2026 : 2024,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $categories = [];
        foreach (['Pemerintah', 'Internal', 'Eksternal'] as $category) {
            $categories[$category] = DB::table('kategori_beasiswa')->insertGetId([
                'nama_kategori' => $category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $periodeId = DB::table('periode')->insertGetId([
            'nama_periode' => '2026/2027',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $programs = [];
        foreach ([
            [
                'nama' => 'KIP-K',
                'jalur' => 'hybrid',
                'kategori' => 'Pemerintah',
                'deskripsi' => 'Bantuan biaya pendidikan dari pemerintah bagi mahasiswa baru yang memiliki keterbatasan ekonomi namun berpotensi akademik tinggi. KIP-Kuliah membebaskan biaya kuliah dan memberikan bantuan biaya hidup.',
                'syarat' => "1. Memiliki NIK, NISN, NPSN, dan email aktif.\r\n2. Lulusan SMA/SMK/MA tahun berjalan atau 2 tahun sebelumnya.\r\n3. Memiliki keterbatasan ekonomi yang dibuktikan dengan KIP, KKS, PKH, DTKS, atau SKTM.\r\n4. Melampirkan KTP dan KK.\r\n5. Melampirkan rapor/ijazah/SKL.\r\n6. Melampirkan bukti penghasilan orang tua.\r\n7. Melampirkan foto kondisi rumah.\r\n8. Melampirkan bukti pembayaran listrik/air/PBB (jika ada).\r\n9. Wajib mendaftar di portal pusat KIP-Kuliah terlebih dahulu, lalu melaporkan bukti pendaftaran ke UNUJA melalui SIBEST untuk mengikuti verifikasi dan wawancara kampus.",
            ],
            [
                'nama' => 'S-Pat',
                'jalur' => 'internal',
                'kategori' => 'Internal',
                'deskripsi' => 'Program beasiswa internal Universitas Nurul Jadid yang ditujukan untuk santri aktif yang memiliki kontribusi dan dedikasi tinggi di lingkungan pondok pesantren dan kampus.',
                'syarat' => "1. Mahasiswa aktif UNUJA yang berstatus sebagai santri di Pondok Pesantren Nurul Jadid.\r\n2. Memenuhi seluruh kriteria seleksi Santri Patriot yang ditetapkan pengelola.\r\n3. Melampirkan KTP, Kartu Keluarga (KK), dan Surat Keterangan Tidak Mampu (SKTM).\r\n4. Melampirkan Surat Perjanjian Santri Patriot (diketik rapi, menggunakan materai Rp10.000, dan diserahkan sesuai ketentuan folder).\r\n5. Bersedia mengikuti seluruh proses seleksi yang ditetapkan pengelola S-Pat melalui SIBEST.",
            ],
            [
                'nama' => 'Beasiswa GenBI',
                'jalur' => 'eksternal',
                'kategori' => 'Eksternal',
                'deskripsi' => 'Beasiswa prestasi dari Bank Indonesia (BI) bagi mahasiswa berprestasi dan peduli sosial. Penerima beasiswa tergabung dalam komunitas Generasi Baru Indonesia (GenBI) dan wajib berperan aktif dalam kegiatan sosial serta pengembangan komunitas.',
                'syarat' => "1. Mahasiswa aktif S1 (minimal menempuh 40 SKS) atau D3 (minimal 24 SKS).\r\n2. Memiliki IPK minimal 3.00 yang dibuktikan dengan KHS/transkrip nilai yang telah dilegalisasi oleh kampus.\r\n3. Melampirkan KTM aktif, KTP, dan Kartu Keluarga (KK).\r\n4. Melampirkan Pas Foto terbaru berwarna (ukuran 3x4 atau 4x3, latar belakang merah/biru).\r\n5. Melampirkan bukti profil aktif PDDIKTI atau Surat Keterangan Aktif Kuliah dari fakultas.\r\n6. Melampirkan Formulir Biodata BI (Form A.1) yang diisi lengkap dan dibubuhi materai Rp10.000.\r\n7. Melampirkan Surat Pernyataan tidak sedang menerima beasiswa lain dan tidak dalam ikatan dinas.\r\n8. Melampirkan Surat Pernyataan bersedia aktif mengelola dan mengembangkan komunitas GenBI.\r\n9. Melampirkan Surat Rekomendasi dari dosen wali/kaprodi atau pimpinan organisasi.\r\n10. Melampirkan CV/Resume, Motivation Letter (Bahasa Indonesia/Inggris), dan sertifikat prestasi (jika ada).\r\n11. Melampirkan SKTM dan slip gaji orang tua (diutamakan bagi pelamar prasejahtera).\r\n12. Pendaftaran awal dilakukan melalui portal resmi penyelenggara GenBI.",
            ],
            [
                'nama' => 'Beasiswa Cinta Bergema',
                'jalur' => 'eksternal',
                'kategori' => 'Pemerintah',
                'deskripsi' => 'Beasiswa dari Pemerintah Kabupaten Jember yang diperuntukkan bagi mahasiswa berprestasi dan/atau memiliki keterbatasan ekonomi, khususnya pemilik KTP dan KK Kabupaten Jember.',
                'syarat' => "Persyaratan Umum:\r\n1. Mahasiswa pemilik KTP dan KK Kabupaten Jember.\r\n2. Melampirkan Surat Keterangan Aktif Kuliah.\r\n3. Melampirkan bukti resmi nominal/nilai UKT dari perguruan tinggi.\r\n4. Akreditasi program studi minimal C atau Baik.\r\n5. Melampirkan KHS semester terakhir (kecuali mahasiswa baru).\r\n6. Melampirkan Surat Pernyataan tidak menerima beasiswa lain (materai Rp10.000).\r\n7. Melampirkan Surat Komitmen penyelesaian perkuliahan (materai Rp10.000).\r\n\r\nJalur 1 (Prestasi):\r\n- IPK minimal 3.50 (akademik/non-akademik).\r\n- Sertifikat/piagam penghargaan atau surat keterangan medali resmi Dispora Jember.\r\n\r\nJalur 2 (Afirmasi Ekonomi):\r\n- SKTM dari desa/kelurahan atau kartu bantuan sosial (PKH/KKS).\r\n- Rekening/tagihan listrik rumah 3 bulan terakhir.\r\n- Foto keluarga dengan latar depan rumah.\r\n- Slip gaji orang tua atau surat keterangan penghasilan.\r\n- Bukti status desil kemiskinan.\r\n\r\nJalur 3 (Guru/Perangkat Desa):\r\n- Surat izin belajar dari Kepala Sekolah (untuk guru) atau surat tugas resmi (untuk perangkat desa).\r\n\r\nJalur 4 (Kompetisi):\r\n- Portofolio kompetensi, surat rekomendasi, atau bukti keikutsertaan kompetisi.\r\n\r\nJalur 5 (Santri/Hafiz):\r\n- Surat keterangan aktif santri dari pengasuh pondok, atau sertifikat hafalan Al-Qur'an.\r\n\r\nJalur 6 (Khusus):\r\n- Surat keterangan disabilitas resmi dari perguruan tinggi/lembaga berwenang.",
            ],
        ] as $program) {
            $programs[$program['nama']] = DB::table('beasiswa_program')->insertGetId([
                'nama' => $program['nama'],
                'jalur' => $program['jalur'],
                'id_kategori' => $categories[$program['kategori']],
                'deskripsi' => $program['deskripsi'],
                'syarat' => $program['syarat'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $offerings = [];
        foreach ([
            ['nama' => 'KIP-K', 'portal' => 'https://kip-kuliah.kemdiktisaintek.go.id/'],
            ['nama' => 'S-Pat', 'portal' => null],
            ['nama' => 'Beasiswa GenBI', 'portal' => 'https://genbi.id/'],
            ['nama' => 'Beasiswa Cinta Bergema', 'portal' => 'https://bergema.jemberkab.go.id/'],
        ] as $offering) {
            $offerings[$offering['nama']] = DB::table('beasiswa_offering')->insertGetId([
                'id_program' => $programs[$offering['nama']],
                'id_periode' => $periodeId,
                'link_portal' => $offering['portal'],
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $stages = [];
        foreach (['KIP-K', 'S-Pat'] as $offeringName) {
            foreach ([
                ['nama' => 'Administrasi', 'urutan' => 1],
                ['nama' => 'Wawancara', 'urutan' => 2],
                ['nama' => 'Hasil', 'urutan' => 3],
            ] as $stage) {
                $stages[$offeringName][$stage['urutan']] = DB::table('tahapan')->insertGetId([
                    'id_offering' => $offerings[$offeringName],
                    'nama_tahapan' => $stage['nama'],
                    'urutan' => $stage['urutan'],
                    'keterangan' => 'Tahapan seleksi ' . $offeringName . '.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $kipkDocuments = [
            ['nama' => 'Bukti Pendaftaran KIP-K', 'required' => true],
            ['nama' => 'KTP', 'required' => false],
            ['nama' => 'KK', 'required' => false],
            ['nama' => 'Pas Foto', 'required' => false],
            ['nama' => 'Rapor', 'required' => false],
            ['nama' => 'Bukti Penghasilan', 'required' => false],
            ['nama' => 'Foto Rumah', 'required' => false],
            ['nama' => 'NIK', 'required' => false],
            ['nama' => 'NISN', 'required' => false],
            ['nama' => 'NPSN', 'required' => false],
            ['nama' => 'Akta Kelahiran', 'required' => false],
        ];

        foreach ($kipkDocuments as $document) {
            DB::table('dokumen_template')->insert([
                'id_offering' => $offerings['KIP-K'],
                'id_tahapan' => $stages['KIP-K'][1],
                'nama_dokumen' => $document['nama'],
                'required' => $document['required'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ([
            ['nama' => 'KTP', 'required' => true],
            ['nama' => 'KK', 'required' => true],
            ['nama' => 'SKTM', 'required' => true],
            ['nama' => 'Foto 3x4', 'required' => true],
            [
                'nama' => 'Surat Perjanjian Santri Patriot 2025',
                'required' => true,
                'keterangan' => 'Biodata diketik rapi; tanggal disesuaikan; print dengan materai Rp10.000; folder biru untuk laki-laki dan kuning untuk perempuan; nama di luar; tidak untuk dibagikan',
            ],
        ] as $document) {
            DB::table('dokumen_template')->insert([
                'id_offering' => $offerings['S-Pat'],
                'id_tahapan' => $stages['S-Pat'][1],
                'nama_dokumen' => $document['nama'],
                'required' => $document['required'],
                'keterangan' => $document['keterangan'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('offering_penyeleksi')->insert([
            [
                'id_offering' => $offerings['S-Pat'],
                'id_user' => $users['0705851234'],
                'created_at' => $now,
            ],
            [
                'id_offering' => $offerings['KIP-K'],
                'id_user' => $users['0708905678'],
                'created_at' => $now,
            ],
        ]);

        $applications = [
            [
                'offering' => 'KIP-K',
                'student' => '2026101001',
                'status' => 'diproses',
                'hasil_akhir' => null,
                'stages' => ['lolos', 'menunggu', 'menunggu'],
            ],
            [
                'offering' => 'S-Pat',
                'student' => '2421500048',
                'status' => 'diajukan',
                'hasil_akhir' => null,
                'stages' => ['menunggu', 'menunggu', 'menunggu'],
            ],
            [
                'offering' => 'S-Pat',
                'student' => '2421500049',
                'status' => 'selesai',
                'hasil_akhir' => 'lulus',
                'stages' => ['lolos', 'lolos', 'lolos'],
            ],
        ];

        foreach ($applications as $application) {
            $submittedAt = $application['status'] === 'diajukan' || $application['status'] === 'diproses'
                ? $now
                : $now->copy()->subDays(14);

            $applicationId = DB::table('pengajuan')->insertGetId([
                'id_offering' => $offerings[$application['offering']],
                'id_mahasiswa' => $users[$application['student']],
                'id_periode' => $periodeId,
                'status' => $application['status'],
                'hasil_akhir' => $application['hasil_akhir'],
                'submitted_at' => $submittedAt,
                'finalized_at' => $application['status'] === 'selesai' ? $now : null,
                'created_at' => $submittedAt,
                'updated_at' => $now,
            ]);

            foreach ($application['stages'] as $index => $status) {
                DB::table('pengajuan_tahapan')->insert([
                    'id_pengajuan' => $applicationId,
                    'id_tahapan' => $stages[$application['offering']][$index + 1],
                    'status' => $status,
                    'catatan' => $status === 'lolos' ? 'Tahap dinyatakan lolos.' : null,
                    'decided_by' => $status === 'lolos'
                        ? $users[$application['offering'] === 'KIP-K' ? '0708905678' : '0705851234']
                        : null,
                    'decided_at' => $status === 'lolos' ? $now : null,
                    'created_at' => $submittedAt,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
