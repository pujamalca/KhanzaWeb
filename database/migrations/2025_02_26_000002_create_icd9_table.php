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
        Schema::create('icd9', function (Blueprint $table) {
            $table->string('kode', 8)->primary();
            $table->string('deskripsi_panjang', 255);
            $table->string('deskripsi_pendek', 80);
        });

        // Add index for searching
        Schema::table('icd9', function (Blueprint $table) {
            $table->index('deskripsi_panjang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icd9');
    }
};
