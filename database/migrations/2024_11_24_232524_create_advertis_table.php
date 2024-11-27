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
        Schema::create('advertis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('image');
            $table->string('link');
            $table->string('payment_by');
            $table->string('payment_status');
            $table->string('payment_date');
            $table->string('accept_date');
            $table->string('receipt');
            $table->string('accept_by');
            $table->string('come_from');
            $table->string('expire_date');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertis');
    }
};
