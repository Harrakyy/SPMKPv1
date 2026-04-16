<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('production_trackings', function (Blueprint $table) {

            // ── Primary Key ──────────────────────────────────────────────
            $table->id('tracking_id');

            // ── Relasi ───────────────────────────────────────────────────
            $table->unsignedBigInteger('mesin_id');
            $table->unsignedBigInteger('permintaan_id')->nullable();

            // ── Kolom: Tanggal PO ────────────────────────────────────────
            $table->date('tanggal_po')->nullable()
                ->comment('Tanggal purchase order diterbitkan');

            // ── Kolom: PO Mekanik ────────────────────────────────────────
            $table->unsignedSmallInteger('po_mekanik_count')->default(0)
                ->comment('Jumlah part yang dibeli oleh Mechanic');
            $table->text('po_mekanik_parts')->nullable()
                ->comment('Daftar nama part Mechanic (JSON array)');
            $table->enum('po_mekanik_status', [
                'belum_po',
                'proses_po',
                'selesai_po',
            ])->default('belum_po')
                ->comment('Status PO Mekanik');

            // ── Kolom: PO Elektrikal ─────────────────────────────────────
            $table->unsignedSmallInteger('po_elektrikal_count')->default(0)
                ->comment('Jumlah part yang dibeli oleh Electric');
            $table->text('po_elektrikal_parts')->nullable()
                ->comment('Daftar nama part Electric (JSON array)');
            $table->enum('po_elektrikal_status', [
                'belum_po',
                'proses_po',
                'selesai_po',
            ])->default('belum_po')
                ->comment('Status PO Elektrikal');

            // ── Kolom: Assy Mekanik ──────────────────────────────────────
            $table->date('tanggal_assy_mekanik')->nullable()
                ->comment('Tanggal aktual Assy Mekanik selesai');
            $table->enum('assy_mekanik_status', [
                'belum',
                'proses',
                'selesai',
            ])->default('belum')
                ->comment('Status Assy Mekanik');
            $table->text('assy_mekanik_catatan')->nullable()
                ->comment('Catatan tambahan Assy Mekanik');

            // ── Kolom: Assy Elektrikal ───────────────────────────────────
            $table->date('tanggal_assy_elektrikal')->nullable()
                ->comment('Tanggal aktual Assy Elektrikal selesai');
            $table->enum('assy_elektrikal_status', [
                'belum',
                'proses',
                'selesai',
            ])->default('belum')
                ->comment('Status Assy Elektrikal');
            $table->text('assy_elektrikal_catatan')->nullable()
                ->comment('Catatan tambahan Assy Elektrikal');

            // ── Kolom: Trial / Install ───────────────────────────────────
            $table->date('tanggal_trial')->nullable()
                ->comment('Tanggal trial / install dijadwalkan atau dilakukan');
            $table->date('tanggal_trial_actual')->nullable()
                ->comment('Tanggal aktual trial selesai');
            $table->enum('trial_status', [
                'belum',
                'dijadwalkan',
                'selesai',
            ])->default('belum')
                ->comment('Status Trial/Install');
            $table->text('trial_catatan')->nullable()
                ->comment('Catatan hasil trial');

            // ── Kolom: Delivery ──────────────────────────────────────────
            $table->date('tanggal_delivery')->nullable()
                ->comment('Tanggal delivery dijadwalkan');
            $table->date('tanggal_delivery_actual')->nullable()
                ->comment('Tanggal aktual delivery selesai');
            $table->enum('delivery_status', [
                'belum',
                'dijadwalkan',
                'terkirim',
            ])->default('belum')
                ->comment('Status Delivery');
            $table->text('delivery_catatan')->nullable()
                ->comment('Catatan pengiriman / penerima');

            // ── Metadata ─────────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable()
                ->comment('User ID yang membuat record tracking');
            $table->unsignedBigInteger('updated_by')->nullable()
                ->comment('User ID yang terakhir mengupdate');
            $table->timestamps();
            $table->softDeletes();

            // ── Foreign Keys (disabled sementara untuk debug) ─────────────
            // $table->foreign('mesin_id')
            //     ->references('mesin_id')
            //     ->on('mesin')
            //     ->onDelete('cascade');

            // $table->foreign('permintaan_id')
            //     ->references('permintaan_id')
            //     ->on('permintaan')
            //     ->onDelete('set null');

            // $table->foreign('created_by')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('set null');

            // $table->foreign('updated_by')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('set null');

            // ── Index ─────────────────────────────────────────────────────
            $table->unique('mesin_id');
            $table->index('permintaan_id');
            $table->index('tanggal_delivery');
            $table->index('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_trackings');
    }
};