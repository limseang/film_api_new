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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->longText('video_url');
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->string('cover_image_url');
            $table->integer('film_id')->nullable();
            $table->integer('article_id')->nullable();
            $table->string('type_id');
            $table->string('category_id');
            $table->string('tag_id');
            $table->string('running_time');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
