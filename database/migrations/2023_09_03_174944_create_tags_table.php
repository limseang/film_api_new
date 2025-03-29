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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string("status")->default('1');
            $table->timestamps();
        });

        DB::table('tags')->insert([
            'name' => 'marvel',
            'description' => 'marvel',
        ]);
        DB::table('tags')->insert([
            'name' => 'dc',
            'description' => 'dc',
        ]);
        DB::table('tags')->insert([
            'name' => 'monster_verse',
            'description' => 'monster_verse',
        ]);
        DB::table('tags')->insert([
            'name' => 'star_wars',
            'description' => 'star_wars',
        ]);
        DB::table('tags')->insert([
            'name' => 'harry_potter',
            'description' => 'harry_potter',
        ]);
        DB::table('tags')->insert([
            'name' => 'lord_of_the_rings',
            'description' => 'lord_of_the_rings',
        ]);
        DB::table('tags')->insert([
            'name' => 'fast_and_furious',
            'description' => 'fast_and_furious',
        ]);
        DB::table('tags')->insert([
            'name' => 'transformers',
            'description' => 'transformers',
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
