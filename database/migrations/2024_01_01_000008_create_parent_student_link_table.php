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
        Schema::create('parent_student_link', function (Blueprint $table) {
            $table->id('link_id');
            $table->foreignId('parent_id')->constrained('parents', 'parent_id')->onDelete('cascade');
            $table->bigInteger('student_id');
            $table->boolean('verified')->default(false);
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student_link');
    }
}; 