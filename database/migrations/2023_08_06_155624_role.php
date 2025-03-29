<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        //create role
        DB::table('roles')->insert([
            'name' => 'admin',
            'description' => 'admin',
        ]);
        DB::table('roles')->insert([
            'name' => 'editor',
            'description' => 'Editor',
        ]);
        DB::table('roles')->insert([
            'name' => 'user',
            'description' => 'user',
        ]);

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
