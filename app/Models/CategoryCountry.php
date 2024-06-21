<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCountry extends Model
{
    use HasFactory;

    protected $fillable=['category_id','country_id'];
}
