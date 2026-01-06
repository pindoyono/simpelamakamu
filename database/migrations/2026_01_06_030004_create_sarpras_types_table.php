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
        Schema::create('sarpras_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_category_id')->constrained('sarpras_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable()->comment('Deskripsi tipe sarpras');
            $table->string('unit', 50)->nullable()->comment('Satuan: unit, m2, buah, dll');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Constraints
            $table->unique(['sarpras_category_id', 'name'], 'unique_category_name');

            // Indexes
            $table->index('sarpras_category_id');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras_types');
    }
};
