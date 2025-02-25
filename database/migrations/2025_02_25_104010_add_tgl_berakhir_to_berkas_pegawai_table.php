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
        Schema::table('berkas_pegawai', function (Blueprint $table) {
            $table->date('tgl_berakhir')->nullable()->after('tgl_upload'); // Menambahkan kolom tgl_berakhir setelah tgl_upload
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berkas_pegawai', function (Blueprint $table) {
            //
            $table->dropColumn('tgl_berakhir'); // Menghapus kolom saat rollback
        });
    }
};
