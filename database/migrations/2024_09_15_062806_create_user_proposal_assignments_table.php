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
        
        Schema::create('user_proposal_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**cr
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_proposal_assignments');
    }
};
