<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductParameter extends Model
{
    protected $table = 'product_parameter';

    protected $fillable = [
        'product_id',
        'parameter_id',
        'value',
    ];

    public $timestamps = false; // ha nincs created_at / updated_at
}
