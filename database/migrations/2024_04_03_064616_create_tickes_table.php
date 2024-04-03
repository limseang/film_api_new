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
        Schema::create('tickes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // code unique
            $table->string('code')->unique();
            $table->string('row');
            $table->string('seat');
            $table->sting('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickes');
    }
};
