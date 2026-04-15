<?php
//ProsesMfg
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesMfg extends Model
{
    use HasFactory;

    protected $table      = 'proses_mfg';
    protected $primaryKey = 'mfg_id';

    protected $fillable = [
        'mesin_id',
        'partlist_id',
        'proses_nama',
        'pic',
        'tanggal_plan',
        'tanggal_actual',
        'request_id',
        'status',
        'start_time',
        'end_time',
        'catatan',
        
    ];

    protected $casts = [
        'tanggal_plan'   => 'date',
        'tanggal_actual' => 'date',
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id', 'mesin_id');
    }

    public function partList()
    {
        // FK di proses_mfg = partlist_id, PK di part_list = partlist_id
        return $this->belongsTo(PartList::class, 'partlist_id', 'partlist_id');
    }
}