<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\CustomerPackageTranslation;
use App\Models\CustomerPackagePayment;
use Auth;
use Session;
use App\Models\User;
use App\Models\Customer;
use App\Utility\SmsUtility;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Mail;
use Str;
use Prgayman\Zatca\Facades\Zatca;

class CustomerPackageController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_classified_packages'])->only('index');
        $this->middleware(['permission:edit_classified_package'])->only('edit');
        $this->middleware(['permission:delete_classified_package'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_packages = CustomerPackage::all();
        return view('backend.customer.customer_packages.index', compact('customer_packages'));
    }
    /**
     * customer with free sub page.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_with_free_sub()
    {
        $packages = CustomerPackage::all();
       // return $packages;
        return view('backend.customer.customer_packages.add_customer_with_free_sub',[
            "packages"=>$packages
        ]);
    }

    /**
     * customer with free sub page.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_customer_with_free_sub(Request $request)
    {

        DB::beginTransaction();
        try{

            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'package' => 'required|string',
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
            $userAlreadyExisted = User::where("email",$request->input("email"))->orWhere("phone",$request->input("mobile"))->first();

            if(!empty($userAlreadyExisted)){
                throw new Exception(translate("Email already existed in database"),99);
                }
            $userAlreadyExisted = User::where("email",$request->input("email"))->orWhere("phone",$request->input("mobile"))->first();

            if(!empty($userAlreadyExisted)){
                throw new Exception(translate("Email already existed in database"),99);
            }

            //$password = Str::random(8);
            $password = "123456";
            $user = new User;
            $package = CustomerPackage::where("id",$request->input("package"))->first();
            $user->name = $request->input("first_name")." ".$request->input("last_name");
            if ($request->filled('email') ) {
                $user->email = $request->input("email");
            }

            if ($request->filled('mobile') ) {
                $user->phone = $formatted_phone;
            }

            $user->password = \bcrypt($password);
            $user->start_sub_date = Carbon::now();
            $user->duration = $package->duration;
            $user->end_sub_date = Carbon::now()->addDays($package->duration);
            $user->nb_members = $package->nb_members-1;
            $user->customer_package_id = $request->input("package");
            $user->email_verified_at = Carbon::now();
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->created_by = Auth::user()->id;
            $customer->save();

            if($request->has('email')){
                $data = [
                    "email" => $user->email,
                    "password"=> $password,
                    "package"=> $package,
                    "start_sub_date"=> $user->start_sub_date,
                    "end_sub_date"=> $user->end_sub_date,
                ];
                $this->sendEmail($data);
                $this->sendEmailToAdmin($data);
            }
            if($request->has("mobile")){
                if (!SmsUtility::sale_new_sub($user, $password)) {
                    flash(translate('Problem while sending SMS notification'))->error();
                } else {
                    flash(translate('SMS notification has been sent succesfully to customer'))->success();
                }
            }

            DB::commit();
            flash(translate('Customer has been created successfully'))->success();
            return redirect()->route('customer_packages.index');
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

    private function sendEmail($data){
        $to = $data["email"];
        try {
            Mail::send('email.free_sub', $data, function ($messages) use ($to) {
                $messages->to($to);
                $messages->subject('GREEN CARD | FREE SUBSCRIPTION');
            });

        } catch (\Throwable $th) {

            Log::error($th);
        }
    }

    private function sendEmailToAdmin($data){
        $to = $data["email"];
        try {
            Mail::send('email.free_sub', $data, function ($messages) use ($to) {
                $messages->to('dev@greencard-sa.com');
                $messages->subject('ADMIN | GREEN CARD | FREE SUBSCRIPTION');
            });

        } catch (\Throwable $th) {

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customer_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_package = new CustomerPackage;
        $customer_package->name = $request->name;
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;
        $customer_package->duration = $request->duration;
        $customer_package->nb_members = $request->nb_members;
        if($request->has("hidden") && $request->input("hidden") == 1){
            $customer_package->hidden = 1;
        }else{
            $customer_package->hidden = 0;
        }
        if($request->has("hide_in_greencart") && $request->input("hide_in_greencart") == 1){
            $customer_package->hide_in_greencart = 1;
        }else{
            $customer_package->hide_in_greencart = 0;
        }
        if($request->has("hide_in_greencard") && $request->input("hide_in_greencard") == 1){
            $customer_package->hide_in_greencard = 1;
        }else{
            $customer_package->hide_in_greencard = 0;
        }
        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();


        flash(translate('Package has been inserted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $customer_package = CustomerPackage::findOrFail($id);
        return view('backend.customer.customer_packages.edit', compact('customer_package', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $customer_package->name = $request->name;
        }
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;
        $customer_package->duration = $request->duration;

        if($request->has("hidden") && $request->input("hidden") == 1){
            $customer_package->hidden = 1;
        }else{
            $customer_package->hidden = 0;
        }
        if($request->has("hide_in_greencart") && $request->input("hide_in_greencart") == 1){
            $customer_package->hide_in_greencart = 1;
        }else{
            $customer_package->hide_in_greencart = 0;
        }
        if($request->has("hide_in_greencard") && $request->input("hide_in_greencard") == 1){
            $customer_package->hide_in_greencard = 1;
        }else{
            $customer_package->hide_in_greencard = 0;
        }
        $customer_package->nb_members = $request->nb_members;


        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => $request->lang, 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();

        flash(translate('Package has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        foreach ($customer_package->customer_package_translations as $key => $customer_package_translation) {
            $customer_package_translation->delete();
        }
        CustomerPackage::destroy($id);

        flash(translate('Package has been deleted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    public function purchase_package(Request $request)
    {
        $paymentMethods = array("MADA", "VISA", "MASTER", "AMEX");
        $data['customer_package_id'] = $request->customer_package_id;
        $data['payment_method'] = $request->payment_option;

        $request->session()->put('payment_type', 'customer_package_payment');
        $request->session()->put('payment_data', $data);

        $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);

        if ($customer_package->amount == 0) {
            $user = User::findOrFail(Auth::user()->id);
            if ($user->customer_package_id != $customer_package->id) {
                return $this->purchase_payment_done(Session::get('payment_data'), null);
            } else {
                flash(translate('You can not purchase this package anymore.'))->warning();
                return back();
            }
        }
        if (in_array($data['payment_method'], $paymentMethods)) {

            $vat = floatval($customer_package->amount) * 0.15;
            $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
                    ->vatRegistrationNumber(get_setting("owner_vat_no"))
                    ->timestamp($customer_package->created_at)
                    ->totalWithVat($customer_package->amount+$vat)
                    ->vatTotal($vat)
                    ->toBase64();
            return redirect()->route("subscription-payment",[
                "amount" => $customer_package->amount,
                "qr_code" => $qrCode, //to generate qr with zakatka
                "vat_total" => floatval($customer_package->amount) * 0.15,
                "user_id" => auth()->user()->id,
                "package_id" => $customer_package->id,
                "card_type" => $data['payment_method'],
                "date_qr" => getCurrentDateInUTCFormat(),
                "coupon" => $request->coupon
            ]);
        }
        $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
        if (class_exists($decorator)) {
            return (new $decorator)->pay($request);
        }
    }

    public function purchase_payment_done($payment_data, $payment)
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->customer_package_id = $payment_data['customer_package_id'];
        $customer_package = CustomerPackage::findOrFail($payment_data['customer_package_id']);
        $user->remaining_uploads += $customer_package->product_upload;
        $user->save();

        flash(translate('Package purchasing successful'))->success();
        return redirect()->route('dashboard');
    }

    public function purchase_package_offline(Request $request)
    {
        $customer_package = new CustomerPackagePayment;
        $customer_package->user_id = Auth::user()->id;
        $customer_package->customer_package_id = $request->package_id;
        $customer_package->payment_method = $request->payment_option;
        $customer_package->payment_details = $request->trx_id;
        $customer_package->approval = 0;
        $customer_package->offline_payment = 1;
        $customer_package->reciept = ($request->photo == null) ? '' : $request->photo;
        $customer_package->save();
        flash(translate('Offline payment has been done. Please wait for response.'))->success();
        return redirect()->route('customer_products.index');
    }
}
