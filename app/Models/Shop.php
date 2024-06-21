<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

    protected $with = ['offers','branches','shopCity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class, "city_id", "id");
    }
    public function shopCity()
    {
        return $this->belongsTo(City::class, "city_id", "id");
    }
    public function upload()
    {
        return $this->belongsTo(Upload::class, "logo", "id");
    }

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }
    public function offers()
    {
        return $this->hasMany(Offer::class,"id_shop");
    }

    public function branches()
    {
        return $this->hasMany(Branch::class,"shop_id");
    }

    public function followers(){
        return $this->hasMany(FollowSeller::class);
    }


    public function invitationLinks()
    {
        return $this->hasMany(InvitationLink::class,"shop_id");
    }

}
