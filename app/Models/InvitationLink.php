<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationLink extends Model
{
    use HasFactory;

    public function shop()
    {
        return $this->belongsTo(Shop::class,"shop_id", "id");
    }

    public function invitationLinks()
    {
        return $this->hasMany(InvitedCustomersByLink::class,"invitation_link_id");
    }
    public function package()
    {
        return $this->belongsTo(CustomerPackage::class,"package_id");
    }
}
