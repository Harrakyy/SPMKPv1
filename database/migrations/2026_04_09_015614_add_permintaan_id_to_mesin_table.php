<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            $table->unsignedBigInteger('permintaan_id')
                  ->nullable()
                  ->after('mesin_id');

            $table->foreign('permintaan_id')
                  ->references('permintaan_id')
                  ->on('permintaan')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            $table->dropForeign(['permintaan_id']);
            $table->dropColumn('permintaan_id');
        });
    }
};