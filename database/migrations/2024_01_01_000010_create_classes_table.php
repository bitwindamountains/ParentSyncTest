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
        Schema::create('classes', function (Blueprint $table) {
            $table->id('class_id');
            $table->foreignId('section_id')->constrained('sections', 'section_id')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects', 'subject_id')->onDelete('cascade');
            $table->bigInteger('teacher_id');
            $table->foreignId('school_year_id')->constrained('school_years', 'school_year_id')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
}; 