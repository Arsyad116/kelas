<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produk'; // Sesuaikan dengan nama tabel yang sudah ada

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'stock',
        'category',
        'is_active'
    ];

    // Alias untuk kolom yang berbeda nama
    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['harga'] ?? null;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['harga'] = $value;
    }

    public function getImageAttribute()
    {
        return $this->attributes['gambar'] ?? null;
    }

    public function setImageAttribute($value)
    {
        $this->attributes['gambar'] = $value;
    }
}
