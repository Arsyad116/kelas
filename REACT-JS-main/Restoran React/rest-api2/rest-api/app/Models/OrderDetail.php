<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $primaryKey = 'idorderdetail';
    public $timestamps = true;

    protected $fillable = [
        'idorder',
        'menu',
        'harga',
        'jumlah',
        'subtotal',
        'catatan'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship dengan order
    public function order()
    {
        return $this->belongsTo(Order::class, 'idorder', 'idorder');
    }
}
