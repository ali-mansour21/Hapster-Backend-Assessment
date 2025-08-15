<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'stock'];

    protected $casts = [
        'price'      => 'decimal:2',
        'stock'      => 'integer',
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
