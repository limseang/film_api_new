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
        Schema::create('rendom_points', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('gift_id');
            $table->string('point');
            $table->string('phone_number');
            $table->string('code');
            $table->string('status')->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendom_points');
    }
};
