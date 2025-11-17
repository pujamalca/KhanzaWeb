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
        // Create kategori_penyakit table first (referenced by penyakit)
        Schema::create('kategori_penyakit', function (Blueprint $table) {
            $table->string('kd_ktg', 5)->primary();
            $table->string('nm_kategori', 50);
            $table->string('ciri_umum', 200)->nullable();
        });

        // Create penyakit table (ICD-10)
        Schema::create('penyakit', function (Blueprint $table) {
            $table->string('kd_penyakit', 10)->primary();
            $table->string('nm_penyakit', 255);
            $table->string('ciri_ciri', 200)->nullable();
            $table->string('keterangan', 200)->nullable();
            $table->string('kd_ktg', 5)->nullable();
            $table->enum('status', ['Ranap', 'Ralan', 'Ranap Dan Ralan'])->default('Ranap Dan Ralan');

            $table->foreign('kd_ktg')->references('kd_ktg')->on('kategori_penyakit')
                  ->onUpdate('cascade')->onDelete('set null');
        });

        // Add indexes for better performance
        Schema::table('penyakit', function (Blueprint $table) {
            $table->index('nm_penyakit');
            $table->index('kd_ktg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyakit');
        Schema::dropIfExists('kategori_penyakit');
    }
};
