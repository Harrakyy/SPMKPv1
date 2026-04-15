<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import model relasi
use App\Models\Permintaan;
use App\Models\PartList;
use App\Models\ProsesMfg;
use App\Models\Schedule;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'nama',
        'email',
        'password_hash',
        'role',
        'last_login',
        'jabatan',
        'nomor_hp',
    ];

    // Kolom yang disembunyikan
    protected $hidden = [
        'password_hash',
        'remember_token'
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTH PASSWORD FIELD
    |--------------------------------------------------------------------------
    | Laravel menggunakan field password, tetapi database kita password_hash
    */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Permintaan yang dibuat user
    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'user_id');
    }

    // Part yang didesign
    public function designedParts()
    {
        return $this->hasMany(PartList::class, 'designer_id');
    }

    // Proses manufaktur oleh operator
    public function prosesMfg()
    {
        return $this->hasMany(ProsesMfg::class, 'operator_id');
    }

    // Schedule sebagai PIC
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'pic');
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDesign()
    {
        return $this->role === 'engineer';
    }

    public function isMachining()
    {
        return $this->role === 'operator';
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE NAME UNTUK TAMPILAN UI
    |--------------------------------------------------------------------------
    */

    public function getRoleNameAttribute()
    {
        return match($this->role) {
            'admin' => 'Admin',
            'engineer' => 'Engineer',
            'operator' => 'Operator',
            default => 'User'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | NAMA DARI EMAIL (OPSIONAL)
    |--------------------------------------------------------------------------
    | Jika nama kosong, otomatis ambil dari email
    */

    public function getDisplayNameAttribute()
    {
        if ($this->nama) {
            return $this->nama;
        }

        $name = explode('@', $this->email)[0];
        return ucfirst($name);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE LAST LOGIN
    |--------------------------------------------------------------------------
    */

    public function updateLastLogin()
    {
        $this->update([
            'last_login' => now()
        ]);
    }
}