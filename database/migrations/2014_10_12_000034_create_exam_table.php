<?php
// database/migrations/xxxx_xx_xx_000016_create_exam_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTable extends Migration
{
    public function up()
    {
        Schema::create('exam', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('date');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->foreignId('student_classes_id')
                ->constrained('student_classes');
            $table->boolean('is_canceled');
            $table->timestamps();

            $table->foreign('student_classes_id')->references('id')->on('student_classes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam');
    }
}
