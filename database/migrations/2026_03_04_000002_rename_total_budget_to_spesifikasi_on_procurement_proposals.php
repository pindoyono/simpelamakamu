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
        Schema::table('procurement_proposals', function (Blueprint $table) {
            $table->renameColumn('total_budget', 'spesifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurement_proposals', function (Blueprint $table) {
            $table->renameColumn('spesifikasi', 'total_budget');
        });
    }
};
