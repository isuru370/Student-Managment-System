<?php
// database/migrations/2019_12_14_000007_create_class_schedule_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// පැරණි: class CreateClassSheduleTable extends Migration
// නව: class CreateClassScheduleTable extends Migration

class CreateClassScheduleTable extends Migration
{
    public function up()
    {
        Schema::create('class_shedule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('day_of_week');
            $table->boolean('is_ongoing');
            $table->unsignedBigInteger('class_category_has_student_class_id');
            $table->unsignedBigInteger('class_hall_id');
            $table->timestamps();

            $table->foreign('class_category_has_student_class_id')->references('id')->on('class_category_has_student_class');
            $table->foreign('class_hall_id')->references('id')->on('class_halls');
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_shedule');
    }
}