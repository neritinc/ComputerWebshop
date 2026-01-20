<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'category_id', 
        'company_id', 
        'pcs', 
        'price', 
        'description',
    ];

    // Kapcsolat a kategóriával
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Kapcsolat a céggel
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Kapcsolat a paraméterekkel (pivot tábla)
    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'product_parameter')  // A pivot tábla
                    ->withPivot('value') // Tároljuk az értékeket a pivot táblában
                    ->withTimestamps();  // Timestamps hozzáadása
    }
}
