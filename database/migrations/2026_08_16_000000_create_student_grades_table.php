<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('school_year');
            $table->string('class_name');
            $table->string('subject');

            $table->decimal('travaux_1', 8, 2)->nullable();
            $table->decimal('travaux_2', 8, 2)->nullable();
            $table->decimal('exam_1', 8, 2)->nullable();
            $table->decimal('total_1', 8, 2)->default(0);
            $table->decimal('max_1', 8, 2)->default(0);

            $table->decimal('travaux_3', 8, 2)->nullable();
            $table->decimal('travaux_4', 8, 2)->nullable();
            $table->decimal('exam_2', 8, 2)->nullable();
            $table->decimal('total_2', 8, 2)->default(0);
            $table->decimal('max_2', 8, 2)->default(0);

            $table->decimal('tg', 8, 2)->default(0);
            $table->decimal('max_tg', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['inscription_id', 'plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
