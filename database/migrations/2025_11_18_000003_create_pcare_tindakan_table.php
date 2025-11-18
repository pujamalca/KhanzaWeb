<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Tindakan - Data tindakan medis yang dilakukan
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        Schema::create('pcare_tindakan', function (Blueprint $table) {
            $table->id();
            $table->string('no_kunjungan', 40)->comment('No kunjungan dari PCare');
            $table->string('no_rawat', 17)->comment('No rawat internal');
            $table->string('kd_tindakan', 20)->comment('Kode tindakan PCare');
            $table->string('nm_tindakan', 255)->nullable()->comment('Nama tindakan');
            $table->decimal('biaya', 10, 2)->default(0)->comment('Biaya tindakan');
            $table->string('keterangan', 100)->nullable()->comment('Keterangan');
            $table->text('results')->nullable()->comment('Response dari PCare (JSON)');

            // Status tracking
            $table->enum('status_kirim', ['Belum', 'Sudah', 'Gagal'])->default('Belum')->comment('Status kirim ke PCare');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('no_kunjungan');
            $table->index('no_rawat');
            $table->index('kd_tindakan');
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
        Schema::dropIfExists('pcare_tindakan');
    }
};
