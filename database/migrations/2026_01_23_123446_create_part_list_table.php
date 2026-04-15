<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('part_list', function (Blueprint $table) {
            $table->id('partlist_id');
            $table->foreignId('permintaan_id')->constrained('permintaan', 'permintaan_id')->cascadeOnDelete();
            $table->foreignId('designer_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->string('kode_part', 50)->unique();
            $table->string('nama_part', 100);
            $table->string('material', 50)->nullable();
            $table->string('dimensi', 100)->nullable();
            $table->string('dimensi_belanja', 100)->nullable();
            $table->integer('quantity')->default(1);
            $table->string('purchase')->default(' ');
            $table->decimal('berat', 10, 2)->nullable();
            $table->string('gambar_part', 255)->nullable();
            $table->enum('status_part', ['draft', 'belum_dibeli', 'dibeli', 'indent', 'ready'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('kode_part');
        });
    }

    public function down()
    {
        Schema::dropIfExists('part_list');
    }
};