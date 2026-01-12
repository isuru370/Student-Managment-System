<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('welfare_expenses', function (Blueprint $table) {
            $table->id();

            // Auto-generated receipt number (SA-YYYY-XXXX format)
            $table->string('receipt_no')->unique();

            // Expense details
            $table->string('expense_for'); // Example: 'සුබසාධන අරමුදල', 'ගුරු සුබසාධන', etc.
            $table->string('expense_type'); // Example: 'medical', 'education', 'emergency', 'general'
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');

            // Payment details
            $table->string('payment_method')->default('cash');

            //Recording
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');

            $table->text('description')->nullable();
            $table->text('remarks')->nullable();

            $table->tinyInteger('status')->default(1)
                ->comment('1 = Approved/Paid, 0 = Cancelled');

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['expense_date']);
            $table->index('expense_type');
            $table->index('status');
            $table->index('receipt_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welfare_expenses');
    }
};
