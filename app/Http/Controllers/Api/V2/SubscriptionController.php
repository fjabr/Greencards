<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function getSubscriptionLink(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "subcription_url" => url('/customer-packages-purchase',encrypt($user->id))
        ]);
    }

}
