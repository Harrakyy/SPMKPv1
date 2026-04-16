<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'partlist_id',
        'mesin_id',
        'pic',
        'tanggal_plan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];
    public function proses()
    {
        return $this->belongsTo(ProsesMfg::class, 'proses_id', 'proses_id');
    }
}