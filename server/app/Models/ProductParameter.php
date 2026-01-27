<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductParameter extends Model
{
    protected $table = 'product_parameter'; // pontos név

    protected $fillable = ['product_id', 'parameter_id', 'value'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}

