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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('scope'); // school, grade, section, class
            $table->foreignId('school_id')->nullable()->constrained('schools', 'school_id')->onDelete('cascade');
            $table->foreignId('grade_id')->nullable()->constrained('grades', 'grade_id')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections', 'section_id')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes', 'class_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
}; 