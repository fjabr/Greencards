<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;

class UserPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $packageId = !empty($_GET['package_id']) ? $_GET['package_id'] : null;
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : null;

        if ($userId) $user = User::where('id', $userId)->first();

        if (!$userId || !$packageId || !$user) {
            return response()->json([
                'message' => 'Received invalid parameters.',
                'messageAr' => "تم تلقي معلمات غير صالحة",
                "success" => false,
                "exist" => true,
            ], 400);
        }

        $packageId = (int) $packageId;
        if (empty($user->customer_package_id) || strval($user->customer_package_id) !== strval($packageId)) {
            $packageId = null;
        }

        return response()->json([
            'user' => $user,
            'customerPackageId' => $packageId,
            "success" => true,
            'status'=>200,
            "exist" => false
        ], 200);
    }
}
