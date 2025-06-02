<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'idorder';
    public $timestamps = true;

    protected $fillable = [
        'idpelanggan',
        'total',
        'status',
        'catatan'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship dengan pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'idpelanggan', 'idpelanggan');
    }

    // Relationship dengan order details
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'idorder', 'idorder');
    }
}
