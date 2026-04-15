<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password_hash');
            $table->enum('role', ['admin', 'engineer', 'operator']); // hapus 'requester'
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();  // created_at & updated_at selalu di akhir
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};