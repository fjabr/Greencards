<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SliderCollection;
use App\Http\Resources\V2\SliderWithLinkCollection;
use App\Models\BusinessSetting;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    /* @Deprecated */
    public function sliders(Request $request)
    {
        Log::info($request);
        if ($request->has('lang') && $request->input('lang') == 'ar') {
            return new SliderCollection(json_decode(get_setting('mobile_app_slider_images_ar'), true));
        }
        return new SliderCollection(json_decode(get_setting('mobile_app_slider_images'), true));
    }

    public function sliders_v2(Request $request)
    {
        $images_setting_key = 'mobile_app_slider_images';
        $links_setting_key = 'mobile_app_slider_links';
        if ($request->has('lang') && $request->input('lang') == 'ar') {
            $images_setting_key = 'mobile_app_slider_images_ar';
            $links_setting_key = 'mobile_app_slider_links_ar';
        }
        $images =  json_decode(get_setting($images_setting_key, true));
        $links  =  json_decode(get_setting($links_setting_key, true));
        return response()->json([
            "data" => $this->prepareImageWithLink($images, $links),
            "success" => true,
            "status" => 200
        ]);
    }
    private function prepareImageWithLink($images, $links)
    {
        $result = [];
        for ($i = 0; $i < count($images); $i++) {
            $linkedImage = [
                "photo" => uploaded_asset($images[$i]),
                "link" => $links[$i],
            ];

            array_push($result, $linkedImage);
        }
        return $result;
    }

    public function bannerOne()
    {
        return Cache::remember('app.home_banner1_images', 86400, function () {
            return new SliderCollection(json_decode(get_setting('home_banner1_images'), true));
        });
    }

    public function bannerTwo()
    {
        return Cache::remember('app.home_banner2_images', 86400, function () {
            return new SliderCollection(json_decode(get_setting('home_banner2_images'), true));
        });
    }

    public function bannerThree()
    {
        return Cache::remember('app.home_banner3_images', 86400, function () {
            return new SliderCollection(json_decode(get_setting('home_banner3_images'), true));
        });
    }
}
