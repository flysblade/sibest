<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('username', 100)->unique('uq_users_username');
            $table->string('nama');
            $table->string('email')->unique('uq_users_email');
            $table->string('password_hash');
            $table->enum('role', ['mahasiswa', 'admin', 'penyeleksi']);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('kategori_beasiswa', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('nama_kategori', 100)->unique('uq_kategori_nama');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('periode', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('nama_periode', 50)->unique('uq_periode_nama');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('beasiswa_program', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->text('syarat')->nullable();
            $table->enum('jalur', ['internal', 'eksternal', 'hybrid']);
            $table->bigInteger('id_kategori');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('id_kategori', 'fk_program_kategori');
            $table->foreign('id_kategori', 'fk_program_kategori')
                ->references('id')->on('kategori_beasiswa')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('beasiswa_offering', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_program');
            $table->bigInteger('id_periode');
            $table->string('link_portal')->nullable();
            $table->string('kontak_pic')->nullable();
            $table->date('tanggal_buka')->nullable();
            $table->date('tanggal_tutup')->nullable();
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');
            $table->integer('view_count')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_program', 'id_periode'], 'uq_offering_program_periode');
            $table->index('id_periode', 'fk_offering_periode');
            $table->foreign('id_periode', 'fk_offering_periode')
                ->references('id')->on('periode')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_program', 'fk_offering_program')
                ->references('id')->on('beasiswa_program')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('tahapan', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_offering');
            $table->string('nama_tahapan', 100);
            $table->integer('urutan');
            $table->dateTime('mulai_at')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_offering', 'urutan'], 'uq_tahapan_offering_urutan');
            $table->foreign('id_offering', 'fk_tahapan_offering')
                ->references('id')->on('beasiswa_offering')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('dokumen_template', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_offering');
            $table->bigInteger('id_tahapan')->nullable();
            $table->string('nama_dokumen');
            $table->boolean('required')->default(true);
            $table->text('keterangan')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_offering', 'nama_dokumen'], 'uq_template_offering_nama');
            $table->index('id_tahapan', 'fk_template_tahapan');
            $table->foreign('id_offering', 'fk_template_offering')
                ->references('id')->on('beasiswa_offering')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_tahapan', 'fk_template_tahapan')
                ->references('id')->on('tahapan')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::create('pengajuan', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_offering');
            $table->bigInteger('id_mahasiswa');
            $table->bigInteger('id_periode');
            $table->enum('status', ['draft', 'diajukan', 'diproses', 'ditolak', 'selesai'])->default('draft');
            $table->text('admin_note')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('finalized_at')->nullable();
            $table->enum('hasil_akhir', ['lulus', 'tidak_lulus', 'dibatalkan'])->nullable();
            $table->tinyInteger('active_flag')->storedAs("case when (`status` = 'selesai') then NULL else 1 end");
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_mahasiswa', 'id_periode', 'active_flag'], 'uq_pengajuan_aktif');
            $table->index('id_offering', 'fk_pengajuan_offering');
            $table->index('id_periode', 'fk_pengajuan_periode');
            $table->foreign('id_mahasiswa', 'fk_pengajuan_mahasiswa')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_offering', 'fk_pengajuan_offering')
                ->references('id')->on('beasiswa_offering')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_periode', 'fk_pengajuan_periode')
                ->references('id')->on('periode')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_pengajuan');
            $table->bigInteger('id_template');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->dateTime('uploaded_at')->useCurrent();
            $table->enum('verified_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->bigInteger('verified_by')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->text('verified_note')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_pengajuan', 'id_template'], 'uq_dokumen_pengajuan_template');
            $table->index('verified_by', 'idx_dokumen_verified_by');
            $table->index('id_template', 'fk_dokumen_template');
            $table->foreign('id_pengajuan', 'fk_dokumen_pengajuan')
                ->references('id')->on('pengajuan')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_template', 'fk_dokumen_template')
                ->references('id')->on('dokumen_template')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('verified_by', 'fk_dokumen_verified_by')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('pengajuan_tahapan', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_pengajuan');
            $table->bigInteger('id_tahapan');
            $table->enum('status', ['menunggu', 'lolos', 'gagal'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->bigInteger('decided_by')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['id_pengajuan', 'id_tahapan'], 'uq_pengajuan_tahapan');
            $table->index('decided_by', 'idx_pengajuan_tahapan_decided_by');
            $table->index('id_tahapan', 'fk_pengajuan_tahapan_tahapan');
            $table->foreign('decided_by', 'fk_pengajuan_tahapan_decided_by')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_pengajuan', 'fk_pengajuan_tahapan_pengajuan')
                ->references('id')->on('pengajuan')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_tahapan', 'fk_pengajuan_tahapan_tahapan')
                ->references('id')->on('tahapan')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('offering_penyeleksi', function (Blueprint $table) {
            $table->bigInteger('id_offering');
            $table->bigInteger('id_user');
            $table->dateTime('created_at')->useCurrent();
            $table->primary(['id_offering', 'id_user']);
            $table->unique('id_user', 'uq_penyeleksi_user');
            $table->foreign('id_offering', 'fk_penyeleksi_offering')
                ->references('id')->on('beasiswa_offering')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_user', 'fk_penyeleksi_user')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('pengumuman', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_offering');
            $table->string('judul');
            $table->text('isi');
            $table->dateTime('published_at')->nullable();
            $table->bigInteger('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index('created_by', 'idx_pengumuman_created_by');
            $table->index('id_offering', 'fk_pengumuman_offering');
            $table->foreign('created_by', 'fk_pengumuman_created_by')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_offering', 'fk_pengumuman_offering')
                ->references('id')->on('beasiswa_offering')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('mahasiswa_profile', function (Blueprint $table) {
            $table->bigInteger('user_id')->primary();
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->string('penghasilan_orang_tua', 50)->nullable();
            $table->string('prodi', 150)->nullable();
            $table->year('angkatan')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('user_id', 'fk_profile_user')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->boolean('notif_pengajuan')->default(true)->nullable();
            $table->boolean('notif_status')->default(true)->nullable();
            $table->boolean('notif_pengumuman')->default(true)->nullable();
            $table->string('telegram_chat_id', 50)->nullable();
            $table->string('telegram_username', 100)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique('user_id');
            $table->foreign('user_id', 'user_preferences_ibfk_1')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });

        Schema::create('telegram_subscriber', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('chat_id', 50)->unique('uq_telegram_chat_id');
            $table->bigInteger('id_mahasiswa')->nullable()->unique('uq_telegram_mahasiswa');
            $table->dateTime('tanggal_subscribe')->useCurrent();
            $table->foreign('id_mahasiswa', 'fk_telegram_mahasiswa')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->nullOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            foreach ([
                'users',
                'kategori_beasiswa',
                'periode',
                'beasiswa_program',
                'beasiswa_offering',
                'tahapan',
                'dokumen_template',
                'pengajuan',
                'dokumen_pengajuan',
                'pengajuan_tahapan',
                'offering_penyeleksi',
                'pengumuman',
                'mahasiswa_profile',
                'user_preferences',
                'telegram_subscriber',
            ] as $table) {
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_subscriber');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('mahasiswa_profile');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('offering_penyeleksi');
        Schema::dropIfExists('pengajuan_tahapan');
        Schema::dropIfExists('dokumen_pengajuan');
        Schema::dropIfExists('pengajuan');
        Schema::dropIfExists('dokumen_template');
        Schema::dropIfExists('tahapan');
        Schema::dropIfExists('beasiswa_offering');
        Schema::dropIfExists('beasiswa_program');
        Schema::dropIfExists('periode');
        Schema::dropIfExists('kategori_beasiswa');
        Schema::dropIfExists('users');
    }
};
