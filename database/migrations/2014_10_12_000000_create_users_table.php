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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('nickname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->date('birthday');
            $table->text('avatar');
            $table->boolean('is_deleted')->default(false);
            $table->string('remember_token', 60)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
