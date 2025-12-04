<?php
// database/migrations/xxxx_xx_xx_000025_create_titute_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTituteTable extends Migration
{
    public function up()
    {
        Schema::create('titute', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id');
            $table->bigInteger('class_category_has_student_class_id');
            $table->string('titute_for', 50);
            $table->boolean('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('titute');
    }
}