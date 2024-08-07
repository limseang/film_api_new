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
        Schema::create('premium_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('payment_id');
            $table->string('register_date');
            $table->string('expired_date');
            $table->string('payment_method');
            $table->string('recipe_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_users');
    }
};
