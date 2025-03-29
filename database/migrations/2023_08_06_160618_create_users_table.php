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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('phone');
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('role_id')->nullable()->default('3');
            $table->string('password');
            $table->string ('point')->nullable()->default('1');
            $table->string('user_type')->nullable()->default('1');
            $table->string('status')->default('1');
//            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });




    }

    //create admin user


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
