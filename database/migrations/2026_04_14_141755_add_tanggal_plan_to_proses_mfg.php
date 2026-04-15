<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->date('tanggal_plan')->nullable()->after('sequence');
    });
}

public function down()
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->dropColumn('tanggal_plan');
    });
}
};
