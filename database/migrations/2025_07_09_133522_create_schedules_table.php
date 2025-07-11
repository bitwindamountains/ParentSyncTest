<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('Schedules', function (Blueprint $table) {
        $table->id('schedule_id');
        $table->unsignedBigInteger('subject_id');
        $table->unsignedBigInteger('section_id');
        $table->string('day_of_week', 20)->nullable();
        $table->time('start_time')->nullable();
        $table->time('end_time')->nullable();

        $table->foreign('subject_id')->references('subject_id')->on('Subjects')->onDelete('cascade');
        $table->foreign('section_id')->references('section_id')->on('Sections')->onDelete('cascade');
    });
}

};
