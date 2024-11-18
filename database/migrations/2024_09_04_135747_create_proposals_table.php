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
        if (!Schema::hasTable('proposals')) {
            Schema::create('proposals', function (Blueprint $table) {
                $table->id();
                $table->string('COE');
                $table->string('proposal_title');
                $table->text('proposal_abstract');
                $table->text('proposal_introduction');
                $table->text('proposal_literature');
                $table->text('proposal_methodology');
                $table->text('proposal_results');
                $table->text('proposal_reference');
                $table->date('proposal_start_date');
                $table->date('proposal_end_date');
                $table->decimal('proposal_budget', 15, 2);
                $table->decimal('remaining_budget', 15, 2)->default(0);

                // Foreign key for user_id
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                
                // Foreign key for call_id
                $table->foreignId('call_id')->constrained()->onDelete('cascade');
        
                $table->timestamps();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
