<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $fillable = ['parameter_name', 'category_id', 'unit_id'];

    // Kapcsolat a termékekkel
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_parameter')  // A pivot tábla
                    ->withPivot('value')  // A paraméterek értéke a pivot táblában
                    ->withTimestamps();  // Timestamps hozzáadása
    }
}
