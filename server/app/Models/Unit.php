<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    

    // Alapértelmezett táblanév: 'units', nem szükséges megadni, ha egyezik
    protected $fillable = ['unit_name'];  // Mezők, amiket tömegesen kitölthetünk
}
