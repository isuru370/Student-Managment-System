<?php
// database/migrations/xxxx_xx_xx_000023_create_extra_incomes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('extra_incomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reason');
            $table->bigInteger('amount');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('extra_incomes');
    }
}