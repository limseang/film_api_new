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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('overview');
            $table->string('director')->nullable();
            $table->string('cast')->nullable();
            $table->string('release_date');
            $table->string('category');
            $table->string('tag')->nullable();
            $table->double('rating')->default(0.0);
            $table->string('poster');
            $table->string('cover');
            $table->string('trailer');
            $table->string('type');
            $table->string('running_time');
            $table->string('review')->nullable();
            $table->string('language');
            $table->string('movie')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
