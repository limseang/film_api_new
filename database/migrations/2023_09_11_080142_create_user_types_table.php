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
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('status')->default('1');
            $table->timestamps();
        });

        DB::table('user_types')->insert([
            'name' => 'starter',
            'description' => '1-100 points',
        ]);
        DB::table('user_types')->insert([
            'name' => 'intermediate',
            'description' => '101-500 points',
        ]);
        DB::table('user_types')->insert([
            'name' => 'advanced',
            'description' => '501-1000 points',
        ]);
        DB::table('user_types')->insert([
            'name' => 'expert',
            'description' => '1001-2000 points',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
