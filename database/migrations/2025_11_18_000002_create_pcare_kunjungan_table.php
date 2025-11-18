<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Kunjungan - Data kunjungan pasien dengan diagnosa
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        Schema::create('pcare_kunjungan', function (Blueprint $table) {
            $table->string('no_kunjungan', 40)->primary()->comment('No kunjungan dari PCare');
            $table->string('no_rawat', 17)->unique()->comment('No rawat internal');
            $table->string('tgl_kunjungan', 10)->comment('Tanggal kunjungan');
            $table->string('kd_provider', 20)->comment('Kode provider');
            $table->string('no_kartu', 25)->comment('No kartu BPJS');
            $table->string('nm_pasien', 100)->comment('Nama pasien');
            $table->string('tgl_daftar', 10)->comment('Tanggal daftar');
            $table->string('kd_poli', 10)->comment('Kode poli');
            $table->string('nm_poli', 50)->nullable()->comment('Nama poli');
            $table->string('no_urut', 10)->nullable()->comment('No urut');

            // Tanda vital
            $table->string('kunjungan_sakit', 1)->default('1')->comment('0=Sehat, 1=Sakit');
            $table->decimal('sistole', 5, 2)->nullable()->comment('Tekanan darah sistole');
            $table->decimal('diastole', 5, 2)->nullable()->comment('Tekanan darah diastole');
            $table->decimal('beratbadan', 5, 2)->nullable()->comment('Berat badan (kg)');
            $table->decimal('tinggibadan', 5, 2)->nullable()->comment('Tinggi badan (cm)');
            $table->decimal('resprate', 5, 2)->nullable()->comment('Respiratory rate');
            $table->decimal('heartrate', 5, 2)->nullable()->comment('Heart rate');
            $table->decimal('lingkarperut', 5, 2)->nullable()->comment('Lingkar perut (cm)');

            // Keluhan dan pemeriksaan
            $table->text('keluhan')->nullable()->comment('Keluhan subjektif');
            $table->text('pemeriksaan')->nullable()->comment('Pemeriksaan objektif');
            $table->text('kesadaran')->nullable()->comment('Kesadaran pasien');

            // Diagnosa
            $table->string('kd_diagnosa1', 10)->comment('Kode diagnosa primer (ICD-10)');
            $table->string('nm_diagnosa1', 255)->nullable()->comment('Nama diagnosa primer');
            $table->string('kd_diagnosa2', 10)->nullable()->comment('Kode diagnosa sekunder');
            $table->string('nm_diagnosa2', 255)->nullable()->comment('Nama diagnosa sekunder');
            $table->string('kd_diagnosa3', 10)->nullable()->comment('Kode diagnosa tersier');
            $table->string('nm_diagnosa3', 255)->nullable()->comment('Nama diagnosa tersier');

            // Therapy dan tindak lanjut
            $table->text('therapy')->nullable()->comment('Terapi yang diberikan');
            $table->string('kd_tacc', 10)->nullable()->comment('Kode TACC (Tarik Anak Cara Cepat)');
            $table->string('nm_tacc', 50)->nullable()->comment('Nama TACC');
            $table->string('alasantacc', 1)->nullable()->comment('Alasan TACC');

            // Tindak lanjut
            $table->string('kd_tkp', 10)->comment('Kode tindak lanjut (10, 20, 40, 50)');
            $table->string('nm_tkp', 50)->nullable()->comment('Nama tindak lanjut');
            $table->string('tgl_pulang', 10)->nullable()->comment('Tanggal pulang/estimasi rujuk');
            $table->string('kd_dokter', 20)->comment('Kode dokter');
            $table->string('nm_dokter', 100)->nullable()->comment('Nama dokter');
            $table->string('kd_status_pulang', 10)->default('0')->comment('Kode status pulang');
            $table->string('nm_status_pulang', 50)->nullable()->comment('Nama status pulang');

            // Rujukan (jika ada)
            $table->string('tgl_rujuk', 10)->nullable()->comment('Tanggal rujukan');
            $table->string('kd_ppk', 20)->nullable()->comment('Kode PPK rujukan');
            $table->string('nm_ppk', 100)->nullable()->comment('Nama PPK rujukan');
            $table->string('kd_spesialis', 10)->nullable()->comment('Kode spesialis rujukan');
            $table->string('nm_spesialis', 50)->nullable()->comment('Nama spesialis');
            $table->string('kd_subspesialis', 10)->nullable()->comment('Kode subspesialis');
            $table->string('nm_subspesialis', 50)->nullable()->comment('Nama subspesialis');
            $table->string('kd_sarana', 10)->nullable()->comment('Kode sarana rujukan');
            $table->string('nm_sarana', 50)->nullable()->comment('Nama sarana');
            $table->string('kd_khusus', 10)->nullable()->comment('Kode rujukan khusus');
            $table->string('nm_khusus', 50)->nullable()->comment('Nama rujukan khusus');
            $table->text('catatan')->nullable()->comment('Catatan rujukan');

            // Status tracking
            $table->enum('status_kirim', ['Belum', 'Sudah', 'Gagal'])->default('Belum')->comment('Status kirim ke PCare');
            $table->text('keterangan')->nullable()->comment('Keterangan/error message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('no_rawat');
            $table->index('no_kartu');
            $table->index('tgl_kunjungan');
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
        Schema::dropIfExists('pcare_kunjungan');
    }
};
