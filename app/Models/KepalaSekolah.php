<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class KepalaSekolah extends Authenticatable
{
    use Notifiable;

    protected $table = 'kepala_sekolahs';
    protected $fillable = ['nama', 'nip', 'username', 'password', 'foto', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Relationships
    public function validasis()
    {
        return $this->hasMany(Validasi::class, 'id_kepsek');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'id_kepsek');
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active === true;
    }
}
