<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->foreignId('mesin_id')->constrained('mesin', 'mesin_id')->onDelete('cascade');
            $table->foreignId('partlist_id')->constrained('part_list', 'partlist_id')->onDelete('cascade');
            $table->foreignId('mfg_id')->constrained('proses_mfg', 'mfg_id')->onDelete('cascade');
            $table->string('activity', 200);
            $table->foreignId('pic')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('planned');
            $table->enum('machining_status', ['waiting', 'scheduled', 'in_progress', 'completed'])->default('waiting');
            $table->date('tanggal_plan');
            $table->time('start_time_plan')->nullable();
            $table->time('end_time_plan')->nullable();
            $table->date('tanggal_act')->nullable();
            $table->time('start_time_act')->nullable();
            $table->time('end_time_act')->nullable();
            $table->integer('durasi_plan_minutes')->nullable();
            $table->integer('durasi_act_minutes')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('tanggal_plan');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule');
    }
};