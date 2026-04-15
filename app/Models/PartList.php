<?php
//PartList
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartList extends Model
{
    use HasFactory;

    protected $table      = 'part_list';
    protected $primaryKey = 'partlist_id'; // PK sesuai DB

    protected $fillable = [
        'permintaan_id',
        'designer_id',
        'kode_part',
        'nama_part',
        'material',
        'dimensi',
        'dimensi_belanja',
        'quantity',
        'purchase',
        'berat',
        'gambar_part',
        'status_part',
        'catatan',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id', 'permintaan_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id', 'user_id');
    }

    public function prosesMfg()
    {
        // FK di proses_mfg = partlist_id, PK di part_list = partlist_id
        return $this->hasMany(ProsesMfg::class, 'partlist_id', 'partlist_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public static function generateKodePart(int $permintaanId): string
    {
        $pkColumn = (new static())->getKeyName(); // 'partlist_id'

        $lastPart = static::where('permintaan_id', $permintaanId)
            ->orderBy($pkColumn, 'desc')
            ->first();

        $urutan = $lastPart
            ? ((int) substr($lastPart->kode_part, -3)) + 1
            : 1;

        return 'PRT-' . $permintaanId . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }
}