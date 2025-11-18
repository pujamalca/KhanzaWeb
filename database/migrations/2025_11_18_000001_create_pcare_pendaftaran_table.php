<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Pendaftaran - Registrasi pasien BPJS ke PCare
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        Schema::create('pcare_pendaftaran', function (Blueprint $table) {
            $table->string('no_rawat', 17)->primary()->comment('No rawat internal');
            $table->string('no_urut', 10)->nullable()->comment('No urut pendaftaran PCare');
            $table->string('no_kunjungan', 40)->unique()->nullable()->comment('No kunjungan dari PCare (key)');
            $table->string('tgl_daftar', 10)->comment('Tanggal pendaftaran');
            $table->string('no_kartu', 25)->comment('No kartu BPJS');
            $table->string('nm_pasien', 100)->comment('Nama pasien');
            $table->enum('jkel', ['L', 'P'])->comment('Jenis kelamin');
            $table->date('tgl_lahir')->comment('Tanggal lahir');
            $table->string('no_telp', 20)->nullable()->comment('No telepon');
            $table->string('kd_provider', 20)->comment('Kode provider');
            $table->string('nm_provider', 100)->nullable()->comment('Nama provider');
            $table->string('kd_poli', 10)->comment('Kode poli PCare');
            $table->string('nm_poli', 50)->nullable()->comment('Nama poli');
            $table->string('kunjungan_sakit', 1)->default('1')->comment('0=Sehat, 1=Sakit');
            $table->enum('sistole', ['0', '1'])->default('0')->comment('Sistole (diisi untuk prolanis)');
            $table->enum('diastole', ['0', '1'])->default('0')->comment('Diastole (diisi untuk prolanis)');
            $table->decimal('beratbadan', 5, 2)->nullable()->comment('Berat badan (kg)');
            $table->decimal('tinggibadan', 5, 2)->nullable()->comment('Tinggi badan (cm)');
            $table->decimal('resprate', 5, 2)->nullable()->comment('Respiratory rate');
            $table->decimal('heartrate', 5, 2)->nullable()->comment('Heart rate');
            $table->string('kdtkp', 10)->default('10')->comment('Kode TKP (10=Layanan Obat Tanpa Resep Dokter)');

            // Status tracking
            $table->enum('status_kirim', ['Belum', 'Sudah', 'Gagal'])->default('Belum')->comment('Status kirim ke PCare');
            $table->text('keterangan')->nullable()->comment('Keterangan/error message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('no_kartu');
            $table->index('tgl_daftar');
            $table->index('kd_poli');
            $table->index('status_kirim');

            // Foreign key
            $table->foreign('no_rawat')->references('no_rawat')->on('reg_periksa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcare_pendaftaran');
    }
};
