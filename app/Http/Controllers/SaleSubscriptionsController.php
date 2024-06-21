<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\CustomerPackagePayment;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Utility\SendSMSUtility;
use App\Utility\SmsUtility;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Prgayman\Zatca\Facades\Zatca;
use Storage;
use Str;

class SaleSubscriptionsController extends Controller
{
    //

    public function upload_receipt(Request $request)
    {
        //return $request->hasFile("receipt")?"true":"false";
        $packagePayment = CustomerPackagePayment::where("id", $request->id)->first();
        //return $packagePayment;
        if (!empty($packagePayment)) {
            if ($request->hasFile("receipt")) {
                $file = $request->file('receipt');
                $filename = date('YmdHi') . "." . $file->getClientOriginalExtension();
                move_uploaded_file($file, public_path() . '/uploads/customer_package_payment_reciept/' . $filename);
                $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
                    ->vatRegistrationNumber(get_setting("owner_vat_no"))
                    ->timestamp(Carbon::now())
                    ->totalWithVat($packagePayment->amount)
                    ->vatTotal(12)
                    ->toBase64();
                $packagePayment->reciept = $filename;
                $packagePayment->qr_code = $qrCode;
                $packagePayment->save();
            }
        }
        return back();
    }

    public function all_customer()
    {
        $packagePaymentRequests = CustomerPackagePayment::where('offline_payment', 1)->orderBy('id', 'desc');
        if (Auth::user()->user_type == 'admin') {
            $packagePaymentRequests =  $packagePaymentRequests->paginate(10);
        } else {
            $packagePaymentRequests = $packagePaymentRequests
                ->join("users", "users.id", "customer_package_payments.user_id")
                ->join("customers", "customers.user_id", "users.id")
                ->where("customers.created_by", Auth::user()->id)
                ->select("customer_package_payments.*")
                ->paginate(10);
        }
        // return $packagePaymentRequests;
        return view('backend.sale_subs.customers', compact('packagePaymentRequests'));
    }


