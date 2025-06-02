<?php

namespace App\Models;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';
    protected $primaryKey = 'idkategori';

    protected $fillable = [
        'kategori',
        'deskripsi',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = true;
    public $timestamps = true;

    /**
     * Relasi ke model Menu
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'idkategori', 'idkategori');
    }
}
