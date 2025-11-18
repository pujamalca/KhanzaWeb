<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Obat Diberikan - Data obat yang diberikan ke pasien
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        Schema::create('pcare_obat_diberikan', function (Blueprint $table) {
            $table->id();
            $table->string('no_kunjungan', 40)->comment('No kunjungan dari PCare');
            $table->string('no_rawat', 17)->comment('No rawat internal');
            $table->string('kd_obat', 20)->comment('Kode obat PCare');
            $table->string('nm_obat', 255)->nullable()->comment('Nama obat');
            $table->decimal('signa1', 5, 2)->default(1)->comment('Signa 1 (frekuensi per hari)');
            $table->decimal('signa2', 5, 2)->default(1)->comment('Signa 2 (dosis per kali)');
            $table->decimal('jml_obat', 10, 2)->comment('Jumlah obat');
            $table->string('jns_obat', 20)->default('23')->comment('Jenis obat (23=Non Racikan, 24=Racikan)');
            $table->text('nmrckn')->nullable()->comment('Nama racikan');
            $table->text('aturan_pakai')->nullable()->comment('Aturan pakai');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan');
            $table->text('results')->nullable()->comment('Response dari PCare (JSON)');

            // Status tracking
            $table->enum('status_kirim', ['Belum', 'Sudah', 'Gagal'])->default('Belum')->comment('Status kirim ke PCare');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('no_kunjungan');
            $table->index('no_rawat');
            $table->index('kd_obat');
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
        Schema::dropIfExists('pcare_obat_diberikan');
    }
};
