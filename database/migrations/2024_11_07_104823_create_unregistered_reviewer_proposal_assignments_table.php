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
        Schema::create('unregistered_reviewer_proposal_assignments', function (Blueprint $table) {
            $table->id();
            
            // Specify a unique name for each foreign key constraint
            $table->foreignId('unregistered_reviewer_id')
                  ->constrained('unregistered_reviewers')
                  ->onDelete('cascade')
                  ->index('idx_unregistered_reviewer'); // Unique index name
            
            $table->foreignId('proposal_id')
                  ->constrained('proposals')
                  ->onDelete('cascade')
                  ->index('idx_proposal'); // Unique index name
            
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('request_status')->default('pending'); // pending, accepted, rejected
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unregistered_reviewer_proposal_assignments');
    }
};
