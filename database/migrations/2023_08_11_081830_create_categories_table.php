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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('1');
        });

        DB::table('categories')->insert([
            'name' => 'action',
            'description' => 'វាយប្រហារ',
        ]);
        DB::table('categories')->insert([
            'name' => 'adventure',
            'description' => 'ផ្សងព្រេង',
        ]);
        DB::table('categories')->insert([
            'name' => 'animation',
            'description' => 'តុក្តតា',
        ]);
        DB::table('categories')->insert([
            'name' => 'comedy',
            'description' => 'កំប្លែង',
        ]);
        DB::table('categories')->insert([
            'name' => 'crime',
            'description' => 'ឧក្រិដ្ឋកម្ម',
        ]);
        DB::table('categories')->insert([
            'name' => 'drama',
            'description' => 'ស្នេហា',
        ]);
        DB::table('categories')->insert([
            'name' => 'family',
            'description' => 'គ្រួសារ',
        ]);
        DB::table('categories')->insert([
            'name' => 'fantasy',
            'description' => 'ផ្សងព្រេង',
        ]);
        DB::table('categories')->insert([
            'name' => 'history',
            'description' => 'ប្រវត្តិសាស្ត្រ',
        ]);
        DB::table('categories')->insert([
            'name' => 'horror',
            'description' => 'រន្ធត់',
        ]);
        DB::table('categories')->insert([
            'name' => 'musical',
            'description' => 'តន្ត្រី',
        ]);
        DB::table('categories')->insert([
            'name' => 'mystery',
            'description' => 'ស៊ើបអង្កេត',
        ]);
        DB::table('categories')->insert([
            'name' => 'romance',
            'description' => 'រឿងស្នេហា',
        ]);
        DB::table('categories')->insert([
            'name' => 'documentary',
            'description' => 'រឿងអនុវត្ត',
        ]);
        DB::table('categories')->insert([
            'name' => 'science_fiction',
            'description' => 'រឿងអនុវត្តន៍',
        ]);
        DB::table('categories')->insert([
            'name' => 'thriller',
            'description' => 'រឿងទូរទស្សន៍',
        ]);
        DB::table('categories')->insert([
            'name' => 'western',
            'description' => 'រឿងកុមារ',
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
