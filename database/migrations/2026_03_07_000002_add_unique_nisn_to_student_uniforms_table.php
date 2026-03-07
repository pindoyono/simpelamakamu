<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_uniforms', function (Blueprint $table) {
            $table->unique(['nisn', 'academic_period_id'], 'student_uniforms_nisn_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_uniforms', function (Blueprint $table) {
            $table->dropUnique('student_uniforms_nisn_period_unique');
        });
    }
};
