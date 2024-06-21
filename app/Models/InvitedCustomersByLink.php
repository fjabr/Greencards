<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitedCustomersByLink extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(User::class,"user_id", "id");
    }

    public function nbrInvitaions()
    {
        return InvitationLink::where('id',$this->invitation_link_id)->count();
    }

}
