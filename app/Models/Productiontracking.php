<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionTracking extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'tracking_id';

    protected $fillable = [
        'mesin_id',
        'permintaan_id',
        'tanggal_po',

        // PO Mekanik
        'po_mekanik_count',
        'po_mekanik_parts',
        'po_mekanik_status',

        // PO Elektrikal
        'po_elektrikal_count',
        'po_elektrikal_parts',
        'po_elektrikal_status',

        // Assy Mekanik
        'tanggal_assy_mekanik',
        'assy_mekanik_status',
        'assy_mekanik_catatan',

        // Assy Elektrikal
        'tanggal_assy_elektrikal',
        'assy_elektrikal_status',
        'assy_elektrikal_catatan',

        // Trial
        'tanggal_trial',
        'tanggal_trial_actual',
        'trial_status',
        'trial_catatan',

        // Delivery
        'tanggal_delivery',
        'tanggal_delivery_actual',
        'delivery_status',
        'delivery_catatan',

        // Metadata
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_po'              => 'date',
        'tanggal_assy_mekanik'    => 'date',
        'tanggal_assy_elektrikal' => 'date',
        'tanggal_trial'           => 'date',
        'tanggal_trial_actual'    => 'date',
        'tanggal_delivery'        => 'date',
        'tanggal_delivery_actual' => 'date',

        // Simpan daftar nama part sebagai JSON array
        'po_mekanik_parts'    => 'array',
        'po_elektrikal_parts' => 'array',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id', 'mesin_id');
    }

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Helper: sinkronisasi dari PartList permintaan ────────────────────

    /**
     * Sinkronkan data PO Mekanik & PO Elektrikal
     * dari part_lists milik permintaan terkait.
     * Dipanggil setelah part ditambah/diedit/dihapus.
     */
    public function syncFromPartLists(): void
    {
        $partLists = $this->permintaan?->partLists ?? collect();

        $mekanik    = $partLists->filter(fn($p) => strtolower($p->purchase ?? '') === 'material');
        $elektrikal = $partLists->filter(fn($p) => strtolower($p->purchase ?? '') === 'electric');

        $this->update([
            'po_mekanik_count'    => $mekanik->count(),
            'po_mekanik_parts'    => $mekanik->pluck('nama_part')->values()->toArray(),
            'po_elektrikal_count' => $elektrikal->count(),
            'po_elektrikal_parts' => $elektrikal->pluck('nama_part')->values()->toArray(),
            'updated_by'          => auth()->id(),
        ]);
    }

    // ── Accessor: preview nama part (max 2 + "...") ──────────────────────

    public function getPoMekanikPreviewAttribute(): string
    {
        $parts = $this->po_mekanik_parts ?? [];
        if (empty($parts)) return '-';
        $preview = array_slice($parts, 0, 2);
        return implode(', ', $preview) . (count($parts) > 2 ? ', ...' : '');
    }

    public function getPoElektrikalPreviewAttribute(): string
    {
        $parts = $this->po_elektrikal_parts ?? [];
        if (empty($parts)) return '-';
        $preview = array_slice($parts, 0, 2);
        return implode(', ', $preview) . (count($parts) > 2 ? ', ...' : '');
    }
}