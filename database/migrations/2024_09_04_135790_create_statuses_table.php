<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert initial statuses
        DB::table('statuses')->insert([
            ['name' => 'pending'],
            ['name' => 'reviewed'],
            ['name' => 'evaluated'],
            ['name' => 'approved'],
            ['name' => 'rejected'],
            ['name' => 'started'],
            ['name' => 'on delay'],
            ['name' => 'responded'],
            ['name' => 'completed'],
            ['name' => 'closed'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
