<?php
// database/migrations/xxxx_xx_xx_000007_create_class_category_has_student_class_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassCategoryHasStudentClassTable extends Migration
{
    public function up()
    {
        Schema::create('class_category_has_student_class', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('fees');
            $table->unsignedBigInteger('student_classes_id');
            $table->unsignedBigInteger('class_category_id');
            $table->timestamps();

            $table->foreign('student_classes_id')->references('id')->on('student_classes');
            $table->foreign('class_category_id')->references('id')->on('class_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_category_has_student_class');
    }
}