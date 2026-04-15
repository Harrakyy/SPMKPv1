<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id('permintaan_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->string('nomor_permintaan', 50)->unique();
            $table->text('deskripsi_kebutuhan');
            $table->string('jenis_produk', 100)->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'in_progress', 'completed'])->default('draft');
            $table->date('tanggal_permintaan');
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('tanggal_permintaan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan');
    }
};