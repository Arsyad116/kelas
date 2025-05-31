<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners'; // Sesuaikan dengan nama tabel yang sudah ada

    protected $fillable = [
        'image_path',
        'title',
        'description',
        'is_active'
    ];

    // Jika tidak ada timestamps di tabel
    public $timestamps = false;
}
