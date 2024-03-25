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
        Schema::create('event_plans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('description');
            $table->string('location');
            $table->string('location_link');
            $table->string('start_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('status');
            $table->string('type');
            $table->string('image');
            $table->string('ticket_price');
            $table->string('ticket_quantity');
            $table->string('genre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plans');
    }


};
