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
        Schema::table('tracker', function (Blueprint $table) {
            // Tambahkan kolom id hanya jika tabel belum memiliki primary key lain
            if (!Schema::hasColumn('tracker', 'id')) {
                $table->id()->first(); // Menambahkan kolom id di awal tabel
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracker', function (Blueprint $table) {
            if (Schema::hasColumn('tracker', 'id')) {
                $table->dropColumn('id'); // Hapus kolom id jika rollback
            }
        });
    }
};
