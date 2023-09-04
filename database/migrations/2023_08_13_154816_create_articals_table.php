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
        Schema::create('articals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('origin_id');
            $table->string('category_id');
            $table->string('image');
            $table->string('type_id');
            $table->string('like')->nullable()->default('0');
            $table->string('comment')->nullable();
            $table->string('share')->nullable()->default('0');
            $table->string('view')->nullable()->default('0');
            $table->string('film')->nullable();
            $table->string('tag')->nullable();
            $table->string('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articals');
    }
};
