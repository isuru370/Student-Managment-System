<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_payments', function (Blueprint $table) {
            $table->id();

            // Foreign key → teacher (mandatory)
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->onDelete('restrict');

            // Foreign key → system user who recorded the payment
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict');

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('salary_deduction');
            $table->tinyInteger('status')->default(1)
                ->comment('1 = Paid/Completed, 0 = Deleted');
            $table->text('description')->nullable();

            // Soft delete
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['teacher_id']);
            $table->index('payment_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_payments');
    }
    
};
