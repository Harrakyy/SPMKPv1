<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            $table->date('tanggal_trial')->nullable()->after('last_maintenance');
            $table->date('tanggal_delivery')->nullable()->after('tanggal_trial');
        });
    }

    public function down(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            $table->dropColumn(['tanggal_trial', 'tanggal_delivery']);
        });
    }
};