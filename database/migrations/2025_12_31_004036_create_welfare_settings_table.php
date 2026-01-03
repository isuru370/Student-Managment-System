<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Monthly welfare amount
            $table->decimal('amount', 10, 2);

            // Status → 1 = active, 0 = inactive
            $table->tinyInteger('status')->default(1)
                  ->comment('1 = Active, 0 = Inactive');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_settings');
    }
};
