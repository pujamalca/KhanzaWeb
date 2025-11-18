<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Rujukan - Data rujukan ke spesialis/RS/lab
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        Schema::create('pcare_rujukan', function (Blueprint $table) {
            $table->string('no_rujukan', 50)->primary()->comment('No rujukan dari PCare');
            $table->string('no_kunjungan', 40)->comment('No kunjungan dari PCare');
            $table->string('no_rawat', 17)->comment('No rawat internal');
            $table->string('tgl_rujukan', 10)->comment('Tanggal rujukan');
            $table->string('kd_ppk', 20)->comment('Kode PPK tujuan rujukan');
            $table->string('nm_ppk', 100)->nullable()->comment('Nama PPK tujuan');
            $table->string('kd_spesialis', 10)->comment('Kode spesialis');
            $table->string('nm_spesialis', 50)->nullable()->comment('Nama spesialis');
            $table->string('kd_subspesialis', 10)->nullable()->comment('Kode subspesialis');
            $table->string('nm_subspesialis', 50)->nullable()->comment('Nama subspesialis');
            $table->string('kd_sarana', 10)->nullable()->comment('Kode sarana rujukan');
            $table->string('nm_sarana', 50)->nullable()->comment('Nama sarana');
            $table->string('kd_khusus', 10)->nullable()->comment('Kode rujukan khusus');
            $table->string('nm_khusus', 50)->nullable()->comment('Nama rujukan khusus');
            $table->string('kd_diagnosa', 10)->comment('Kode diagnosa (ICD-10)');
            $table->string('nm_diagnosa', 255)->nullable()->comment('Nama diagnosa');
            $table->text('catatan')->nullable()->comment('Catatan rujukan');
            $table->text('results')->nullable()->comment('Response dari PCare (JSON)');

            // Status tracking
            $table->enum('status_kirim', ['Belum', 'Sudah', 'Gagal'])->default('Belum')->comment('Status kirim ke PCare');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('no_kunjungan');
            $table->index('no_rawat');
            $table->index('tgl_rujukan');
            $table->index('kd_ppk');
            $table->index('status_kirim');

            // Foreign keys
            $table->foreign('no_kunjungan')->references('no_kunjungan')->on('pcare_kunjungan')->onDelete('cascade');
            $table->foreign('no_rawat')->references('no_rawat')->on('reg_periksa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcare_rujukan');
    }
};
