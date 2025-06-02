<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $primaryKey = 'idmenu';
    protected $table = 'menus';

    protected $fillable = [
        'menu',
        'idkategori',
        'harga',
        'deskripsi',
        'status',
        'gambar'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = true;
    public $timestamps = true;

    /**
     * Relasi ke model Kategori
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'idkategori', 'idkategori');
    }
}
