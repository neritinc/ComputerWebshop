<?php

// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name']; // Engedélyezd a company_name mezőt

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

