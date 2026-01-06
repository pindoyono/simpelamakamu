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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('archive_code')->unique()->comment('Kode arsip unik');
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('academic_period_id')->constrained('academic_periods')->onDelete('cascade');

            // Document Classification
            $table->enum('document_type', [
                'sarpras_report', 'procurement_proposal', 'maintenance_record',
                'inspection_report', 'inventory_list', 'budget_document',
                'contract', 'photo_documentation', 'certificate', 'other'
            ])->default('other');
            $table->string('category')->nullable()->comment('Kategori dokumen');
            $table->string('title')->comment('Judul dokumen');
            $table->text('description')->nullable();

            // File Information
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('File size in bytes');
            $table->string('mime_type')->nullable();

            // Document Status
            $table->enum('status', ['draft', 'active', 'archived', 'deleted'])->default('active');
            $table->boolean('is_confidential')->default(false);
            $table->date('retention_date')->nullable()->comment('Tanggal retensi dokumen');

            // Access Tracking
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();

            // Audit Fields
            $table->uuid('uploaded_by')->nullable();
            $table->uuid('created_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('sekolah_id');
            $table->index('academic_period_id');
            $table->index('document_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
