<?php
// database/migrations/xxxx_xx_xx_000029_create_system_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemUsersTable extends Migration
{
    public function up()
    {
        Schema::create('system_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_id');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->string('fname');
            $table->string('lname');
            $table->string('email');
            $table->string('mobile');
            $table->string('nic');
            $table->string('bday');
            $table->string('gender');
            $table->string('address1');
            $table->string('address2');
            $table->string('address3');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_users');
    }
}
