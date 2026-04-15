<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mesin', function (Blueprint $table) {
            $table->id('mesin_id');
            $table->string('kode_mesin', 50)->unique();
            $table->string('nama_mesin', 100);
            $table->string('jenis_proses', 100)->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->decimal('kapasitas', 10, 2)->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->date('last_maintenance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mesin');
    }
};