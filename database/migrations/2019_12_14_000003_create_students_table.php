<?php
// database/migrations/xxxx_xx_xx_000014_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_id');
            $table->string('fname');
            $table->string('lname');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('whatsapp_mobile');
            $table->string('nic')->nullable();
            $table->string('bday');
            $table->string('gender');
            $table->string('address1');
            $table->string('address2');
            $table->string('address3')->nullable();
            $table->string('guardian_fname')->nullable();
            $table->string('guardian_lname')->nullable();
            $table->string('guardian_nic')->nullable();
            $table->string('guardian_mobile');
            $table->string('is_active');
            $table->mediumText('img_url');
            $table->unsignedBigInteger('grade_id')
                ->constrained('grades');
            $table->boolean('admission');
            $table->boolean('is_freecard');
            $table->string('student_school')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
