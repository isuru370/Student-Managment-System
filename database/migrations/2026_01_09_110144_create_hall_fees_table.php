<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHallFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hall_fees', function (Blueprint $table) {
            $table->id();

            // Foreign key to class_halls table
            $table->foreignId('hall_id')->constrained('class_halls')->onDelete('cascade');

            // Fee amount
            $table->decimal('amount', 10, 2);

            // Status: 1 = active, 0 = inactive
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hall_fees');
    }
}
