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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            'transaction_id' => 'required|string|unique:transactions',
            'transaction_date' => 'required|date',
            'transaction_amount' => 'required|numeric',
            'transaction_type' => 'required|string',
            'transaction_description' => 'nullable|string',
            'fund_request_id' => 'required|exists:fund_requests,id',
            'user_id' => 'required|exists:users,id',
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
