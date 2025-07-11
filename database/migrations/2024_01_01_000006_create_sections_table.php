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
        Schema::create('sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->string('section_name');
            $table->foreignId('grade_id')->constrained('grades', 'grade_id')->onDelete('cascade');
            $table->bigInteger('teacher_id')->nullable();
            $table->foreignId('school_year_id')->constrained('school_years', 'school_year_id')->onDelete('cascade');
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
}; 