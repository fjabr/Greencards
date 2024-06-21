<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use App;

class MobileSettingsController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:banner_app_setup'])->only('banner');
    }


    public function banner(Request $request)
    {
        $lang = !$request->filled("lang") ? App::getLocale() : $request->input("lang");
		return view('backend.mobile_settings.banner', compact("lang"));

    }
}
