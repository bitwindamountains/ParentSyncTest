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
        Schema::create('consent_forms', function (Blueprint $table) {
            $table->id('form_id');
            $table->string('title');
            $table->text('description');
            $table->foreignId('event_id')->nullable()->constrained('events', 'event_id')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections', 'section_id')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes', 'class_id')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->date('deadline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_forms');
    }
}; 