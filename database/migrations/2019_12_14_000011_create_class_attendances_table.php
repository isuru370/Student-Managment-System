<?php
// database/migrations/xxxx_xx_xx_000010_create_class_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('start');
            $table->string('end');
            $table->string('status');
            $table->unsignedBigInteger('class_category_has_student_class_id');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('day_of_week');
            $table->boolean('is_ongoing');
            $table->unsignedBigInteger('class_hall_id');
            $table->string('date');
            $table->timestamps();

            $table->foreign('class_category_has_student_class_id')->references('id')->on('class_category_has_student_class');
            $table->foreign('class_hall_id')->references('id')->on('class_halls');
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_attendances');
    }
}