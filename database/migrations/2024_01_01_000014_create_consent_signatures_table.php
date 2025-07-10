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
        Schema::create('consent_signatures', function (Blueprint $table) {
            $table->id('signature_id');
            $table->foreignId('form_id')->constrained('consent_forms', 'form_id')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('parents', 'parent_id')->onDelete('cascade');
            $table->bigInteger('student_id');
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_signatures');
    }
}; 