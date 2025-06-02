<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model implements AuthenticatableContract
{
    use Authenticatable, SoftDeletes;

    protected $table = 'pelanggans';
    protected $primaryKey = 'idpelanggan';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'alamat',
        'telp',
        'status',
        'api_token'
    ];

    protected $hidden = [
        'password'
    ];

    public function isActive()
    {
        return $this->status === 'aktif';
    }
}
