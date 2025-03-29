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
        Schema::create('request_films', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('film_name');
            $table->string('film_link');
            $table->string('film_image');
            $table->string('film_description');
            $table->string('noted');
            $table->string('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_films');
    }
};
