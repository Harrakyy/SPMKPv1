<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;

    protected $table      = 'mesin';
    protected $primaryKey = 'mesin_id';

    protected $fillable = [
        'permintaan_id',
        'kode_mesin',
        'nama_mesin',
        'jenis_proses',
        'lokasi',
        'status',
        'kapasitas',
        'last_maintenance',
        'dokumen_path',
    ];

    protected $casts = [
        'last_maintenance' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    /** Mesin ini terkait dengan permintaan mana */
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }

    /** Semua proses/activity yang dijadwalkan di mesin ini */
    public function prosesMfg()
    {
        return $this->hasMany(ProsesMfg::class, 'mesin_id', 'mesin_id');
    }
}