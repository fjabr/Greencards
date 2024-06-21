<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\PickupPoint;
use App\Models\CustomerPackage;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Branch;
use Cookie;
use Illuminate\Support\Str;
use App\Models\AffiliateConfig;
use App\Models\Offer;
use App\Models\InvitationLink;
use App\Models\InvitedCustomersByLink;
use App\Models\Customer;
use App\Models\FlashDeal;
use App\Models\Page;
use App\Models\ProductQuery;
use Mail;
use Illuminate\Auth\Events\PasswordReset;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Utility\SmsUtility;
use Prgayman\Zatca\Facades\Zatca;
use App;

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
            return Category::where('featured', 1)->get();
        });

        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::where('published', 1)->where('todays_deal', '1'))->get();
        });

        $newest_products = Cache::remember('newest_products', 3600, function () {
            return filter_products(Product::latest())->limit(12)->get();
        });

        return view('frontend.index', compact('featured_categories', 'todays_deal_products', 'newest_products'));
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    public function invitationByLinkRegistration(Request $request, $id)
    {
        $invitationLinkId = decrypt($id);
        $showHeaderFooter = false;
        $invitationLink = InvitationLink::where('id',$invitationLinkId)->where("deleted",0)->first();
        if(empty($invitationLink)){
            return view('frontend.failed_user_registration_invitation_link');

        }
        if(count($invitationLink->invitationLinks) >= $invitationLink->nb_members){
            return view('frontend.failed_user_registration_invitation_link');
        }
        return view('frontend.user_registration_invitation_link', compact("invitationLink","id",'showHeaderFooter'));

    }

    public function invitationLinkRegistration(Request $request, $id)
    {


        DB::beginTransaction();
        try{

            $invitationLinkId = decrypt($id);
            $invitationLink = InvitationLink::where('id',$invitationLinkId)->where("deleted",0)->first();

            if(empty($invitationLink)){
                return view('frontend.failed_user_registration_invitation_link');
            }
            if(count($invitationLink->invitationLinks) >= $invitationLink->nb_members){
                return view('frontend.failed_user_registration_invitation_link');
            }
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'password' => 'required|string',
                'mobile' => 'required|string',
                'country_code' => 'required|string',
                'password_confirmation' => 'required|string',
            ]);


            if ($request->password !== $request->password_confirmation) {
                flash("Password and confirm password didn't match")->warning();
                return redirect()->back();
            }

            if (!$request->filled('email') && !$request->filled('mobile')) {
                throw new Exception(translate('Email or mobile are required'), 99);
            }

            if ($request->filled('mobile') && !$request->filled('country_code')) {
                throw new Exception(translate('Country code is required'), 99);
            }

            if ($request->filled('email')) {
                $userAlreadyExisted = User::where("email", $request->input("email"))->first();
                if (!empty($userAlreadyExisted)) {
                    throw new Exception(translate("Email already existed in database"), 99);
                }
            }
            if ($request->filled('mobile')) {
                $phone = $request->input("mobile");
                $country_code = $request->input("country_code");
                $phone = str_replace("+{$country_code}", "", $phone);

                if (preg_match('/^\d{9}$/', $phone)) {
                    $formatted_phone = "+{$country_code}{$phone}";
                } else {
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
                $userAlreadyExisted = User::orWhere("phone", "like", '%' . $formatted_phone. '%')->first();
                if (!empty($userAlreadyExisted)) {
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
            }
            //$password = Str::random(8);
            //$password = "123456";
            $password = $request->password;
            $user = new User;
            $package = $invitationLink->package;
            $user->name = $request->input("first_name")." ".$request->input("last_name");
            if ($request->filled("email") ) {
                $user->email = $request->input("email");
            }

            if ($request->filled("mobile") ) {
                $user->phone = $formatted_phone;
            }

            $user->password = \bcrypt($password);
            $user->start_sub_date = Carbon::now();
            $user->duration = $package->duration;
            $user->email_verified_at = Carbon::now();
            $user->end_sub_date = Carbon::now()->addDays($package->duration);
            $user->nb_members = $package->nb_members-1;
            $user->customer_package_id = $invitationLink->package_id;
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->registration_source = "invitation_link";
            $customer->save();
            $invitedCustomerByLink = new InvitedCustomersByLink();
            $invitedCustomerByLink->user_id = $user->id;
            $invitedCustomerByLink->invitation_link_id = $invitationLink->id;
            $invitedCustomerByLink->save();

             $data = [
                    "email" => $user->email ?? $user->phone,
                    "password"=> $password,
                    "package"=> $package,
                    "start_sub_date"=> $user->start_sub_date,
                    "end_sub_date"=> $user->end_sub_date,
                ];

            if($request->filled('email')){
                sendEmail($data);
            }

            sendEmailToAdmin($data);

            if($request->filled("mobile")){
                if (!SmsUtility::sale_new_sub($user, $password)) {
                    flash(translate('Problem while sending SMS notification'))->error();
                } else {
                    flash(translate('SMS notification has been sent succesfully to customer'))->success();
                }
            }

            DB::commit();
            flash(translate('Customer has been created successfully by this invitation link'))->success();
            return redirect()->route('user.links.register.success');
        }catch(Exception $ex){
            if($ex->getCode() == 99){
                flash($ex->getMessage())->error();

            }else{
                flash(translate('Customer was not created, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return back();
        }
    }

    public function invitationLinkRegistrationSuccess(Request $request)
    {
        return view('frontend.success_user_registration_invitation_link');

    }



    public function sendGiftRegistration(Request $request)
    {


        DB::beginTransaction();
        try{

            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'mobile' => 'required|string',
                'country_code' => 'required|string',
            ]);


            if (!$request->filled('email') && !$request->filled('mobile')) {
                throw new Exception(translate('Email or mobile are required'), 99);
            }

            if ($request->filled('mobile') && !$request->filled('country_code')) {
                throw new Exception(translate('Country code is required'), 99);
            }

            if ($request->filled('email')) {
                $userAlreadyExisted = User::where("email", $request->input("email"))->first();
                if (!empty($userAlreadyExisted)) {
                    throw new Exception(translate("Email already existed in database"), 99);
                }
            }
            if ($request->filled('mobile')) {
                $phone = $request->input("mobile");
                $country_code = $request->input("country_code");
                $phone = str_replace("+{$country_code}", "", $phone);

                if (preg_match('/^\d{9}$/', $phone)) {
                    $formatted_phone = "+{$country_code}{$phone}";
                } else {
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
                $userAlreadyExisted = User::orWhere("phone", "like", '%' . $formatted_phone. '%')->first();
                if (!empty($userAlreadyExisted)) {
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
            }
            //$password = Str::random(8);
            $password = "123456";
            $user = new User;
            $package = CustomerPackage::where("id", 6)->first();
            $user->name = $request->input("first_name")." ".$request->input("last_name");
            if ($request->filled("email") ) {
                $user->email = $request->input("email");
            }

            if ($request->filled("mobile") ) {
                $user->phone = $formatted_phone;
            }

            $user->password = \bcrypt($password);
            $user->start_sub_date = Carbon::now();
            $user->duration = $package->duration;
            $user->email_verified_at = Carbon::now();
            $user->end_sub_date = Carbon::now()->addDays($package->duration);
            $user->nb_members = $package->nb_members-1;
            $user->customer_package_id = $package->id;
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->registration_source = "invitation_link";
            $customer->save();

             $data = [
                    "email" => $user->email ?? $user->phone,
                    "password"=> $password,
                    "package"=> $package,
                    "start_sub_date"=> $user->start_sub_date,
                    "end_sub_date"=> $user->end_sub_date,
                ];

            if($request->filled('email')){
                sendEmail($data);
            }

            sendEmailToAdmin($data);

            if($request->filled("mobile")){
                if (!SmsUtility::sale_new_sub($user, $password)) {
                    flash(translate('Problem while sending SMS notification'))->error();
                } else {
                    flash(translate('SMS notification has been sent succesfully to customer'))->success();
                }
            }

            DB::commit();
            flash(translate('Customer has been created successfully by this invitation link'))->success();
            return redirect()->route('user.links.register.success');
        }catch(Exception $ex){
            if($ex->getCode() == 99){
                flash($ex->getMessage())->error();

            }else{
                flash(translate('Customer was not created, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return back();
        }
    }

    public function sendGiftForm(Request $request, $id)
    {
        $user_id = decrypt($id);
        $user = User::where("id", $user_id)->first();

        if(empty($user)){
            return view('frontend.failed_user_registration_invitation_link');
        }

        $showHeaderFooter = false;
        $package = CustomerPackage::where("id", 6)->first();

        auth()->login($user, true);
        return view('frontend.send_gift_form', compact('showHeaderFooter', 'package'));

    }
    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
            try {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {
            }
        }
        return view('frontend.user_registration');
    }

    public function sendGiftSuccess(Request $request)
    {
        return view('frontend.success_user_registration_invitation_link');

    }
    public function cart_login(Request $request)
    {
        $user = null;
        if ($request->get('phone') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('phone', "+{$request['country_code']}{$request['phone']}")->first();
        } elseif ($request->get('email') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->first();
        }

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        } else {
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
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.dashboard');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.dashboard');
        } else {
            abort(404);
        }
    }

    public function contract(Request $req){



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
                $shop_name = urlencode($shop_name);
                return view('seller.offers.offers')->with([
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
                $shop_name = urlencode($shop_name);
                $contract = null;
                if($seller->permission_add_offers == 1){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
                }

                if($seller->permission_add_offers == 2){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
                }
                return view('seller.offers.offers')->with([
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
               return view('seller.offers.editOffer')->with(["offer" =>$offer, "types_offers" => $types_offers, "contract" =>  $contract]);
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
            'description_ar' => 'required',
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
                'title_ar' => $type->name_ar,
                'description' => $req->description,
                'description_ar' => $req->description_ar,
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
            'description_ar' => 'required',
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
                'title_ar' => $type->name_ar,
                'description' => $req->description,
                'description_ar' => $req->description_ar,
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
        if (Auth::user()->user_type == 'seller') {
            return redirect()->route('seller.profile.index');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.profile');
        } else {
            return view('frontend.user.profile');
        }
    }

    public function userProfileUpdate(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
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

        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }

        $user->avatar_original = $request->photo;
        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();
        return back();
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section()
    {
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.partials.best_selling_section');
    }

    public function load_auction_products_section()
    {
        if (!addon_is_activated('auction')) {
            return;
        }
        return view('auction.frontend.auction_products_section');
    }

    public function load_home_categories_section()
    {
        return view('frontend.partials.home_categories_section');
    }

    public function load_best_sellers_section()
    {
        return view('frontend.partials.best_sellers_section');
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct  = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('slug', $slug)->where('approved', 1)->first();

        $product_queries = ProductQuery::where('product_id', $detailedProduct->id)->where('customer_id', '!=', Auth::id())->latest('id')->paginate(10);
        $total_query = ProductQuery::where('product_id', $detailedProduct->id)->count();
        // Pagination using Ajax
        if (request()->ajax()) {
            return Response::json(View::make('frontend.partials.product_query_pagination', array('product_queries' => $product_queries))->render());
        }
        // End of Pagination
        if ($detailedProduct != null && $detailedProduct->published) {
            if ($request->has('product_referral_code') && addon_is_activated('affiliate_system')) {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);

                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }
            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details', compact('detailedProduct', 'product_queries', 'total_query'));
            } else {
                return view('frontend.product_details', compact('detailedProduct', 'product_queries', 'total_query'));
            }
        }
        abort(404);
    }

    public function shop($slug)
    {

        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null) {
            if ($shop->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop'));
            }
        }
        abort(404);
    }

    public function filter_shop(Request $request, $slug, $type)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {

            if ($type == 'all-products') {
                $sort_by = $request->sort_by;
                $min_price = $request->min_price;
                $max_price = $request->max_price;
                $selected_categories = array();
                $brand_id = null;
                $rating = null;

                $conditions = ['user_id' => $shop->user->id, 'published' => 1, 'approved' => 1];

                if ($request->brand != null) {
                    $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
                    $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
                }

                $products = Product::where($conditions);

                if ($request->has('selected_categories')) {
                    $selected_categories = $request->selected_categories;
                    $products->whereIn('category_id', $selected_categories);
                }

                if ($min_price != null && $max_price != null) {
                    $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
                }

                if ($request->has('rating')) {
                    $rating = $request->rating;
                    $products->where('rating', '>=', $rating);
                }

                switch ($sort_by) {
                    case 'newest':
                        $products->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $products->orderBy('created_at', 'asc');
                        break;
                    case 'price-asc':
                        $products->orderBy('unit_price', 'asc');
                        break;
                    case 'price-desc':
                        $products->orderBy('unit_price', 'desc');
                        break;
                    default:
                        $products->orderBy('id', 'desc');
                        break;
                }

                $products = $products->paginate(24)->appends(request()->query());

                return view('frontend.seller_shop', compact('shop', 'type', 'products', 'selected_categories', 'min_price', 'max_price', 'brand_id', 'sort_by', 'rating'));
            }

            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
        $categories = Category::where('level', 0)->orderBy('order_level', 'desc')->get();
        return view('frontend.all_category', compact('categories'));
    }

    public function all_brands(Request $request)
    {
        $brands = Brand::all();
        return view('frontend.all_brand', compact('brands'));
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
                return view('seller.money_withdraw_requests', compact('categories'));
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
        return view('seller.product.product_bulk_upload', compact('categories'));
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
        return view('seller.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
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
        return view('seller.product.products.index', compact('products', 'search'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
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

        if ($request->has('color')) {
            $str = $request['color'];
        }

        if (json_decode($product->choice_options) != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $product_stock = $product->stocks->where('variant', $str)->first();

        $price = $product_stock->price;


        if ($product->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        $quantity = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if ($quantity >= 1 && $product->min_qty <= $quantity) {
            $in_stock = 1;
        } else {
            $in_stock = 0;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            if ($quantity >= 1 && $product->min_qty < $quantity) {
                $quantity = translate('In Stock');
            } else {
                $quantity = translate('Out Of Stock');
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        // taxes
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return array(
            'price' => single_price($price * $request->quantity),
            'quantity' => $quantity,
            'digital' => $product->digital,
            'variation' => $str,
            'max_limit' => $max_limit,
            'in_stock' => $in_stock
        );
    }

    public function sellerpolicy()
    {
        $page =  Page::where('type', 'seller_policy_page')->first();
        return view("frontend.policies.sellerpolicy", compact('page'));
    }

    public function returnpolicy()
    {
        $page =  Page::where('type', 'return_policy_page')->first();
        return view("frontend.policies.returnpolicy", compact('page'));
    }

    public function supportpolicy()
    {
        $page =  Page::where('type', 'support_policy_page')->first();
        return view("frontend.policies.supportpolicy", compact('page'));
    }

    public function terms()
    {
        $page =  Page::where('type', 'terms_conditions_page')->first();
        return view("frontend.policies.terms", compact('page'));
    }

    public function privacypolicy()
    {
        $page =  Page::where('type', 'privacy_policy_page')->first();
        return view("frontend.policies.privacypolicy", compact('page'));
    }

    public function get_pick_up_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index($package="")
    {
        $customer_packages = CustomerPackage::where("hidden",0)->get();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }

    public function subscription_package()
    {
        return redirect()->route("home");
        $customer_packages = CustomerPackage::where('customer_packages.hidden',0)->get();
//        dd($customer_packages);
        return view('frontend.user.subscription_package.subscripe_package', compact('customer_packages'));
    }

    // public function new_page()
    // {
    //     $user = User::where('user_type', 'admin')->first();
    //     auth()->login($user);
    //     return redirect()->route('admin.dashboard');

    // }


    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
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
        if (isUnique($email)) {
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
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
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

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                if ($user->user_type == 'seller') {
                    return redirect()->route('seller.dashboard');
                }
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }

    public function reset_password_with_code(Request $request)
    {

        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return view('auth.passwords.reset');
            }
        } else {
            flash("Verification code mismatch")->error();
            return view('auth.passwords.reset');
        }
    }


    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d H:i:s'));

        $data['all_flash_deals'] = FlashDeal::where('status', 1)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">", $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    public function todays_deal()
    {
        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::where('published', 1)->where('todays_deal', '1'))->get();
        });

        return view("frontend.todays_deal", compact('todays_deal_products'));
    }

    public function all_seller(Request $request)
    {
        $shops = Shop::whereIn('user_id', verified_sellers_id())
            ->paginate(15);

        return view('frontend.shop_listing', compact('shops'));
    }

    public function all_coupons(Request $request)
    {
        $coupons = Coupon::where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->paginate(15);
        return view('frontend.coupons', compact('coupons'));
    }

    public function inhouse_products(Request $request)
    {
        $products = filter_products(Product::where('added_by', 'admin'))->with('taxes')->paginate(12)->appends(request()->query());
        return view('frontend.inhouse_products', compact('products'));
    }

    public function agreement_mobile(){
        return view("frontend.policy.privacy");
    }

    public function agreement_mobile_ar(){
        return view("frontend.policy.privacy_ar");
    }


    public function offerStatistics(Request $request, Offer $offer)
    {
        //PAGINATION
        //return $request;
        $offers_scanned_query = DB::table("offers_scanned")
            ->join("users","users.id","offers_scanned.user_id")
            ->join("customer_packages","users.customer_package_id","customer_packages.id")
            ->where("offer_id",$offer->id)
            ->where("user_type","customer")
            ->select("users.name","offers_scanned.code","offers_scanned.id as id","offers_scanned.created_at as date","customer_packages.name as subscription")
            ->orderBy("offers_scanned.created_at",'DESC');

        if($request->has("start_from") && $request->has("ends_at")){
            $offers_scanned_query = $offers_scanned_query->where("offers_scanned.created_at",">=",new Carbon($request->input("start_from")))->where("offers_scanned.created_at","<=",new Carbon($request->input("ends_at")));
        }
        $start_at = $request->input("start_from");
        $ends_at = $request->input("ends_at");
        $offers_scanned = $offers_scanned_query->get();

        $shop = Shop::where("id",$offer->id_shop)->first();
        $name_shop = str_replace(" ", "-", $shop->name);

        $shop_name = $name_shop."-".$shop->id;
        $shop_name = urlencode($shop_name);
        $key = 0;
        return view('seller.offers.offer_scanned', compact('offer','offers_scanned','shop_name','ends_at','start_at','key'));



    }

    function validateAndExtractID($nameID)
    {
        // Find the last occurrence of '-' in the string
        $lastDashIndex = strrpos($nameID, '-');

        if ($lastDashIndex !== false) {

            // Return the ID
            $id = substr($nameID, $lastDashIndex + 1);
            return $id ;
        }

        // Return null if no ID is found
        return null;
    }

    function seller_subscribtion_gateway(Request $request, $shopSlug) {
        $data = [];
        $showHeaderFooter = false;
        try{
        //    if($this->validateAndExtractID($shopSlug)){
                $branchID = $this->extractBranchId($shopSlug);
                $package = CustomerPackage::where("id",get_setting("seller_custumer_subscription"))->first();
                if(empty($package)) throw new Exception("subscription package not found",404);


                if($branchID === null){
                    $shopId = $this->validateAndExtractID($shopSlug);
                    if($shopId === null){
                        throw new Exception("invalid QR code");
                    }
                    $shop = Shop::where("id",$shopId)->first();
                    if(empty($shop)){
                        return new Exception("invalid QR code");
                    }

                    $data["city"] = $shop->city->getTranslation("name");
                    $data["address"] =App::getLocale() === 'ar' ? $shop->address_ar : $shop->address;
                    $data["source"] = "seller";
                    $data["id"] = $shopId;
                    $data["logo"] =$shop->logo;
                    $data["name"] =App::getLocale() === 'ar' ? $shop->name_ar : $shop->name;

                return view('frontend.sellers.user_registration_seller_subscription', compact("data","package",'showHeaderFooter'));

                }

                $branch = Branch::where("id",$branchID)->first();
                if(empty($branch)) throw new Exception("branch not found",404);

                $data["city"] = $branch->branchCity->getTranslation("name");
                $data["address"] =App::getLocale() === 'ar' ? $branch->address_ar : $branch->address;
                $data["id"] = $branchID;
                $data["source"] = "branch";
                $data["logo"] =$branch->shop->logo;
                $data["name"] =App::getLocale() === 'ar' ? $branch->name_ar : $branch->name;


                return view('frontend.sellers.user_registration_seller_subscription', compact("data","branch","package",'showHeaderFooter'));

      //      }



        }catch (\Exception $e) {
            Log::error($e);
            return $e;
            return redirect("/downloadapps");
        }
    }

    function extractBranchId($encoded_shop_name) {
        $decoded_shop_name = urldecode($encoded_shop_name);
        $parts = explode("-", $decoded_shop_name);
        Log::info($parts);
        if(count($parts)> 2 && $parts[0] == "branch"){
            $branch_id = $parts[1];
            return $branch_id;
        }else {
            return null;
        }
    }

    function customerSubscribeFromSellerQR(Request $request) {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string',
            'payment_option' => 'required|string',
            'password_confirmation' => 'required|string',
            'shop_id' => 'required|string',
        ]);

        $shop_id = $request->shop_id;
        DB::beginTransaction();

        try{

            $package = CustomerPackage::where("id",get_setting("seller_custumer_subscription"))->first();

            if ($request->password !== $request->password_confirmation) {
                flash("Password and confirm password didn't match")->warning();
                return redirect()->back();
            }

            if (!$request->filled('email') && !$request->filled('mobile')) {
                throw new Exception(translate('Email or mobile are required'), 99);
            }

            if ($request->filled('mobile') && !$request->filled('country_code')) {
                throw new Exception(translate('Country code is required'), 99);
            }

            if ($request->filled('email')) {
                $userAlreadyExisted = User::where("email", $request->input("email"))->first();

                if (!empty($userAlreadyExisted)) {

                    if(isSubscribedUser($userAlreadyExisted)){
                        throw new Exception(translate("Your have already subscribed"), 99);
                    }
                    if(!empty($userAlreadyExisted->customer) && $userAlreadyExisted->customer->registration_source = 'seller_subscription'){
                        $total_amount = $package->amount;

                        $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
                        if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                            flash(translate("Coupon not applied, maybe try again with a valid one"));
                        } else {
                            $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
                        }
                        $vat = floatval($total_amount) * 0.15;

                        $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
                                ->vatRegistrationNumber(get_setting("owner_vat_no"))
                                ->timestamp(Carbon::now())
                                ->totalWithVat($total_amount+$vat)
                                ->vatTotal($vat)
                                ->toBase64();

                        return redirect()->route("subscription-payment",[
                            "amount" => $total_amount+$vat,
                            "qr_code" => $qrCode, //to generate qr with zakatka
                            "vat_total" => floatval($vat) ,
                            "user_id" => $userAlreadyExisted->id,
                            "package_id" => $package->id,
                            "card_type" => $request->payment_option,
                            "date_qr" => getCurrentDateInUTCFormat(),
                            "coupon" => $request->coupon,
                            "registration_source" => 'seller_subscription',
                            "seller_id" => $shop_id,
                            "source" => $request->source,
                        ]);
                    }

                    throw new Exception(translate("Email already existed in database"), 99);
                }
            }
            if ($request->filled('mobile')) {
                $phone = $request->input("mobile");
                $country_code = $request->input("country_code");
                $phone = str_replace("+{$country_code}", "", $phone);

                if (preg_match('/^\d{9}$/', $phone)) {
                    $formatted_phone = "+{$country_code}{$phone}";
                } else {
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
                $userAlreadyExisted = User::orWhere("phone", "like", '%' . $formatted_phone. '%')->first();
                if (!empty($userAlreadyExisted)) {
                    if(isSubscribedUser($userAlreadyExisted)){
                        throw new Exception(translate("Your have already subscribed"), 99);
                    }

                    if(!empty($userAlreadyExisted->customer) && $userAlreadyExisted->customer->registration_source = 'seller_subscription'){
                        $total_amount = $package->amount;

                        $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
                        if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                            flash(translate("Coupon not applied, maybe try again with a valid one"));
                        } else {
                            $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
                        }
                        $vat = floatval($total_amount) * 0.15;

                        $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
                                ->vatRegistrationNumber(get_setting("owner_vat_no"))
                                ->timestamp(Carbon::now())
                                ->totalWithVat($total_amount+$vat)
                                ->vatTotal($vat)
                                ->toBase64();

                        return redirect()->route("subscription-payment",[
                            "amount" => $total_amount+$vat,
                            "qr_code" => $qrCode, //to generate qr with zakatka
                            "vat_total" => floatval($vat) ,
                            "user_id" => $userAlreadyExisted->id,
                            "package_id" => $package->id,
                            "card_type" => $request->payment_option,
                            "date_qr" => getCurrentDateInUTCFormat(),
                            "coupon" => $request->coupon,
                            "registration_source" => 'seller_subscription',
                            "seller_id" => $shop_id,
                            "source" => $request->source,
                        ]);
                    }
                    throw new Exception(translate("Mobile already existed in database"), 99);
                }
            }


            $password = $request->password;
            $user = new User;
            if(empty($package)) throw new Exception("subscription package not found",99);


            $user->name = $request->input("first_name")." ".$request->input("last_name");
            if ($request->filled("email") ) {
                $user->email = $request->input("email");
            }

            if ($request->filled("mobile") ) {
                $user->phone = $formatted_phone;
            }

            $user->password = \bcrypt($password);

            $user->duration = $package->duration;
            $user->email_verified_at = Carbon::now();
            $user->nb_members = $package->nb_members-1;
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->registration_source = "seller_subscription";
            $customer->save();

            // seller subscription report

            $total_amount = $package->amount;

            $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
            if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                flash(translate("Coupon not applied, maybe try again with a valid one"));
            } else {
                $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
            }
            $vat = floatval($total_amount) * 0.15;

            $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
                    ->vatRegistrationNumber(get_setting("owner_vat_no"))
                    ->timestamp(Carbon::now())
                    ->totalWithVat($total_amount+$vat)
                    ->vatTotal($vat)
                    ->toBase64();

            DB::commit();

            return redirect()->route("subscription-payment",[
                "amount" => $total_amount+$vat,
                "qr_code" => $qrCode, //to generate qr with zakatka
                "vat_total" => floatval($vat) ,
                "user_id" => $user->id,
                "package_id" => $package->id,
                "card_type" => $request->payment_option,
                "date_qr" => getCurrentDateInUTCFormat(),
                "coupon" => $request->coupon,
                "registration_source" => 'seller_subscription',
                "seller_id" => $shop_id,
                "source" => $request->source,
            ]);


        }catch(Exception $ex){
            DB::rollBack();
            if($ex->getCode() == 99){
                flash(translate($ex->getMessage()))->error();
            }else{
                flash(translate("An Error has accured, please try again"))->error();
            }

            Log::error($ex->getMessage());

            return redirect()->back();

        }
    }

    //

}
