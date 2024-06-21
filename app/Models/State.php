<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $state_translation = $this->hasMany(StateTranslation::class)->where('lang', $lang)->first();
        return $state_translation != null ? $state_translation->$field : $this->$field;
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
}
