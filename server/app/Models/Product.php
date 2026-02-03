<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'category_id',
        'company_id',
        'pcs',
        'price',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }

    // Helyes pivot tábla név
    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'product_parameter')
            ->withPivot('value')
            ->withTimestamps();
    }
}
