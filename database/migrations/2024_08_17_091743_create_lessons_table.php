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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('poster')->nullable();
            $table->string('video')->nullable();
            $table->time('duration')->nullable();
            $table->string('ExplainPdf')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('lec_id')->constrained('lecs')->cascadeOnDelete();
            $table->integer('numOfPdf')->nullable();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
