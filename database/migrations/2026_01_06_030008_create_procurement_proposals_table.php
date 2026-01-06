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
        Schema::create('procurement_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_code')->unique()->comment('Kode usulan unik per periode');
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('academic_period_id')->constrained('academic_periods')->onDelete('cascade');

            // Proposal Details
            $table->string('title')->comment('Judul usulan pengadaan');
            $table->text('description')->nullable();
            $table->decimal('total_budget', 15, 2)->nullable()->comment('Total anggaran yang diusulkan');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Workflow Management
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'])->default('draft');

            // Submission tracking
            $table->uuid('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // Review tracking
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Approval tracking
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();

            // Rejection tracking
            $table->uuid('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('sekolah_id');
            $table->index('academic_period_id');
            $table->index('status');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_proposals');
    }
};