    public function renew_subscription_confirmation(Request $request)
    {
        try {

            if (!$request->filled('user_id')) {
                throw new Exception(translate('Error while renew action'), 99);
            }

            $authUser = Auth::user();
            $user = User::where("id", $request->input("user_id"))->first();
            if(empty($user)){
                throw new Exception(translate('cannot find user'), 99);
            }

            if ($authUser->user_type == 'admin') {
                $packages = CustomerPackage::all();
            } else {
                $packages = CustomerPackage::where("hidden", 0)->get();
            }

            $package = CustomerPackage::where("id", $request->input("package"))->first();
            $total_amount = $package->amount;
            $appliedCouponCalculation = [];
            if ($request->has("coupon") && !empty($request->input("coupon"))) {
                if (!empty($package)) {

                    $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
                    if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                        flash(translate("Coupon not applied, maybe try again with a valid one"));
                    } else {
                        $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
                    }
                }
            }

            $vta_total = floatval($total_amount) * 0.15;
            return view('backend.sale_subs.renew_confirmation', [
                "vta_total" => $vta_total,
                "total_amount" => $total_amount,
                "packages" => $packages,
                "user" => $user,
                "request" => [
                    "package" => $request->input("package"),
                    "coupon" => $request->input("coupon"),
                    "payment_method" => $request->input("payment_method"),
                    "deliveryComment" => $request->input("deliveryComment"),
                    "country_code" => $request->input("country_code")
                ],
                "appliedCouponCalculation" => $appliedCouponCalculation

            ]);
        } catch (Exception $ex) {
            if ($ex->getCode() == 99) {
                flash($ex->getMessage())->error();
            } else {
                flash(translate('Customer was not created, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return back();
        }
    }




    function renew_sub(Request $request, User $user) {
        $AuthUser = Auth::user();
        if ($AuthUser->user_type == 'admin') {
            $packages = CustomerPackage::all();
        } else {
            $packages = CustomerPackage::where("hidden", 0)->get();
        }


        return view('backend.sale_subs.renew_subs',compact('packages', 'user'));

    }

    function renew_subscription_post(Request $request) {
        DB::beginTransaction();
        try {

            if (!$request->filled('user_id')) {
                throw new Exception(translate('Error while renew action'), 99);
            }

            $user = User::where("id", $request->input("user_id"))->first();

            if(empty($user)){
                throw new Exception(translate('cannot find user'), 99);
            }

            $package = CustomerPackage::where("id", $request->input("package"))->first();

            $user->duration = $package->duration;
            $user->nb_members = $package->nb_members - 1;
            $user->email_verified_at = Carbon::now();
            $user->save();
            $total_amount = $package->amount;
            $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
            if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                flash(translate("Coupon not applied, maybe try again with a valid one"));
            } else {
                $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
            }
            $vta_total = floatval($total_amount) * 0.15;

            $customerPackagePayment = new CustomerPackagePayment();
            $customerPackagePayment->customer_package_id = $package->id;
            $customerPackagePayment->user_id = $user->id;
            switch ($request->input("payment_method")) {
                case 1:
                case 3:
                    $customerPackagePayment->payment_method = "Offline";
                    $customerPackagePayment->offline_payment = 1;
                    break;
                case 2:
                    $customerPackagePayment->payment_method = "onligne";
                    $customerPackagePayment->offline_payment = 2;
                    break;
                default:
                    # code...
                    break;
            }
            $customerPackagePayment->amount = $total_amount;
            $customerPackagePayment->approval = 0;
            $customerPackagePayment->date_invoice = Carbon::now();
            $customerPackagePayment->vat_total = $vta_total;
            if ($request->has("deliveryComment") && !empty($request->input("deliveryComment"))) {
                $customerPackagePayment->deliveryComment = $request->input("deliveryComment");
            }
            $customerPackagePayment->save();

            DB::commit();
            flash(translate('Customer subscription has been renewed successfully'))->success();
            return redirect()->route('sale_subscription.subscription');
        } catch (Exception $ex) {
            if ($ex->getCode() == 99) {
                flash($ex->getMessage())->error();
            } else {
                flash(translate('Customer subscription was not renewed, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return redirect()->route('sale_subscription.subscription');
        }
    }


    public function sale_sub()
    {
        $user = Auth::user();
        if ($user->user_type == 'admin') {
            $packages = CustomerPackage::all();
        } else {
            $packages = CustomerPackage::where("hidden", 0)->get();
        }

        return view('backend.sale_subs.sale_subs', [
            "packages" => $packages
        ]);
    }

    public function confirmation(Request $request)
    {
        try {

            if (!$request->filled('email') && !$request->filled('mobile')) {
                throw new Exception(translate('Email or mobile are required'), 99);
            }

            if ($request->filled('mobile') && !$request->filled('country_code')) {
                throw new Exception(translate('Country code is required'), 99);
            }

            if ($request->filled('email')) {
                $userAlreadyExisted = User::where("email", $request->input("email"))->first();
                if (!empty($userAlreadyExisted)) {
                    $packagePayment = CustomerPackagePayment::where("user_id",$userAlreadyExisted->id)->where("approval",1)->orderByDesc("created_at")->first();
                    if(empty($packagePayment)){
                        $request->request->set('user_id', $userAlreadyExisted->id);
                        return $this->renew_subscription_confirmation($request);
                    }
                    flash(translate('Customer already created'))->warning();
                    return  redirect()->route('sale_subscription.details',["packagePayment" => $packagePayment->id]);
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
                if (!empty($userAlreadyExisted)) {$packagePayment = CustomerPackagePayment::where("user_id",$userAlreadyExisted->id)->where("approval",1)->orderByDesc("created_at")->first();
                    if(empty($packagePayment)){
                        $request->request->set('user_id', $userAlreadyExisted->id);
                        return $this->renew_subscription_confirmation($request);
                    }
                    flash(translate('Customer already created'))->warning();
                    return  redirect()->route('sale_subscription.details',["packagePayment" => $packagePayment->id]);
                }
            }

            $user = Auth::user();
            if ($user->user_type == 'admin') {
                $packages = CustomerPackage::all();
            } else {
                $packages = CustomerPackage::where("hidden", 0)->get();
            }

            $package = CustomerPackage::where("id", $request->input("package"))->first();
            $total_amount = $package->amount;
            $appliedCouponCalculation = [];
            if ($request->has("coupon") && !empty($request->input("coupon"))) {
                if (!empty($package)) {

                    $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
                    if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                        flash(translate("Coupon not applied, maybe try again with a valid one"));
                    } else {
                        $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
                    }
                }
            }

            $vta_total = floatval($total_amount) * 0.15;
            return view('backend.sale_subs.confirmation', [
                "vta_total" => $vta_total,
                "total_amount" => $total_amount,
                "packages" => $packages,
                "request" => [
                    "first_name" => $request->input("first_name"),
                    "last_name" => $request->input("last_name"),
                    "email" => $request->input("email"),
                    "mobile" => $request->input("mobile"),
                    "package" => $request->input("package"),
                    "coupon" => $request->input("coupon"),
                    "payment_method" => $request->input("payment_method"),
                    "deliveryComment" => $request->input("deliveryComment"),
                    "country_code" => $request->input("country_code")
                ],
                "appliedCouponCalculation" => $appliedCouponCalculation

            ]);
        } catch (Exception $ex) {
            if ($ex->getCode() == 99) {
                flash($ex->getMessage())->error();
            } else {
                flash(translate('Customer was not created, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return back();
        }
    }


    /**
     * customer with free sub page.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_customer(Request $request)
    {
        DB::beginTransaction();
        try {

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
            $package = CustomerPackage::where("id", $request->input("package"))->first();
            $user->name = $request->input("first_name") . " " . $request->input("last_name");
            $user->phone = $formatted_phone;
            $user->password = \bcrypt($password);
            if ($request->filled("email")) {
                $user->email = $request->input("email");
            }
            $user->email_verified_at = Carbon::now();
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->created_by = Auth::user()->id;
            $customer->save();
            $total_amount = $package->amount;
            $appliedCouponCalculation = applyCouponCaculation($request->input("coupon"), $package->amount);
            if (empty($appliedCouponCalculation) || $appliedCouponCalculation['discountAmount'] == 0) {
                flash(translate("Coupon not applied, maybe try again with a valid one"));
            } else {
                $total_amount = $package->amount - $appliedCouponCalculation['discountAmount'];
            }
            $vta_total = floatval($total_amount) * 0.15;

            $customerPackagePayment = new CustomerPackagePayment();
            $customerPackagePayment->customer_package_id = $package->id;
            $customerPackagePayment->user_id = $user->id;
            switch ($request->input("payment_method")) {
                case 1:
                case 3:
                    $customerPackagePayment->payment_method = "Offline";
                    $customerPackagePayment->offline_payment = 1;
                    break;
                case 2:
                    $customerPackagePayment->payment_method = "onligne";
                    $customerPackagePayment->offline_payment = 2;
                    break;
                default:
                    # code...
                    break;
            }
            $customerPackagePayment->amount = $total_amount;
            $customerPackagePayment->approval = 0;
            $customerPackagePayment->date_invoice = Carbon::now();
            $customerPackagePayment->vat_total = $vta_total;
            if ($request->has("deliveryComment") && !empty($request->input("deliveryComment"))) {
                $customerPackagePayment->deliveryComment = $request->input("deliveryComment");
            }
            $customerPackagePayment->save();

            $data = [
                "email" => $user->email,
                "password" => $password,
            ];
            if (!empty($request->input("mobile"))) {
                if (!SmsUtility::sale_new_sub($user, $password)) {
                    flash(translate('Problem while sending SMS notification'))->error();
                } else {
                    flash(translate('SMS notification has been sent succesfully to customer'))->success();
                }
            }

            if (!empty($request->input("email"))) {
                $this->sendEmail($data);
            } else {
                $data["email"] = $user->phone;
            }
            $this->sendEmailToAdmin($data);
            DB::commit();
            flash(translate('Customer has been created successfully'))->success();
            return redirect()->route('sale_subscription.subscription');
        } catch (Exception $ex) {
            if ($ex->getCode() == 99) {
                flash($ex->getMessage())->error();
            } else {
                flash(translate('Customer was not created, an error was accured'))->error();
            }
            Log::error($ex);
            DB::rollBack();
            return redirect()->route('sale_subscription.subscription');
        }
    }


    private function sendEmail($data)
    {
        $to = $data["email"];
        try {
            Mail::send('email.signup', $data, function ($messages) use ($to) {
                $messages->to($to);
                $messages->subject('GREEN CARD | NEW SUBSCRIPTION');
            });
        } catch (\Throwable $th) {

            Log::error($th);
        }
    }

    private function sendEmailToAdmin($data)
    {
        try {
            Mail::send('email.signup', $data, function ($messages) use ($to) {
                $messages->to('dev@greencard-sa.com');
                $messages->subject('ADMIN | GREEN CARD | NEW SUBSCRIPTION');
            });
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    function subscription_details(Request $request, CustomerPackagePayment $packagePayment) {

        return view('backend.sale_subs.subscription_details',compact('packagePayment'));
    }
}
