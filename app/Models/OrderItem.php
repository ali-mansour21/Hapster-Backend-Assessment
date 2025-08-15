<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'qty', 'price'];

    public function order()
    {
        $this->belongsTo(Order::class);
    }
    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
