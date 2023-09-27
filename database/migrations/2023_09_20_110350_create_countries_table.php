<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('flag');
            $table->string('nationality');
            $table->string('status')->default('1');
            $table->timestamps();
        });
        DB::table('countries')->insert([
            'code' => 'starter',
            'name' => '1-100 points',
            'flag' => '1-100 points',
            'nationality' => '1-100 points',
            'status' => '1'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
