<?php
// backend-laravel-server/database/migrations/xxxx_xx_xx_xxxxxx_create_complaints_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('coe');
            $table->text('complaint');
            $table->text('response')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}