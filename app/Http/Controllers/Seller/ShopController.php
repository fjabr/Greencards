<?php

namespace App\Http\Controllers\Seller;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Country;
use App\Models\SellerSubscriptionReport;
use Illuminate\Http\Request;
use App\Models\Shop;
use Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shop = Shop::with('city')->where('user_id', $user->id)->first();
        $shop_categories = json_decode($shop->categories);
        $categories = DB::table('categories')
            // ->where('categories.parent_id', 0)
            ->where('categories.packages_allowed', "!=", null)
            ->where('categories.is_ecom_category', 0)
            ->select('categories.id', 'categories.name as enName')
            ->get();
        $branches = DB::table("branches")->where("shop_id", $shop->id)->get();
        $countries = Country::where("status","1")->get();
        $cities = City::where("status","1")->where('state_id',$shop->city->state_id)->get();
        return view('seller.shop', compact('cities', 'shop', 'categories', 'branches', 'shop_categories','countries'));
    }

    public function update(Request $request)
    {
        $shop = Shop::find($request->shop_id);

        if ($request->has('name') && $request->has('address')) {
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }

            $shop->name             = $request->name;
            $shop->name_ar          = $request->name_ar;
            $shop->address          = $request->address;
            $shop->address_ar          = $request->address_ar;
            $shop->phone            = $request->phone;
            $shop->slug             = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
            $shop->meta_title       = $request->meta_title;
            $shop->meta_title_ar       = $request->meta_title_ar;
            $shop->meta_description = $request->meta_description;
            $shop->meta_description_ar = $request->meta_description_ar;
            $shop->logo             = $request->logo;
            $city = City::where("id", $request->city_id)->first();
            $shop->city_id             = $request->city_id;
            $shop->city_name             = $city->name;
            $shop->city_name_ar           = $city->name;
        }

        if ($request->has('delivery_pickup_longitude') && $request->has('delivery_pickup_latitude')) {

            $shop->delivery_pickup_longitude    = $request->delivery_pickup_longitude;
            $shop->delivery_pickup_latitude     = $request->delivery_pickup_latitude;
        } elseif (
            $request->has('facebook') ||
            $request->has('google') ||
            $request->has('twitter') ||
            $request->has('youtube') ||
            $request->has('instagram')
        ) {
            $shop->facebook = $request->facebook;
            $shop->instagram = $request->instagram;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } elseif (
            $request->has('top_banner') ||
            $request->has('sliders') ||
            $request->has('banner_full_width_1') ||
            $request->has('banners_half_width') ||
            $request->has('banner_full_width_2')
        ) {
            $shop->top_banner = $request->top_banner;
            $shop->sliders = $request->sliders;
            $shop->banner_full_width_1 = $request->banner_full_width_1;
            $shop->banners_half_width = $request->banners_half_width;
            $shop->banner_full_width_2 = $request->banner_full_width_2;
        }

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function verify_form ()
    {
        if (Auth::user()->shop->verification_info == null) {
            $shop = Auth::user()->shop;
            return view('seller.verify_form', compact('shop'));
        } else {
            flash(translate('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_' . $i]);
            } elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $shop = Auth::user()->shop;
        $shop->verification_info = json_encode($data);
        if ($shop->save()) {
            flash(translate('Your shop verification request has been submitted successfully!'))->success();
            return redirect()->route('seller.dashboard');
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function show()
    {
    }


    public function branches(){
        $user = auth()->user();
        $shop = $user->shop;
        $branches = Shop::where("user_id", $user->id)->join("branches",'branches.shop_id','shops.id')->select("branches.*")->get();
        $cities = City::where("status","1")->get();
        return view('seller.branches', compact( 'branches','shop','cities'));
    }

    public function sellerQrReport(Request $request){
        $user = auth()->user();
        $shop = $user->shop;

        $branchIds = Branch::where("shop_id", $shop->id)->pluck('id')->toArray();

        $sort_search = null;
        $date_range = null;

        $sellerSubscriptions = SellerSubscriptionReport::whereIn("shop_id", $branchIds)->orWhere("shop_id", $shop->id);
        if($request->filled("date_range")){
            $date_range = $request->input("date_range");
            $dates = $this->extractDates($date_range);
            $sellerSubscriptions->where(function ($q) use ($dates){
                $q->where('seller_subscription_reports.created_at', '>=', $dates['start_date'])
                ->where('seller_subscription_reports.created_at', '<=', $dates['end_date']);
            });
        }

        $sellerSubscriptions = $sellerSubscriptions->paginate(10);
        foreach ($sellerSubscriptions as $sellerSubscription) {
            $shop = null;
            if($sellerSubscription->source === 'branch'){
                $shop = Branch::where('id', $sellerSubscription->shop_id)->first();
                $shop->city = $shop->branchCity;
            }else{
                $shop = Shop::where('id', $sellerSubscription->shop_id)->first();
                $shop->city = $shop->shopCity;
            }
            $sellerSubscription->shop = $shop;
        }


        return view('seller.reports', compact('sellerSubscriptions','date_range'));
    }

    private function extractDates($dateString)
    {
        if($dateString == null || empty($dateString )) {
            return [
                'start_date' => null,
                'end_date' => null
            ];
        };
        // Split the string based on the hyphen delimiter
        $dates = explode('/', $dateString);

        // Trim whitespace from the start date and end date
        $formattedStartDate = trim($dates[0]);
        $formattedEndDate = trim($dates[1]);

        return [
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate
        ];
    }
}
