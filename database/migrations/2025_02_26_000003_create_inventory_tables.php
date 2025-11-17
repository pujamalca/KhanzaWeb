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
        // Create kodesatuan table (Unit of Measure)
        Schema::create('kodesatuan', function (Blueprint $table) {
            $table->string('kode_sat', 4)->primary();
            $table->string('satuan', 30);
        });

        // Create jenis table (Item Category)
        Schema::create('jenis', function (Blueprint $table) {
            $table->string('kd_jenis', 4)->primary();
            $table->string('nama', 50);
        });

        // Create databarang table (Medicines & Supplies Inventory)
        Schema::create('databarang', function (Blueprint $table) {
            $table->string('kode_brng', 15)->primary();
            $table->string('nama_brng', 100);
            $table->string('kd_sat', 4)->nullable();
            $table->string('letak', 50)->nullable();
            $table->double('dasar', 15, 2)->default(0); // Base price
            $table->double('h_beli', 15, 2)->default(0); // Purchase price
            $table->double('ralan', 15, 2)->default(0); // Outpatient price
            $table->double('kelas1', 15, 2)->default(0); // Class 1 price
            $table->double('kelas2', 15, 2)->default(0); // Class 2 price
            $table->double('kelas3', 15, 2)->default(0); // Class 3 price
            $table->double('utama', 15, 2)->default(0); // Main class price
            $table->double('vip', 15, 2)->default(0); // VIP price
            $table->double('vvip', 15, 2)->default(0); // VVIP price
            $table->double('beliluar', 15, 2)->default(0); // External purchase price
            $table->double('jualbebas', 15, 2)->default(0); // Over-the-counter price
            $table->double('stok', 15, 2)->default(0); // Current stock
            $table->smallInteger('stok_minimum')->default(10); // Minimum stock alert level
            $table->double('kapasitas', 15, 2)->default(1);
            $table->string('kd_jenis', 4)->nullable();
            $table->smallInteger('isi')->default(1);
            $table->date('expire')->nullable();
            $table->enum('status', ['1', '0'])->default('1'); // 1=active, 0=inactive

            $table->foreign('kd_sat')->references('kode_sat')->on('kodesatuan')
                  ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('kd_jenis')->references('kd_jenis')->on('jenis')
                  ->onUpdate('cascade')->onDelete('set null');
        });

        // Add indexes
        Schema::table('databarang', function (Blueprint $table) {
            $table->index('nama_brng');
            $table->index('kd_jenis');
            $table->index('status');
            $table->index('stok');
        });

        // Seed kodesatuan with common units
        DB::table('kodesatuan')->insert([
            ['kode_sat' => 'TAB', 'satuan' => 'Tablet'],
            ['kode_sat' => 'KAPS', 'satuan' => 'Kapsul'],
            ['kode_sat' => 'BOT', 'satuan' => 'Botol'],
            ['kode_sat' => 'AMP', 'satuan' => 'Ampul'],
            ['kode_sat' => 'VIAL', 'satuan' => 'Vial'],
            ['kode_sat' => 'TUBE', 'satuan' => 'Tube'],
            ['kode_sat' => 'PCS', 'satuan' => 'Pieces'],
            ['kode_sat' => 'BKS', 'satuan' => 'Bungkus'],
            ['kode_sat' => 'BTG', 'satuan' => 'Batang'],
            ['kode_sat' => 'PLS', 'satuan' => 'Piles'],
            ['kode_sat' => 'SDT', 'satuan' => 'Sendok Teh'],
            ['kode_sat' => 'SDM', 'satuan' => 'Sendok Makan'],
            ['kode_sat' => 'ML', 'satuan' => 'Mililiter'],
            ['kode_sat' => 'MG', 'satuan' => 'Miligram'],
            ['kode_sat' => 'GR', 'satuan' => 'Gram'],
        ]);

        // Seed jenis with common categories
        DB::table('jenis')->insert([
            ['kd_jenis' => 'OBT', 'nama' => 'Obat'],
            ['kd_jenis' => 'BHP', 'nama' => 'Bahan Habis Pakai'],
            ['kd_jenis' => 'ALK', 'nama' => 'Alat Kesehatan'],
            ['kd_jenis' => 'KON', 'nama' => 'Konsinyasi'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('databarang');
        Schema::dropIfExists('jenis');
        Schema::dropIfExists('kodesatuan');
    }
};
