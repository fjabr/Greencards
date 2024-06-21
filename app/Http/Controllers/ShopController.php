<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Country;
use App\Models\ShopCountry;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Seller;
use Auth;
use Hash;
use App\Notifications\EmailVerificationNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Log;

class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware('user', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $shop = Shop::with('city')->where('user_id', $user->id)->first();
        $categories = DB::table('categories')
            ->where('categories.packages_allowed', "!=", null)
            ->select('categories.id', 'categories.name as enName')
            ->get();
        $branches = DB::table("branches")->where("shop_id", $shop->id)->get();
        $countries = Country::where('status',1)->get();
        $cities=City::where('status',1)->get();
        return view('seller.shop', compact('shop', 'categories', 'branches','cities','countries'));
    }

    public function update(Request $request, $id)
    {

        $shop = Shop::find($id);

        if ($request->has('name') && $request->has('address')) {
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }

            $shop->name             = $request->name;
            $shop->address          = $request->address;
            $shop->phone            = $request->phone;
            $shop->slug             = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
            $shop->meta_title       = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            $shop->logo             = $request->logo;
        }

        if (
            $request->has('delivery_pickup_longitude') &&
            $request->has('delivery_pickup_latitude')
        ) {

            $shop->delivery_pickup_longitude    = $request->delivery_pickup_longitude;
            $shop->delivery_pickup_latitude     = $request->delivery_pickup_latitude;
        } elseif ($request->has('facebook') || $request->has('google') || $request->has('twitter') || $request->has('youtube') || $request->has('instagram')) {
            $shop->facebook = $request->facebook;
            $shop->instagram = $request->instagram;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } elseif ($request->has('latitude') && $request->has('longitude')) {
            $shop->latitude = $request->latitude;
            $shop->longitude = $request->longitude;
        } elseif ($request->has('wifi') || $request->has('parck') || $request->has('server') || $request->has('kids') || $request->has('bag')) {

            $shop->wifi = $request->wifi;
            $shop->parck = $request->parck;
            $shop->server = $request->serverman;
            $shop->kids = $request->kids;
            $shop->bag = $request->bag;
        } elseif (
            $request->has('mo_from') || $request->has('mo_to') || $request->has('tu_from') || $request->has('tu_to') ||
            $request->has('we_from') || $request->has('we_to') || $request->has('th_from') || $request->has('th_to') ||
            $request->has('fr_from') || $request->has('fr_to') || $request->has('sa_from') || $request->has('sa_to') ||
            $request->has('su_from') || $request->has('su_to')
        ) {


            $shop->monday_from = $request->mo_from;
            $shop->monday_to = $request->mo_to;

            $shop->tuesday_from = $request->tu_from;
            $shop->tuesday_to = $request->tu_to;

            $shop->wednesday_from = $request->we_from;
            $shop->wednesday_to = $request->we_to;

            $shop->thursday_from = $request->th_from;
            $shop->thursday_to = $request->th_to;

            $shop->friday_from = $request->fr_from;
            $shop->friday_to = $request->fr_to;

            $shop->saturday_from = $request->sa_from;
            $shop->saturday_to = $request->sa_to;

            $shop->sunday_from = $request->su_from;
            $shop->sunday_to = $request->su_to;


        } elseif ($request->has('menu')) {
            $images = array();
            if ($files = $request->file('menu')) {
                foreach ($files as $file) {
                    $name = $file->getClientOriginalName();
                    //$file->move('menus',$name);
                    $path = $file->store('menus');
                    $images[] = $path;
                }
            }

            $shop->menu = implode("|", $images);
        } elseif ($request->hasFile('default_image')) {
            $defaultImage = $request->file('default_image');
            $imageName = date('YmdHi') . "." . $defaultImage->getClientOriginalExtension();
            move_uploaded_file($defaultImage, public_path() . '/default_images/' . $imageName);
            $shop->default_image = "/default_images/" . $imageName;
        } elseif ($request->has('categories')) {
            $shop->categories = json_encode($request->input('categories'));
        } elseif ($request->has('terms_en')) {
            $shop->terms_en = $request->terms_en;
        } elseif ($request->has('terms_ar')) {
            $shop->terms_ar = $request->terms_ar;
        } else {
            $shop->sliders = $request->sliders;
        }



        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }


    public function addBranch(Request $req)
    {
        try {
            $city = City::where("id", $req->input("city_id"))->first();
            $shop = Shop::where("id", $req->input("shop_id"))->first();
            $branch = new Branch;

            $branch->shop_id = $req->shop_id;
            $branch->name = $req->name;
            $branch->name_ar = $req->name_ar;
            $branch->address = $req->address;
            $branch->address_ar = $req->address_ar;
            $branch->latitude = $req->latitude;
            $branch->longitude = $req->longitude;
            $branch->website = $req->website;
            if(!empty($city)){
                $branch->city_id = $req->city_id;
                $branch->city_name = $city->name;
                $branch->city_name_ar = $city->getTranslation("name", "ar");
            }
            $branch->save();
            $name_shop = str_replace(" ", "-", $shop->name);
            $shop_name = "branch-" . $branch->id . "-shop-" . $name_shop . "-" . $shop->id;
            $shop_name = urlencode($shop_name);

            $branch->slog = $shop_name;
            $branch->save();
            flash(translate("your branch has been added successfully!"))->success();
            return back();
        } catch (Exception $ex) {
            Log::error("branch creation error");
            Log::error($ex);
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }
    }

    public function editBranch(Request $req, $id)
    {
        try {

            $branch = Branch::where('id',$id)->first();
            if(empty($branch)){
                throw new Exception("branch not found");
            }
            $city = City::where("id", $req->input("city_id"))->first();
            $shop = Shop::where("id", $branch->shop_id)->first();
            $branch->name = $req->name;
            $branch->name_ar = $req->name_ar;
            $branch->address = $req->address;
            $branch->address_ar = $req->address_ar;
            $branch->latitude = $req->latitude;
            $branch->longitude = $req->longitude;
            $branch->website = $req->website;
            if(!empty($city)){
                $branch->city_id = $req->city_id;
                $branch->city_name = $city->name;
                $branch->city_name_ar = $city->getTranslation("name", "ar");
            }
            $branch->save();
            $name_shop = str_replace(" ", "-", $shop->name);
            $shop_name = "branch-" . $branch->id . "-shop-" . $name_shop . "-" . $shop->id;
            $shop_name = urlencode($shop_name);

            $branch->slog = $shop_name;
            $branch->save();
            flash(translate("your branch has been edited successfully!"))->success();
            return back();
        } catch (Exception $ex) {
            Log::error("branch edit error");
            Log::error($ex);
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }
    }

    //delete branch
    public function deleteBranch($id)
    {
        $delete = DB::table('branches')->where('id', $id)->delete();

        if ($delete) {
            flash(translate('Your branch has been deleted successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check()) {
			if((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'customer')) {
				flash(translate('Admin or Customer can not be a seller'))->error();
				return back();
			} if(Auth::user()->user_type == 'seller'){
				flash(translate('This user already a seller'))->error();
				return back();
			}

        } else {
            return view('frontend.seller_form');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'email.required' => translate('Email is required'),
            'email.email' => translate('Email must be a valid email address'),
            'email.unique' => translate('The email has already been taken'),
            'address.required' => translate('Address is required'),
            'shop_name.required' => translate('Shop Name is required'),
            'countries.required' => translate('Countries is required'),
            'password.required' => translate('Password is required'),
            'password.confirmed' => translate('Password confirmation does not match'),
            'password.min' => translate('Minimum 6 digits required for password')
        );
        $validator = Validator::make($request->all(), [

            'email'     => 'required|email|unique:users,email',
            'name'      => 'required',
            'address'   => 'required',
            'shop_name' => 'required',
            'countries' => 'required',
            'password' => 'required|min:6|confirmed',
        ],$messages );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // send back all errors to the login form
                ->withInput();
        }
        $user = null;
        if ( ! Auth::check() )
        {
            if ( User::where( 'email', $request->email )->first() != null )
            {
                flash( translate( 'Email already exists!' ) )->error();

                return back();
            }
            if ( $request->password == $request->password_confirmation )
            {
                $user            = new User;
                $user->name      = $request->name;
                $user->email     = $request->email;
                $user->user_type = "seller";
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                flash(translate('Sorry! Password did not match.'))->error();
                return back();
            }
        } else {
            $user = Auth::user();
            if ($user->customer != null) {
                $user->customer->delete();
            }
            $user->user_type = "seller";
            $user->save();
        }

        if (Shop::where('user_id', $user->id)->first() == null) {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $request->shop_name);

            if ($shop->save()) {

                $seller = new Seller;
                $seller->user_id = $user->id;
                $seller->save();

                auth()->login($user, false);
                if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
                    $user->notify(new EmailVerificationNotification());
                }

                flash(translate('Your Shop has been created successfully!'))->success();
                return redirect()->route('shops.index');
            } else {
                $user->user_type == 'customer';
                $user->save();
            }
        }

        foreach ($request->countries as $country) {
            ShopCountry::query()->create([
                'shop_id'=>$shop->id,
                'country_id'=>$country
                                         ]);
        }
        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

}
