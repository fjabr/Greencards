<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use Cache;
use App;
use Illuminate\Http\Request ;

class CategoryController extends Controller
{

    public function index(Request $request, $parent_id = 0)
    {
        if(request()->has('parent_id') && is_numeric (request()->get('parent_id'))){
          $parent_id = request()->get('parent_id');
        }

        if(request()->has('source') && request()->get('source') == 'greencart-app'){
            $categoryRequest = Category::where('parent_id', $parent_id)->where("is_ecom_category",1);
            return new CategoryCollection($categoryRequest->get());
        }
        $categoryRequest = Category::where('parent_id', $parent_id)->where("is_ecom_category",0);
        return new CategoryCollection($categoryRequest->get());
    }

    public function featured()
    {
        return Cache::remember('app.featured_categories', 86400, function(){
            return new CategoryCollection(Category::where('featured', 1)->get());
        });
    }

    public function home()
    {
        return Cache::remember('app.home_categories', 86400, function(){
            return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->get());
        });
    }

    public function top()
    {
        return Cache::remember('app.top_categories', 86400, function(){
            return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->limit(20)->get());
        });
    }
}
