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
        Schema::create('diagnosa_pasien', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->string('no_rawat', 17);
            $table->string('kd_penyakit', 10);

            // Diagnosis details
            $table->enum('status', ['Ralan', 'Ranap'])->default('Ralan');
            $table->enum('prioritas', ['1', '2', '3', '4'])->default('1')
                  ->comment('1=Diagnosa Utama, 2-4=Diagnosa Sekunder');
            $table->enum('status_penyakit', ['Lama', 'Baru'])->default('Baru');

            // Metadata
            $table->string('kd_dokter', 20)->nullable();
            $table->date('tgl_diagnosis')->nullable();
            $table->time('jam_diagnosis')->nullable();

            // Foreign key constraints
            $table->foreign('no_rawat')
                  ->references('no_rawat')
                  ->on('reg_periksa')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('kd_penyakit')
                  ->references('kd_penyakit')
                  ->on('penyakit')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('kd_dokter')
                  ->references('kd_dokter')
                  ->on('dokter')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Indexes
            $table->index(['no_rawat', 'prioritas']);
            $table->index('kd_penyakit');
            $table->index('tgl_diagnosis');

            // No timestamps (matching SIMRS Khanza pattern)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_pasien');
    }
};
