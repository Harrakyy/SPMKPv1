<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proses_mfg', function (Blueprint $table) {
            $table->id('mfg_id');
            $table->foreignId('partlist_id')->constrained('part_list', 'partlist_id')->onDelete('cascade');
            $table->integer('sequence')->default(1);
            $table->string('proses_nama', 100);
            $table->foreignId('mesin_id')->nullable()->constrained('mesin', 'mesin_id')->nullOnDelete();
            $table->enum('status', ['pending', 'running', 'completed', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->integer('hasil_ok')->default(0);
            $table->integer('hasil_ng')->default(0);
            $table->timestamps();
            
            $table->index('status');
            $table->index('partlist_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proses_mfg');
    }
};