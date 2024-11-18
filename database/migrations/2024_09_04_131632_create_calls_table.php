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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->string('whyApplyTitle');
            $table->text('whyApplyContent');
            $table->json('bulletPoints');
            $table->string('buttonText');
            $table->boolean('isActive')->default(true);
            $table->date('startDate');
            $table->date('endDate');
            $table->string('proposalType');
            $table->boolean('isResubmissionAllowed')->default(false);
            $table->string('coverImage')->nullable(); // Added column for cover image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
