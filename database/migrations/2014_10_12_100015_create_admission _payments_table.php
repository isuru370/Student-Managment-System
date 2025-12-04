<?php
// database/migrations/xxxx_xx_xx_000002_create_admission_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('admission_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id');
            $table->double('amount');
            $table->bigInteger('admission_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admission_payments');
    }
}