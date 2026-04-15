<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->string('pic')->nullable()->after('proses_nama');
    });
}

public function down(): void
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->dropColumn('pic');
    });
}
};
