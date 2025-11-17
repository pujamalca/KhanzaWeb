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
        // Billing/Invoice table
        Schema::create('billing_pasien', function (Blueprint $table) {
            $table->id();

            // Foreign key
            $table->string('no_rawat', 17)->unique();

            // Cost breakdown
            $table->double('biaya_registrasi', 15, 2)->default(0);
            $table->double('biaya_pemeriksaan', 15, 2)->default(0);
            $table->double('biaya_tindakan', 15, 2)->default(0);
            $table->double('biaya_obat', 15, 2)->default(0);
            $table->double('biaya_laboratorium', 15, 2)->default(0);
            $table->double('biaya_lainnya', 15, 2)->default(0);

            // Discounts & adjustments
            $table->double('diskon', 15, 2)->default(0)->comment('Discount amount');
            $table->double('pajak', 15, 2)->default(0)->comment('Tax amount');

            // Totals
            $table->double('subtotal', 15, 2)->default(0)->comment('Before discount & tax');
            $table->double('total_biaya', 15, 2)->default(0)->comment('Grand total');
            $table->double('total_dibayar', 15, 2)->default(0)->comment('Amount paid');
            $table->double('sisa_tagihan', 15, 2)->default(0)->comment('Remaining balance');

            // Status & metadata
            $table->enum('status_bayar', ['Belum Bayar', 'Lunas', 'Cicilan'])->default('Belum Bayar');
            $table->date('tgl_billing')->nullable();
            $table->text('catatan')->nullable();

            // Audit fields
            $table->string('created_by', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraints
            $table->foreign('no_rawat')
                  ->references('no_rawat')
                  ->on('reg_periksa')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Indexes
            $table->index('status_bayar');
            $table->index('tgl_billing');
        });

        // Payment tracking table
        Schema::create('pembayaran_pasien', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('billing_id');
            $table->string('no_rawat', 17);

            // Payment details
            $table->double('jumlah_bayar', 15, 2)->default(0);
            $table->enum('metode_bayar', ['Tunai', 'Transfer', 'Kartu Kredit', 'Kartu Debit', 'BPJS', 'Asuransi', 'Lainnya'])->default('Tunai');
            $table->string('no_referensi', 50)->nullable()->comment('Transaction/Reference number');
            $table->date('tgl_bayar');
            $table->time('jam_bayar');
            $table->text('keterangan')->nullable();

            // Metadata
            $table->string('diterima_oleh', 20)->nullable()->comment('Received by (nip)');
            $table->timestamp('created_at')->useCurrent();

            // Foreign key constraints
            $table->foreign('billing_id')
                  ->references('id')
                  ->on('billing_pasien')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('no_rawat')
                  ->references('no_rawat')
                  ->on('reg_periksa')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Indexes
            $table->index(['billing_id', 'tgl_bayar']);
            $table->index('tgl_bayar');
            $table->index('metode_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pasien');
        Schema::dropIfExists('billing_pasien');
    }
};
