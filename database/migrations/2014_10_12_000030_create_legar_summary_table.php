<?php
// database/migrations/xxxx_xx_xx_000024_create_legar_summary_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegarSummaryTable extends Migration
{
    public function up()
    {
        Schema::create('legar_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('month');
            $table->double('amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('legar_summary');
    }
}