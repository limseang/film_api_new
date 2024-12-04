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
        Schema::create('casting_form_models', function (Blueprint $table) {
            $table->id();
            $table->string('casting_id');
            $table->string('casting_role_id');
            $table->string('eng_name');
            $table->string('kh_name');
            $table->string('age');
            $table->string('height');
            $table->string('weight');
            $table->string('gender');
            $table->string('email');
            $table->string('phone');
            $table->string('fb_link')->nullable();
            $table->string('tiktok_link')->nullable();
            $table->text('experience')->nullable();
            $table->text('full_image');
            $table->string('4x6_image');
            $table->string('intro_video');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casting_form_models');
    }
};
