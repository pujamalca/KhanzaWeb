<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE trackersql DROP PRIMARY KEY');
        Schema::table('trackersql', function (Blueprint $table) {
            // Tambahkan kolom id hanya jika tabel belum memiliki primary key lain
            if (!Schema::hasColumn('trackersql', 'id')) {
                $table->id()->first(); // Menambahkan kolom id di awal tabel
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackersql', function (Blueprint $table) {
            if (Schema::hasColumn('trackersql', 'id')) {
                $table->dropColumn('id'); // Hapus kolom id jika rollback
            }
        });
        DB::statement('ALTER TABLE trackersql DROP PRIMARY KEY');
    }
};
