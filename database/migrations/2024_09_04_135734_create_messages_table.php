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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->string('sender_type')->nullable(); // Store the sender type
            $table->foreignId('receiver_id')->constrained('users')->nullable();
            $table->string('message_subject');
            $table->text('message_content');
            $table->boolean('is_broadcast')->default(false); // Indicate if it's a broadcast message
            $table->json('attachments')->nullable(); // Store file attachments as JSON
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
