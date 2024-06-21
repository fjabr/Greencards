<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\SliderCollection;
use App\Http\Resources\V2\SliderWithLinkCollection;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Upload;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App;

class SliderController extends Controller
{
    /* @Deprecated */
    public function sliders(Request $request)
    {
        $lang = App::getLocale();
        if($request->filled('source') && $request->input('source') == 'greencart-app'){
            return new SliderCollection(json_decode(get_setting('slider_greencard_app_image','',$lang ), true));
        }
        $lang = App::getLocale();
        if ($request->has('lang') && $request->input('lang') == 'ar') {
            return new SliderCollection(json_decode(get_setting('mobile_app_slider_images_ar'), true));
        }
        return new SliderCollection(json_decode(get_setting('mobile_app_slider_images','',$lang ), true));
    }

    public function sliders_v2(Request $request)
    {
        $lang = App::getLocale();
        $images_setting_key = 'mobile_app_slider_images';
        $links_setting_key = 'mobile_app_slider_links';

        if ($request->has('lang') && $request->input('lang') == 'ar') {
            $images_setting_key = 'mobile_app_slider_images_ar';
            $links_setting_key = 'mobile_app_slider_links_ar';
        }

        if($request->filled('source') && $request->input('source') == 'greencart-app'){
            $images_setting_key = 'slider_greencard_app_image';
            $links_setting_key = 'slider_greencard_app_link';
        }
        $images =  json_decode(get_setting($images_setting_key,'',$lang));
        $links  =  json_decode(get_setting($links_setting_key ,'',$lang));
        return response()->json([
            "data" => $this->prepareImageWithLink($images, $links),
            "success" => true,
            "status" => 200
        ]);
    }
    public function recommendedSellers(Request $request)
    {
        $lang = App::getLocale();


        $images_setting_key = 'recommended_sellers_images';
        $links_setting_key = 'recommended_sellers_links';
        if ($request->has('lang') && $request->input('lang') == 'ar') {
            $images_setting_key = 'recommended_sellers_images_ar';
            $links_setting_key = 'recommended_sellers_links_ar';
        }
        $images =  json_decode(get_setting($images_setting_key,'',$lang));
        $links  =  json_decode(get_setting($links_setting_key,'',$lang));
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

    public function getBigSaleBanner(Request $request)
    {
        $lang = false;
        if($request->filled("lang"))
            $lang = App::getLocale();

        if (get_setting('big_salles_image','',$lang) != null && get_setting('big_salles_value','',$lang) != null) {

            return Cache::remember('app.big_salles_products-'.$lang, 86400, function () use ($lang){
                $bigSalePhotos =  json_decode(get_setting('big_salles_image','',$lang), true);
                $bigSaleProducts =  json_decode(get_setting('big_salles_value','',$lang), true);
                $banners = [];
                foreach ($bigSaleProducts as $key => $value) {
                    $products = Product::where("discount_type", 'percent')
                        ->where("discount", '<=', $value)
                        ->select("name", 'unit_price', 'thumbnail_img', 'id')
                        ->physical();
                    array_push($banners, [
                        "products" => new ProductMiniCollection(filter_products($products)->limit(10)->get()),
                        "photo" => uploaded_asset($bigSalePhotos[$key])
                    ]);
                }


                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'data' =>$banners
                ]);
            });
        }
    }
    public function ProductLessThanPrice(Request $request)
    {
        $lang = false;
        if($request->filled("lang"))
            $lang = App::getLocale();

        if (get_setting('product_less_than_price_image','',$lang) != null && get_setting('product_less_than_price_value','',$lang) != null) {

            return Cache::remember('app.products_less_than_price-'.$lang, 86400, function () use ($lang){
                $productLessThanPricePhotos =  json_decode(get_setting('product_less_than_price_image','',$lang), true);
                $ProductsLessThanPrice =  json_decode(get_setting('product_less_than_price_value','',$lang), true);
                $banners = [];
                foreach ($ProductsLessThanPrice as $key => $value) {
                    $products = Product::where("unit_price", '<=', $value)
                        ->select("name", 'unit_price', 'thumbnail_img', 'id')
                        ->physical();
                    array_push($banners, [
                        "products" => new ProductMiniCollection(filter_products($products)->limit(10)->get()),
                        "photo" => uploaded_asset($productLessThanPricePhotos[$key])
                    ]);
                }


                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'data' =>$banners
                ]);
            });
        }
    }
}
