<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\Brand;
use App\Models\Product;
use App\Models\PickupPoint;
use App\Models\CustomerPackage;
use App\Models\User;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use Cookie;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;
use Illuminate\Auth\Events\PasswordReset;
use Cache;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $featured_categories = Cache::rememberForever('featured_categories', function () {
            return Category::where('featured', 1)->where('packages_allowed', null)->get();
        });

        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::where('published', 1)->where('todays_deal', '1'))->get();            
        });

        return view('frontend.index', compact('featured_categories', 'todays_deal_products'));
    }

    public function login()
    {
        if(Auth::check()){
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    public function registration(Request $request)
    {
        if(Auth::check()){
            return redirect()->route('home');
        }
        if($request->has('referral_code') &&
                \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null &&
                \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {

            try {
                $affiliate_validation_time = \App\AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {

            }
        }
        return view('frontend.user_registration');
    }

    public function cart_login(Request $request)
    {
        $user = null;
        if($request->get('phone') != null){
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('phone', "+{$request['country_code']}{$request['phone']}")->first();
        }
        elseif($request->get('email') != null){
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->first();
        }
        
        if($user != null){
            if(Hash::check($request->password, $user->password)){
                if($request->has('remember')){
                    auth()->login($user, true);
                }
                else{
                    auth()->login($user, false);
                }
            }
            else {
                flash(translate('Invalid email or password!'))->warning();
            }
        }
        else{
            flash(translate('Invalid email or password!'))->warning();
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::user()->user_type == 'seller'){
            return view('frontend.user.seller.dashboard');
        }
        elseif(Auth::user()->user_type == 'customer'){
            return view('frontend.user.customer.dashboard');
        }
        elseif(Auth::user()->user_type == 'delivery_boy'){
            return view('delivery_boys.frontend.dashboard');
        }
        else {
            abort(404);
        }
    }
    
    public function contract(Request $req){
        
        // return $req;
        
        $seller_id = DB::table("sellers")->where("user_id" ,"=" ,Auth::user()->id)->first();
        
        if($seller_id->id == null){
            return redirect("/offers");
        }
        
        $msg = [];
        $contract = [
              "date_create" => "",
              "company_name" => "",
              "company_name_ar" => "",
              "comm_reg_no" => "",
              "vat_reg" => "",
              "opr_as" => "",
              "opr_as_ar" => "",
              "contact_person" => "",
              "contact_person_ar" => "",
              "email" => "",
              "phone" => "",
              "offer_price" => "",
              "average_price" => "",
              "offers" => "",
              "types_offers" => "",
              "type_offer_discount" => "",
              "activation_peroid_from" => "",
              "activation_peroid_expr" => "",
              "fees" => "",
              "entry_fee" => "",
              "commission" => "",
              "job_title" => "",
              "currency" => "SAR",
              "seller_id" => $seller_id->id 
        ];
        
        if($req->has("date_create") || $req->has("date_create_ar")){
            if($req->date_create != null){
                $contract["date_create"] = $req->date_create;
            }
            if($req->date_create_ar != null){
                $contract["date_create"] = $req->date_create_ar;
            }
            
            if($req->date_create == null && $req->date_create_ar == null){
                array_push($msg, "Date required");
            }
            
        }else{
            array_push($msg, "Date required");
        }
        
        if($req->has("company_name") || $req->has("company_name_ar")){
            if($req->company_name != null){
                $contract["company_name"] = $req->company_name;
            }
            if($req->company_name_ar != null){
                $contract["company_name_ar"] = $req->company_name_ar;
            }
            
            if($req->company_name == null && $req->company_name_ar == null){
                array_push($msg, "Company name required");
            }
            
        }else{
            array_push($msg, "Company name required");
        }
        
        if($req->has("comm_reg_no") || $req->has("comm_reg_no_ar")){
            if($req->comm_reg_no != null){
                $contract["comm_reg_no"] = $req->comm_reg_no;
            }
            if($req->comm_reg_no_ar != null){
                $contract["comm_reg_no"] = $req->comm_reg_no_ar;
            }
            
            if($req->comm_reg_no == null && $req->comm_reg_no_ar == null){
                array_push($msg, "under Commercial Registration No, is required");
            }
            
        }else{
            array_push($msg, "under Commercial Registration No, is required");
        }
        
        if($req->has("vat_reg") || $req->has("vat_reg_ar")){
            if($req->vat_reg != null){
                $contract["vat_reg"] = $req->vat_reg;
            }
            if($req->comm_reg_no_ar != null){
                $contract["vat_reg"] = $req->vat_reg_ar;
            }
            
            if($req->vat_reg == null && $req->vat_reg_ar == null){
                array_push($msg, "VAT registration number is required");
            }
            
        }else{
            array_push($msg, "VAT registration number is required");
        }
        
        if($req->has("opr_as") || $req->has("opr_as_ar")){
            if($req->opr_as != null){
                $contract["opr_as"] = $req->opr_as;
            }
            if($req->opr_as_ar != null){
                $contract["opr_as_ar"] = $req->opr_as_ar;
            }
            
            if($req->opr_as == null && $req->opr_as_ar == null){
                array_push($msg, "It operates as required");
            }
            
        }else{
            array_push($msg, "It operates as required");
        }
        
        if($req->has("contact_person") || $req->has("contact_person_ar")){
            if($req->contact_person != null){
                $contract["contact_person"] = $req->contact_person;
            }
            if($req->contact_person_ar != null){
                $contract["contact_person_ar"] = $req->contact_person_ar;
            }
            
            if($req->contact_person == null && $req->contact_person_ar == null){
                array_push($msg, "Contact person required");
            }
            
        }else{
            array_push($msg, "Contact person required");
        }
        
        if($req->has("email") || $req->has("email_ar")){
            if($req->email != null){
                $contract["email"] = $req->email;
            }
            if($req->email_ar != null){
                $contract["email"] = $req->email_ar;
            }
            
            if($req->email == null && $req->email_ar == null){
                array_push($msg, "Email required");
            }
            
        }else{
            array_push($msg, "Email required");
        }
        
        if($req->has("phone") || $req->has("email_ar")){
            if($req->phone != null){
                $contract["phone"] = $req->phone;
            }
            if($req->phone_ar != null){
                $contract["phone"] = $req->phone_ar;
            }
            
            if($req->phone == null && $req->phone_ar == null){
                array_push($msg, "Phone required");
            }
            
        }else{
            array_push($msg, "Phone required");
        }
        
        if($req->has("offer_price") || $req->has("offer_price_ar")){
            if($req->offer_price != null){
                $contract["offer_price"] = $req->offer_price;
            }
            if($req->offer_price_ar != null){
                $contract["offer_price"] = $req->offer_price_ar;
            }
            
            if($req->offer_price == null && $req->offer_price_ar == null){
                array_push($msg, "Offer price required");
            }
            
        }else{
            array_push($msg, "Offer price required");
        }
        
        if($req->has("average_price") || $req->has("average_price_ar")){
            if($req->average_price != null){
                $contract["average_price"] = $req->average_price;
            }
            if($req->average_price_ar != null){
                $contract["average_price"] = $req->average_price_ar;
            }
            
            if($req->average_price == null && $req->average_price_ar == null){
                array_push($msg, "Average price Redemption amount required");
            }
            
        }else{
            array_push($msg, "Average price Redemption amount required");
        }
        
        if($req->has("offers") || $req->has("offers_ar")){
            if($req->offers != null){
                $contract["offers"] = implode(",", $req->offers);
            }
            if($req->offers_ar != null){
                $contract["offers"] = implode(",", $req->offers_ar);
            }
            
            if($req->offers == null && $req->offers_ar == null){
                array_push($msg, "Offers required");
            }
            
        }else{
            array_push($msg, "Offers required");
        }
        
        if($req->has("types_offers") || $req->has("types_offers_ar")){
            if($req->types_offers != null){
                $contract["types_offers"] = implode(",", $req->types_offers);
            }
            if($req->offers_ar != null){
                $contract["types_offers"] = implode(",", $req->types_offers_ar);
            }
            
        }
        
        // Offer type Discount rate
        if($req->has("type_offer_discount") || $req->has("type_offer_discount_ar")){
            if($req->type_offer_discount != null){
                $contract["type_offer_discount"] = $req->type_offer_discount;
            }
            if($req->type_offer_discount_ar != null){
                $contract["type_offer_discount"] = $req->type_offer_discount_ar;
            }
            
            if($req->type_offer_discount == null && $req->type_offer_discount_ar == null){
                array_push($msg, "Offer type Discount rate is required");
            }
            
        }else{
            array_push($msg, "Offer type Discount rate is required");
        }
        
        if($req->has("activation_peroid_from") || $req->has("activation_peroid_from_ar")){
            if($req->activation_peroid_from != null){
                $contract["activation_peroid_from"] = $req->activation_peroid_from;
            }
            if($req->activation_peroid_from_ar != null){
                $contract["activation_peroid_from"] = $req->activation_peroid_from_ar;
            }
            
            if($req->activation_peroid_from == null && $req->activation_peroid_from_ar == null){
                array_push($msg, "Activation period starts from, is required");
            }
            
        }else{
            array_push($msg, "Activation period starts from, is required");
        }
        
        if($req->has("activation_peroid_expr") || $req->has("activation_peroid_expr_ar")){
            if($req->activation_peroid_expr != null){
                $contract["activation_peroid_expr"] = $req->activation_peroid_expr;
            }
            if($req->activation_peroid_expr_ar != null){
                $contract["activation_peroid_expr"] = $req->activation_peroid_expr_ar;
            }
            
            if($req->activation_peroid_expr == null && $req->activation_peroid_expr_ar == null){
                array_push($msg, "Expiry date required");
            }
            
        }else{
            array_push($msg, "Expiry date required");
        }
        
        if($req->has("fees") || $req->has("fees_ar")){
            if($req->fees != null){
                $contract["fees"] = $req->fees;
            }
            if($req->fees_ar != null){
                $contract["fees"] = $req->fees_ar;
            }
            
            if($req->fees == null && $req->fees_ar == null){
                array_push($msg, "Fees required");
            }
            
        }else{
            array_push($msg, "Fees required");
        }
        
        if($req->has("entry_fee") || $req->has("entry_fee_ar")){
            if($req->entry_fee != null){
                $contract["entry_fee"] = $req->entry_fee;
            }
            if($req->entry_fee_ar != null){
                $contract["entry_fee"] = $req->entry_fee_ar;
            }
            
            if($req->entry_fee == null && $req->entry_fee_ar == null){
                array_push($msg, "Entry Fee required");
            }
            
        }else{
            array_push($msg, "Entry Fee required");
        }
        
        if($req->has("commission") || $req->has("commission_ar")){
            if($req->commission != null){
                $contract["commission"] = $req->commission;
            }
            if($req->commission_ar != null){
                $contract["commission"] = $req->commission_ar;
            }
            
            if($req->commission == null && $req->commission_ar == null){
                array_push($msg, "Commission required");
            }
            
        }else{
            array_push($msg, "Commission required");
        }
        
        if($req->has("job_title") || $req->has("job_title_ar")){
            if($req->job_title != null){
                $contract["job_title"] = $req->job_title;
            }
            if($req->commission_ar != null){
                $contract["job_title"] = $req->job_title_ar;
            }
            
            if($req->job_title == null && $req->job_title_ar == null){
                array_push($msg, "Job title required");
            }
            
        }else{
            array_push($msg, "Job title required");
        }
        
        if(count($msg) > 0){
            $user_id = Auth::user()->id;
            $shop = DB::table("shops")
                    ->where("shops.user_id", $user_id)
                    ->leftJoin("uploads", "shops.logo", "=", "uploads.id")
                    ->select("shops.*", "uploads.file_name as logo_url")
                    ->first();
            $seller = DB::table("sellers")
                        ->where("user_id", $user_id)->first();
            $types_offers = DB::table("offers_types")->get();
            // return $types_offers;
            if($shop !== null && $seller !== null){
                $offers = DB::table("offers")->where("id_shop", $shop->id)->get();
                $name_shop = str_replace(" ", "-", $shop->name);
                $shop_name = $name_shop."-".$shop->id;
                return view('frontend.user.seller.offers')->with([
                    "offers" => $offers, "shop_name" => $shop_name,
                    "shop" => $shop, "seller" => $seller,
                    "types_offers" => $types_offers, "errors_contract" => $msg]);
            }
            return redirect("/dahboard");
        }
        
        // $req['msg'] = $msg;
        // return $contract;
        $contract_id = DB::table('seller_contract')->insertGetId(
                            [
                                'seller_id' => $contract["seller_id"],
                                'create_date' => $contract["date_create"],
                                'company_name' => $contract["company_name"],
                                'company_name_ar' => $contract["company_name_ar"],
                                'comm_reg_no' => $contract["comm_reg_no"],
                                'vat_no' => $contract["vat_reg"],
                                'operates_as' => $contract["opr_as"],
                                'operates_as_ar' => $contract["opr_as_ar"],
                                'contact_persons' => $contract["contact_person"],
                                'contact_person_ar' => $contract["contact_person_ar"],
                                'email_company' => $contract["email"],
                                'phone_company' => $contract["phone"],
                                'offers' => $contract["offers"],
                                'price_offer' => $contract["offer_price"],
                                'average_price_amount' => $contract["average_price"],
                                'prices_offers' => $contract["type_offer_discount"],
                                'type_offer' => $contract["types_offers"],
                                'date_start' => $contract["activation_peroid_from"],
                                'date_exp' => $contract["activation_peroid_expr"],
                                'fees' => $contract["fees"],
                                'entry_fee' => $contract["entry_fee"],
                                'currency' => $contract["currency"],
                                'commission' => $contract["commission"],
                                'job_name' => $contract["job_title"],
                            ]
                        );
        if($contract_id !== null){
            $affected = DB::table('sellers')
              ->where('id', $contract["seller_id"])
              ->update(['permission_add_offers' => 1]);
              
            
        }
        
        return redirect("/offers");
    }
    
    public function saveContract(Request $req){
        $validator = $req->validate([
            'contract_file' => 'required|mimes:pdf',
        ]);
        
        $path = $req->file('contract_file')->store('contracts');
        
         $seller = DB::table("sellers")->where("user_id" ,"=" ,Auth::user()->id)->first();
        
        if($path != null && $seller != null){
            $affected = DB::table('seller_contract')
              ->where('seller_id', $seller->id)
              ->update(['status' => 1, "file_url" => $path]);
        }

        return redirect("/offers");
    }
    
    public function init_contract($id){
        
        $contract = DB::table('seller_contract')
              ->where('id', $id)->first();
        
        $affected = DB::table('sellers')
              ->where('id', $contract->seller_id)
              ->update(['permission_add_offers' => 0]);
        if($affected){
            DB::table('seller_contract')->where('id', $id)->delete();
        }
        
        return redirect("/offers");
    }
    
    public function offers(){
        if(Auth::user()->user_type == 'seller'){
            $user_id = Auth::user()->id;
            $shop = DB::table("shops")
                    ->where("shops.user_id", $user_id)
                    ->leftJoin("uploads", "shops.logo", "=", "uploads.id")
                    ->select("shops.*", "uploads.file_name as logo_url")
                    ->first();
            $seller = DB::table("sellers")
                        ->where("user_id", $user_id)->first();
            $types_offers = DB::table("offers_types")->get();
            // return $types_offers;
            if($shop !== null && $seller !== null){
                $offers = DB::table("offers")->where("id_shop", $shop->id)->get();
                $name_shop = str_replace(" ", "-", $shop->name);
                $shop_name = $name_shop."-".$shop->id;
                $contract = null;
                if($seller->permission_add_offers == 1){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
                }
                
                if($seller->permission_add_offers == 2){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
                }
                return view('frontend.user.seller.offers')->with([
                    "offers" => $offers, "shop_name" => $shop_name,
                    "shop" => $shop, "seller" => $seller,
                    "types_offers" => $types_offers, "contract" =>  $contract
                ]);
            }
            return redirect("/dahboard");
        }
        else {
            abort(404);
        }
    }
    
    public function editOffer($id){
        $user_id = Auth::user()->id;
        $shop = DB::table("shops")->where("user_id", $user_id)->first();
        
        $seller = DB::table("sellers")
                        ->where("user_id", $user_id)->first();
        $contract = null;
        if($seller->permission_add_offers == 1){
            $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
        }
        
        if($seller->permission_add_offers == 2){
            $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
        }
        
        if($shop !== null){
            $offer = DB::table("offers")->where("id", $id)->where("id_shop", $shop->id)->first();
            $types_offers = DB::table("offers_types")->get();
           if($offer !== null){
               return view('frontend.user.seller.editOffer')->with(["offer" =>$offer, "types_offers" => $types_offers, "contract" =>  $contract]);
           }
        }
        
        return redirect("/offers");
    }
    
    public function deleteOffer($id){
        $status = DB::table("offers")->where('id', $id)->delete();
        return redirect("/offers");
    }
    
    public function addOffer(Request $req){
        $validator = $req->validate([
            'type' => 'required',
            'description' => 'required',
            'code' => 'required',
            'points' => 'required',
            'limitless' => 'required',
        ]);
        $user_id = Auth::user()->id;
        $shop = DB::table("shops")->where("user_id", $user_id)->first();
        $type = DB::table("offers_types")->where("id", $req->type)->first();
        if($shop !== null && $type != null){
            DB::table('offers')->insert([
                'type_id' => $type->id,
                'title' => $type->name,
                'description' => $req->description,
                'ilimitless_usage' => $req->limitless,
                "member_of_usage" => $req->usage,
                "id_shop" => $shop->id,
                "code" => $req->code,
                "nb_points" => $req->points
            ]);
        }
        return redirect("/offers");
    }
    
    public function updateOffer(Request $req){
        $validator = $req->validate([
            'id' => 'required',
            'type' => 'required',
            'description' => 'required',
            'code' => 'required',
            'points' => 'required',
            'limitless' => 'required',
        ]);
        $user_id = Auth::user()->id;
        $shop = DB::table("shops")->where("user_id", $user_id)->first();
        $type = DB::table("offers_types")->where("id", $req->type)->first();
        if($shop !== null && $type != null){
            DB::table('offers')
            ->where('id', $req->id)
            ->where('id_shop', $shop->id)
            ->update([
                'type_id' => $type->id,
                'title' => $type->name,
                'description' => $req->description,
                'ilimitless_usage' => $req->limitless,
                "member_of_usage" => $req->usage,
                "id_shop" => $shop->id,
                "code" => $req->code,
                "nb_points" => $req->points
            ]);
        }
        return redirect("/offers");
    }

    public function profile(Request $request)
    {
        if(Auth::user()->user_type == 'delivery_boy'){
            return view('delivery_boys.frontend.profile');
        }
        else{
            return view('frontend.user.profile');
        }
    }

    public function customer_update_profile(Request $request)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;

        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar_original = $request->photo;

        if($user->save()){
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }


    public function userProfileUpdate(Request $request)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;

        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
        }
        
        $user->avatar_original = $request->photo;

        $seller = $user->seller;

        if($seller){
            $seller->cash_on_delivery_status = $request->cash_on_delivery_status;
            $seller->bank_payment_status = $request->bank_payment_status;
            $seller->bank_name = $request->bank_name;
            $seller->bank_acc_name = $request->bank_acc_name;
            $seller->bank_acc_no = $request->bank_acc_no;
            $seller->bank_routing_no = $request->bank_routing_no;

            $seller->save();
        }

        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section(){
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section(){
        return view('frontend.partials.best_selling_section');
    }

    public function load_auction_products_section(){
        if(!addon_is_activated('auction')){
            return;
        }
        return view('auction.frontend.auction_products_section');
    }

    public function load_home_categories_section(){
        return view('frontend.partials.home_categories_section');
    }

    public function load_best_sellers_section(){
        return view('frontend.partials.best_sellers_section');
    }

    public function trackOrder(Request $request)
    {
        if($request->has('order_code')){
            $order = Order::where('code', $request->order_code)->first();
            if($order != null){
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct  = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('slug', $slug)->where('approved', 1)->first();

        if($detailedProduct != null && $detailedProduct->published){
            if($request->has('product_referral_code') && addon_is_activated('affiliate_system')) {

                $affiliate_validation_time = \App\AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);

                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }
            if($detailedProduct->digital == 1){
                return view('frontend.digital_product_details', compact('detailedProduct'));
            }
            else {
                return view('frontend.product_details', compact('detailedProduct'));
            }
        }
        abort(404);
    }

    public function shop($slug)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if($shop!=null){
            $seller = Seller::where('user_id', $shop->user_id)->first();
            if ($seller->verification_status != 0){
                return view('frontend.seller_shop', compact('shop'));
            }
            else{
                return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
            }
        }
        abort(404);
    }

    public function filter_shop($slug, $type)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if($shop!=null && $type != null){
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
//        $categories = Category::where('level', 0)->orderBy('name', 'asc')->get();
        $categories = Category::where('level', 0)->where('packages_allowed', null)->orderBy('order_level', 'desc')->get();
        return view('frontend.all_category', compact('categories'));
    }
    public function all_brands(Request $request)
    {
        $categories = Category::where('packages_allowed', null)->get();
        //$categories = Category::all();
        return view('frontend.all_brand', compact('categories'));
    }

    public function show_product_upload_form(Request $request)
    {
        if(addon_is_activated('seller_subscription')){
            if(Auth::user()->seller->remaining_uploads > 0){
                $categories = Category::where('parent_id', 0)
                    ->where('digital', 0)
                    ->where('packages_allowed', null)
                    ->with('childrenCategories')
                    ->get();
                return view('frontend.user.seller.product_upload', compact('categories'));
            }
            else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->where('packages_allowed', null)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_upload', compact('categories'));
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('packages_allowed', null)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function seller_product_list(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%'.$search.'%');
        }
        $products = $products->paginate(10);
        return view('frontend.user.seller.products', compact('products', 'search'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if(is_array($request->top_categories) && in_array($category->id, $request->top_categories)){
                $category->top = 1;
                $category->save();
            }
            else{
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if(is_array($request->top_brands) && in_array($brand->id, $request->top_brands)){
                $brand->top = 1;
                $brand->save();
            }
            else{
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;
        $tax = 0;
        $max_limit = 0;

        if($request->has('color')){
            $str = $request['color'];
        }

        if(json_decode($product->choice_options) != null){
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if($str != null){
                    $str .= '-'.str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                }
                else{
                    $str .= str_replace(' ', '', $request['attribute_id_'.$choice->attribute_id]);
                }
            }
        }

        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;

        if($product->wholesale_product){
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            if($wholesalePrice){
                $price = $wholesalePrice->price;
            }
        }

        $quantity = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if($quantity >= 1 && $product->min_qty <= $quantity){
            $in_stock = 1;
        }else{
            $in_stock = 0;
        }

        //Product Stock Visibility
        if($product->stock_visibility_state == 'text') {
            if($quantity >= 1 && $product->min_qty < $quantity){
                $quantity = translate('In Stock');
            }else{
                $quantity = translate('Out Of Stock');
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        }
        elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if($product->discount_type == 'percent'){
                $price -= ($price*$product->discount)/100;
            }
            elseif($product->discount_type == 'amount'){
                $price -= $product->discount;
            }
        }

        // taxes
        foreach ($product->taxes as $product_tax) {
            if($product_tax->tax_type == 'percent'){
                $tax += ($price * $product_tax->tax) / 100;
            }
            elseif($product_tax->tax_type == 'amount'){
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return array(
            'price' => single_price($price*$request->quantity),
            'quantity' => $quantity,
            'digital' => $product->digital,
            'variation' => $str,
            'max_limit' => $max_limit,
            'in_stock' => $in_stock
        );
    }

    public function sellerpolicy(){
        return view("frontend.policies.sellerpolicy");
    }

    public function returnpolicy(){
        return view("frontend.policies.returnpolicy");
    }

    public function supportpolicy(){
        return view("frontend.policies.supportpolicy");
    }

    public function terms(){
        return view("frontend.policies.terms");
    }

    public function privacypolicy(){
        return view("frontend.policies.privacypolicy");
    }

    public function get_pick_up_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request){
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }

    public function seller_digital_product_list(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.seller.digitalproducts.products', compact('products'));
    }
    public function show_digital_product_upload_form(Request $request)
    {
        if(addon_is_activated('seller_subscription')){
            if(Auth::user()->seller->remaining_digital_uploads > 0){
                $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
                $categories = Category::where('digital', 1)->get();
                return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
            }
            else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }

        $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
        $categories = Category::where('digital', 1)->get();
        return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
    }

    public function show_digital_product_edit_form(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $lang = $request->lang;
        $product = Product::find($id);
        return view('frontend.user.seller.digitalproducts.product_edit', compact('categories', 'product', 'lang'));
    }

    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if(isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if(isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback').'?new_email_verificiation_code='.$verification_code.'&email='.$email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = Auth::user();
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));

            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");

        } catch (\Exception $e) {
            // return $e->getMessage();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request){
        if($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');

    }

    public function reset_password_with_code(Request $request){
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if($request->password == $request->password_confirmation){
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
                {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            }
            else {
                flash("Password and confirm password didn't match")->warning();
                return redirect()->route('password.request');
            }
        }
        else {
            flash("Verification code mismatch")->error();
            return redirect()->route('password.request');
        }
    }


    public function all_flash_deals() {
        $today = strtotime(date('Y-m-d H:i:s'));

        $data['all_flash_deals'] = FlashDeal::where('status', 1)
                ->where('start_date', "<=", $today)
                ->where('end_date', ">", $today)
                ->orderBy('created_at', 'desc')
                ->get();

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    public function all_seller(Request $request) {
        $shops = Shop::whereIn('user_id', verified_sellers_id())
                ->paginate(15);

        return view('frontend.shop_listing', compact('shops'));
    }

    public function all_coupons(Request $request) {
        $coupons = Coupon::where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->paginate(15);
        return view('frontend.coupons', compact('coupons'));
    }

    public function inhouse_products(Request $request) {
        $products = filter_products(Product::where('added_by', 'admin'))->with('taxes')->paginate(12)->appends(request()->query());
        return view('frontend.inhouse_products', compact('products'));
    }
    
    public function agreement_mobile(){
        return view("frontend.policy.privacy");
    }
    
    public function agreement_mobile_ar(){
        return view("frontend.policy.privacy_ar");
    }
    
}
