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
        Schema::create('subcripts', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name');
            $table->string('duration');
            $table->string('price');
            $table->string('description');
            $table->string('supplier_code');
            $table->uuid('uuid')->unique(); // Ensures UUID is a valid format and unique
            $table->string('status');
            $table->timestamps(); // Automatically creates `created_at` and `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcripts');
    }
};
