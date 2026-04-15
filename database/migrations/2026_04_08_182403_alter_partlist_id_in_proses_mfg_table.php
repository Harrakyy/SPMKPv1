<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proses_mfg', function (Blueprint $table) {
            $table->unsignedBigInteger('partlist_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proses_mfg', function (Blueprint $table) {
            $table->unsignedBigInteger('partlist_id')->nullable(false)->change();
        });
    }
};