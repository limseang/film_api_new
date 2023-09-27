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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('status')->default('1');
            $table->timestamps();
        });

        DB::table('types')->insert([
            'name' => 'review',
            'description' => 'អត្ថបទ Review',
        ]);
        DB::table('types')->insert([
            'name' => 'news',
            'description' => 'ព័ត៌មាន',
        ]);
        DB::table('types')->insert([
            'name' => 'trailer',
            'description' => 'វីដេអូ Trailer',
        ]);
        DB::table('types')->insert([
            'name' => 'interview',
            'description' => 'វីដេអូ Interview',
        ]);
        DB::table('types')->insert([
            'name' => 'shortfilm',
            'description' => 'វីដេអូ Short Film',
        ]);
        DB::table('types')->insert([
            'name' => 'tvshow',
            'description' => 'វីដេអូ TV Show',
        ]);
        DB::table('types')->insert([
            'name' => 'movie',
            'description' => 'វីដេអូ Movie',
        ]);
        DB::table('types')->insert([
            'name' => 'series',
            'description' => 'វីដេអូ Series',
        ]);
        DB::table('types')->insert([
            'name' => 'nowshowing',
            'description' => 'Now Showing',
        ]);
        DB::table('types')->insert([
            'name' => 'comingsoon',
            'description' => 'Coming Soon',
        ]);
        DB::table('types')->insert([
            'name' => 'promotion',
            'description' => 'Promotion',
        ]);
        DB::table('types')->insert([
            'name' => 'event',
            'description' => 'Event',
        ]);
        DB::table('types')->insert([
            'name' => 'award',
            'description' => 'Award',
        ]);
        DB::table('types')->insert([
            'name' => 'other',
            'description' => 'Other',
        ]);

    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
