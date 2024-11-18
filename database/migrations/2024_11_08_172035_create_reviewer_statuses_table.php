<?php
// backend-laravel-server/database/migrations/2024_11_01_000000_create_reviewer_statuses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewerStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewer_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->json('expertise')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();

            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviewer_statuses');
    }
}