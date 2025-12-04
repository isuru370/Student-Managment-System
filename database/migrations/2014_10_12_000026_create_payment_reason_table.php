<?php
// database/migrations/xxxx_xx_xx_000022_create_payment_reason_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentReasonTable extends Migration
{
    public function up()
    {
        Schema::create('payment_reason', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reason_code');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_reason');
    }
}