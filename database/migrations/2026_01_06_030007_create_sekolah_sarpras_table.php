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
        Schema::create('sekolah_sarpras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('sarpras_type_id')->constrained('sarpras_types')->onDelete('cascade');
            $table->foreignId('academic_period_id')->constrained('academic_periods')->onDelete('cascade');

            // Historical Data Fields
            $table->unsignedInteger('jumlah')->default(0);
            $table->unsignedInteger('kondisi_baik')->default(0);
            $table->unsignedInteger('kondisi_rusak_ringan')->default(0);
            $table->unsignedInteger('kondisi_rusak_sedang')->default(0);
            $table->unsignedInteger('kondisi_rusak_berat')->default(0);

            // Financial Data
            $table->decimal('nilai_perolehan', 15, 2)->nullable()->comment('Nilai perolehan awal');
            $table->year('tahun_perolehan')->nullable();
            $table->text('keterangan')->nullable();

            // Verification & Tracking
            $table->enum('data_source', ['manual', 'carry_forward', 'import'])->default('manual');
            $table->boolean('is_verified')->default(false);
            $table->uuid('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->uuid('created_by')->nullable();
            $table->boolean('needs_attention')->default(false);

            $table->timestamps();

            // Constraints
            $table->unique(['sekolah_id', 'sarpras_type_id', 'academic_period_id'], 'unique_sekolah_sarpras_period');

            // Indexes
            $table->index('sekolah_id');
            $table->index('sarpras_type_id');
            $table->index('academic_period_id');
            $table->index('is_verified');
            $table->index('needs_attention');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah_sarpras');
    }
};
