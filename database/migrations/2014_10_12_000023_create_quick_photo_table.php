<?php
// database/migrations/xxxx_xx_xx_000026_create_quick_photo_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickPhotoTable extends Migration
{
    public function up()
    {
        Schema::create('quick_photo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_id');
            $table->string('quick_img');
            $table->foreignId('grade_id')
                ->constrained('grades');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quick_photo');
    }
}
