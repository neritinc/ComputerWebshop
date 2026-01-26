<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_item extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    // Kapcsolat a kosárral
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Kapcsolat a termékkel
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}