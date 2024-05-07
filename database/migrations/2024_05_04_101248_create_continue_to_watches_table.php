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
        Schema::create('continue_to_watches', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('film_id');
            $table->string('film_type');
            $table->string('episode_id');
            $table->string('duration');
            $table->string('progressing');
            $table->string('watched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('continue_to_watches');
    }
};
