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
        Schema::create('ConsentFormRecipients', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('form_id');
            $table->bigInteger('student_id');

            $table->foreign('form_id')->references('form_id')->on('consent_forms')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ConsentFormRecipients');
    }
};
