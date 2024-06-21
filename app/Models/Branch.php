<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Branch extends Model
{
    protected $with = ['branchCity'];
    public function branchCity()
    {
        return $this->belongsTo(City::class, "city_id", "id");
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, "shop_id", "id");
    }

}
