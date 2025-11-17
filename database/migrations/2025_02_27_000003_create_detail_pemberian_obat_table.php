<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_pemberian_obat', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->date('tgl_perawatan');
            $table->time('jam');
            $table->string('no_rawat', 17);
            $table->string('kode_brng', 15);

            // Prescription details
            $table->double('jml', 15, 2)->default(0)->comment('Jumlah/Quantity');
            $table->double('embalase', 15, 2)->default(0)->comment('Biaya Embalase');
            $table->double('tuslah', 15, 2)->default(0)->comment('Biaya Tuslah/Jasa');
            $table->double('total', 15, 2)->default(0)->comment('Total Biaya');

            // Dosage & frequency
            $table->string('dosis', 100)->nullable()->comment('e.g., 500mg');
            $table->string('frekuensi', 100)->nullable()->comment('e.g., 3x sehari');
            $table->string('aturan_pakai', 150)->nullable()->comment('e.g., Sesudah makan');
            $table->text('catatan')->nullable()->comment('Special instructions');

            // Metadata
            $table->string('kd_dokter', 20)->nullable();
            $table->string('nip', 20)->nullable()->comment('Petugas yang input');
            $table->enum('status', ['Ralan', 'Ranap'])->default('Ralan');

            // Foreign key constraints
            $table->foreign('no_rawat')
                  ->references('no_rawat')
                  ->on('reg_periksa')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('kode_brng')
                  ->references('kode_brng')
                  ->on('databarang')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('kd_dokter')
                  ->references('kd_dokter')
                  ->on('dokter')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Indexes
            $table->index(['no_rawat', 'tgl_perawatan']);
            $table->index('kode_brng');
            $table->index('tgl_perawatan');

            // No timestamps (matching SIMRS Khanza pattern)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemberian_obat');
    }
};
