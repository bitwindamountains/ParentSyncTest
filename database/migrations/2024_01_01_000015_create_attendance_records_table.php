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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('section_id')->constrained('sections', 'section_id')->onDelete('cascade');
            
            $table->bigInteger('student_id');
            $table->date('date');
            $table->string('status'); // present, absent, late
            $table->text('notes')->nullable();
            $table->bigInteger('marked_by');
            $table->timestamps();
            
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('marked_by')->references('teacher_id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
}; 