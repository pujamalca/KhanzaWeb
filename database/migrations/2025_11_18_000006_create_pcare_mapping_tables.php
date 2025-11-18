<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PCare Mapping Tables - Mapping kode internal ke kode PCare
     * Based on SIMRS Khanza desktop PCare FKTP implementation
     */
    public function up(): void
    {
        // Mapping Poli
        Schema::create('pcare_mapping_poli', function (Blueprint $table) {
            $table->string('kd_poli_internal', 5)->primary()->comment('Kode poli internal');
            $table->string('kd_poli_pcare', 10)->unique()->comment('Kode poli PCare');
            $table->string('nm_poli_pcare', 50)->nullable()->comment('Nama poli PCare');
            $table->timestamps();

            // Foreign key
            $table->foreign('kd_poli_internal')->references('kd_poli')->on('poliklinik')->onDelete('cascade');
        });

        // Mapping Dokter
        Schema::create('pcare_mapping_dokter', function (Blueprint $table) {
            $table->string('kd_dokter_internal', 20)->primary()->comment('Kode dokter internal');
            $table->string('kd_dokter_pcare', 20)->unique()->comment('Kode dokter PCare');
            $table->string('nm_dokter_pcare', 100)->nullable()->comment('Nama dokter PCare');
            $table->timestamps();

            // Foreign key
            $table->foreign('kd_dokter_internal')->references('kd_dokter')->on('dokter')->onDelete('cascade');
        });

        // Mapping Diagnosa (ICD-10 ke PCare)
        Schema::create('pcare_mapping_diagnosa', function (Blueprint $table) {
            $table->string('kd_diagnosa_internal', 10)->primary()->comment('Kode ICD-10 internal');
            $table->string('kd_diagnosa_pcare', 10)->comment('Kode diagnosa PCare');
            $table->string('nm_diagnosa_pcare', 255)->nullable()->comment('Nama diagnosa PCare');
            $table->timestamps();

            $table->index('kd_diagnosa_pcare');

            // Foreign key
            $table->foreign('kd_diagnosa_internal')->references('kd_penyakit')->on('penyakit')->onDelete('cascade');
        });

        // Mapping Tindakan
        Schema::create('pcare_mapping_tindakan', function (Blueprint $table) {
            $table->id();
            $table->string('kd_tindakan_internal', 15)->unique()->comment('Kode tindakan internal');
            $table->string('kd_tindakan_pcare', 20)->comment('Kode tindakan PCare');
            $table->string('nm_tindakan_pcare', 255)->nullable()->comment('Nama tindakan PCare');
            $table->decimal('tarif', 10, 2)->default(0)->comment('Tarif');
            $table->timestamps();

            $table->index('kd_tindakan_pcare');
        });

        // Mapping Obat
        Schema::create('pcare_mapping_obat', function (Blueprint $table) {
            $table->string('kd_obat_internal', 15)->primary()->comment('Kode obat internal (databarang)');
            $table->string('kd_obat_pcare', 20)->comment('Kode obat PCare');
            $table->string('nm_obat_pcare', 255)->nullable()->comment('Nama obat PCare');
            $table->timestamps();

            $table->index('kd_obat_pcare');

            // Foreign key
            $table->foreign('kd_obat_internal')->references('kode_brng')->on('databarang')->onDelete('cascade');
        });

        // Activity Log PCare
        Schema::create('pcare_activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat', 17)->nullable()->comment('No rawat terkait');
            $table->string('no_kunjungan', 40)->nullable()->comment('No kunjungan terkait');
            $table->string('activity_type', 50)->comment('Jenis aktivitas (pendaftaran, kunjungan, etc)');
            $table->enum('action', ['insert', 'update', 'delete', 'get'])->comment('Aksi yang dilakukan');
            $table->string('endpoint', 255)->comment('Endpoint PCare yang dipanggil');
            $table->enum('status', ['success', 'failed'])->comment('Status request');
            $table->integer('http_code')->nullable()->comment('HTTP response code');
            $table->text('request_data')->nullable()->comment('Data request (JSON)');
            $table->text('response_data')->nullable()->comment('Response data (JSON)');
            $table->text('error_message')->nullable()->comment('Error message jika gagal');
            $table->string('user', 25)->nullable()->comment('User yang melakukan aktivitas');
            $table->timestamps();

            $table->index('no_rawat');
            $table->index('no_kunjungan');
            $table->index('activity_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcare_activity_log');
        Schema::dropIfExists('pcare_mapping_obat');
        Schema::dropIfExists('pcare_mapping_tindakan');
        Schema::dropIfExists('pcare_mapping_diagnosa');
        Schema::dropIfExists('pcare_mapping_dokter');
        Schema::dropIfExists('pcare_mapping_poli');
    }
};
