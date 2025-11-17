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
        Schema::create('pemeriksaan_ralan', function (Blueprint $table) {
            // Primary key and relationship
            $table->string('no_rawat', 17)->primary();
            $table->date('tgl_perawatan');
            $table->time('jam_rawat');

            // SOAP Notes (Subjective, Objective, Assessment, Plan)
            $table->text('suhu_tubuh')->nullable()->comment('Body Temperature (°C)');
            $table->text('tensi')->nullable()->comment('Blood Pressure (mmHg)');
            $table->text('nadi')->nullable()->comment('Pulse/Heart Rate (bpm)');
            $table->text('respirasi')->nullable()->comment('Respiratory Rate (per minute)');
            $table->text('tinggi')->nullable()->comment('Height (cm)');
            $table->text('berat')->nullable()->comment('Weight (kg)');
            $table->text('spo2')->nullable()->comment('Oxygen Saturation (%)');
            $table->text('gcs')->nullable()->comment('Glasgow Coma Scale');
            $table->text('kesadaran')->nullable()->comment('Consciousness Level');

            // SOAP Notes
            $table->text('keluhan')->nullable()->comment('Subjective: Chief Complaint');
            $table->text('pemeriksaan')->nullable()->comment('Objective: Physical Examination');
            $table->text('alergi')->nullable()->comment('Allergies');
            $table->text('lingkar_perut')->nullable()->comment('Abdominal Circumference (cm)');
            $table->text('rtl')->nullable()->comment('Assessment: Rencana Tindak Lanjut / Plan');
            $table->text('penilaian')->nullable()->comment('Assessment: Medical Assessment');
            $table->text('instruksi')->nullable()->comment('Plan: Medical Instructions');
            $table->text('evaluasi')->nullable()->comment('Evaluation');

            // Metadata
            $table->string('nip', 20)->nullable()->comment('Petugas/Perawat yang input');

            // Foreign keys
            $table->foreign('no_rawat')
                  ->references('no_rawat')
                  ->on('reg_periksa')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Indexes for performance
            $table->index(['tgl_perawatan', 'jam_rawat']);
            $table->index('nip');

            // No timestamps (matching SIMRS Khanza pattern)
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_ralan');
    }
};
