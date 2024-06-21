<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerPackage;
use App\Utility\EmailUtils;
use App\Utility\PdfUtils;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\SellerSubscriptionReport;
use App\Models\Shop;
use App\Models\User;
use App\Utility\SmsUtility;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;
use PDF;
use Prgayman\Zatca\Facades\Zatca;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:seller_payment_history'])->only('payment_histories');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $payments = Payment::where('seller_id', Auth::user()->seller->id)->paginate(9);
    //     return view('seller.payment_history', compact('payments'));
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_histories(Request $request)
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(15);
        return view('backend.sellers.payment_histories.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find(decrypt($id));
        $payments = Payment::where('seller_id', $user->id)->orderBy('created_at', 'desc')->get();
        if ($payments->count() > 0) {
            return view('backend.sellers.payment', compact('payments', 'user'));
        }
        flash(translate('No payment history available for this seller'))->warning();
        return back();
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function apply_coupon_code($amount, $couponCode, $user_id)
    {
        $coupon = Coupon::where('code', $couponCode)->where('type', 'subscription_base')->first();
        if ($coupon == null)
            return ['success' => false, 'message' => 'Invalid coupon'];

        if (strtotime(date('d-m-Y')) < $coupon->start_date || strtotime(date('d-m-Y')) > $coupon->end_date) {
            return ['success' => false, 'message' => 'Coupon expired'];
        }

        $couponSubscription = CouponUsage::where('user_id', $user_id)->where('coupon_id', $coupon->id)->first();
        if ($couponSubscription != null) {
            return ['success' => false, 'message' => 'You already used this coupon!'];
        }

        $coupon_discount = 0;
        if ($coupon->discount_type == 'percent') {
            $coupon_discount = ($amount * $coupon->discount) / 100;
        } elseif ($coupon->discount_type == 'amount') {
            $coupon_discount = $coupon->discount;
        }

        return ['success' => true, 'message' => 'Coupon has been applied', 'coupon_discount' => $coupon_discount];
    }

    public function subscriptionPayment()
    {
        $amount = !empty($_GET['amount']) ? floatval($_GET['amount']) : null;

        $vat_total = !empty($_GET['vat_total']) ? $_GET['vat_total'] : null;
        $date_qr = !empty($_GET['date_qr']) ? $_GET['date_qr'] : null;
        $user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : null;
        $package_id = !empty($_GET['package_id']) ? $_GET['package_id'] : null;
        $card_type = !empty($_GET['card_type']) ? $_GET['card_type'] : null;
        $coupon = !empty($_GET['coupon']) ? $_GET['coupon'] : null;
        $action = !empty($_GET['action']) ? $_GET['action'] : null;


        // TODO:  generate qr from here
        if (!$amount || !$vat_total || !$date_qr || !$user_id || !$package_id || !$card_type || ($action && $action !== "renew")) {
            return view('frontend.payment.subscription_payment', [
                'paymentResponse' => [
                    'message' => 'Unable to process your payment. Please contact admin.',
                    'messageAr' => "غير قادر على معالجة الدفع الخاص بك. يرجى الاتصال بالمسؤول.",
                    "success" => false,
                    "exist" => false,
                ]
            ]);
        }

        if (env("HYPERPAY_ACTIVE") != "on") {
            return view('frontend.payment.subscription_payment', [
                'paymentResponse' => [
                    'message' => 'Payment online not active for now, please try again',
                    'messageAr' => "الدفع عبر الإنترنت غير نشط في الوقت الحالي ، يرجى المحاولة مرة أخرى",
                    "success" => false,
                    "exist" => false,
                ]
            ]);
        }

        $customer = null;
        if (!$action) {
            $customer = DB::table('users')->where('id', $user_id)->where('customer_package_id', "!=", null)->first();
        }

        if ($customer != null) {
            return view('frontend.payment.subscription_payment', [
                'paymentResponse' => [
                    'message' => 'You have already pay.',
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }

        $qr_code = Zatca::sellerName(get_setting("owner_company_name"))
            ->vatRegistrationNumber(get_setting("owner_vat_no"))
            ->timestamp(Carbon::now())
            ->totalWithVat($amount)
            ->vatTotal($vat_total)
            ->toBase64();
        $params = [
            'amount' => $amount,
            'qr_code' => $qr_code,
            'vat_total' => $vat_total,
            'date_qr' => $date_qr,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'coupon' => $coupon,
            'action' => $action,
            'card_type' => $card_type,
            "registration_source" =>  !empty($_GET['registration_source']) ? $_GET['registration_source'] : null,
            "seller_id" => !empty($_GET['seller_id']) ? $_GET['seller_id'] : null,
            "source" => !empty($_GET['source']) ? $_GET['source'] : null
        ];

        return view('frontend.payment.subscription_payment', [
            'paymentResponse' => [
                'success' => true,
                'data' => $params,
                'query' => http_build_query($params)
            ],
        ]);
    }

    public function subscriptionCheckout($id)
    {
        $params = [
            'amount' => $_GET['amount'],
            'qr_code' => $_GET['qr_code'],
            'vat_total' => $_GET['vat_total'],
            'date_qr' => getCurrentDateInUTCFormat(),
            'user_id' => $_GET['user_id'],
            'package_id' => $_GET['package_id'],
            'card_type' => $_GET['card_type'],
            'coupon' => !empty($_GET['coupon']) ? $_GET['coupon'] : null,
            'action' => !empty($_GET['action']) ? $_GET['action'] : null,
            "registration_source" =>  !empty($_GET['registration_source']) ? $_GET['registration_source'] : null,
            "seller_id" => !empty($_GET['seller_id']) ? $_GET['seller_id'] : null,
            "source" => !empty($_GET['source']) ? $_GET['source'] : null
        ];

        $amount = floatval($_GET['amount']);

        Log::info(json_encode($params, true));
        Log::info(http_build_query($params));
        return view('frontend.payment.subscription_checkout', [
            'id' => $id,
            'card_type' => $_GET['card_type'],
            'amount' => $amount,
            'query' => http_build_query($params)
        ]);
    }

    public function subscriptionComplete()
    {
        try {
            $resourcePath = urldecode($_GET['resourcePath']);
            $params = [
                'amount' => $_GET['amount'],
                'qr_code' => $_GET['qr_code'],
                'vat_total' => $_GET['vat_total'],
                'date_qr' => $_GET['date_qr'],
                'user_id' => $_GET['user_id'],
                'package_id' => $_GET['package_id'],
                'card_type' => $_GET['card_type'],
                'coupon' => !empty($_GET['coupon']) ? $_GET['coupon'] : null,
                'action' => !empty($_GET['action']) ? $_GET['action'] : null
            ];

            if (env("HYPERPAY_MODE_TEST") == "on") {
                if ($params['card_type'] === 'MADA')
                    $entityId = '8ac7a4ca7d97962b017d997e2b8d06e4';
                if ($params['card_type'] === 'VISA')
                    $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                if ($params['card_type'] === 'MASTER')
                    $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                if ($params['card_type'] === 'STC_PAY')
                    $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                $baseUrl = "https://eu-test.oppwa.com/" . $resourcePath . "?entityId=" . $entityId;
                $token = "OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg==";
            } else {
                if ($params['card_type'] === 'MADA')
                    $entityId = env('HYPERPAY_ID');
                if ($params['card_type'] === 'VISA')
                    $entityId = env('HYPERPAYVISA_ENTITYID');
                if ($params['card_type'] === 'MASTER')
                    $entityId = env('HYPERPAYMASTER_ENTITYID');
                if ($params['card_type'] === 'STC_PAY')
                    $entityId = env('HYPERPAYMASTER_ENTITYID');
                $baseUrl = "https://oppwa.com/" . $resourcePath . "?entityId=" . $entityId;
                $token = "OGFjZGE0Y2E4MDQ2NGZhMTAxODA1ZmZhNDgyNDA1Y2J8NE5zZ1B6NFpzbg==";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $token));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            $curlError = null;
            if (curl_errno($ch)) {
                $curlError = curl_error($ch);
            }
            curl_close($ch);

            $responseData = json_decode($responseData, true);
            $res = $responseData['result'];
            Log::info("data payment");
            Log::info($res);
            Log::info("code : " . $res['code']);
            Log::info(preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $res['code']) ? "match code" : "code didn't match");
            if (!preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $res['code'])) {
                return view('frontend.payment.subscription_complete', [
                    'paymentResponse' => [
                        'message' => 'Your payment was not successful. Please try again.',
                        'messageAr' => "الدفع الخاص بك لم يكن ناجحا. حاول مرة اخرى.",
                        "success" => false,
                        "exist" => true,
                    ]
                ]);
            }

            $customer_id = null;
            try {
                Log::info("insert customer with user id ");
                Log::info($params['user_id']);
                $customer_id = DB::table('customers')->insertGetId([
                    'user_id' => $params['user_id'],
                ]);
            } catch (\Throwable $th) {
                Log::error($th);
            }

            if ($customer_id === null) {
                Log::info("uncreated customer, customer id is null");
                return view('frontend.payment.subscription_complete', [
                    'paymentResponse' => [
                        'message' => 'An error occurred while processing payment. Please contact administrator.',
                        'messageAr' => "حدث خطأ أثناء معالجة الدفع. يرجى الاتصال بالمسؤول.",
                        "success" => true,
                        "exist" => false,
                    ]
                ]);
            }


            $current_package = CustomerPackage::where("id", $params['package_id'])->first();
            $user                       = User::where('id', $params['user_id'])->first();
            $user->customer_package_id  = $current_package->id;
            $user->start_sub_date       = !empty($user->start_sub_date) && $user->start_sub_date!= null ? new Carbon($user->start_sub_date) : new Carbon();
            $user->end_sub_date         = (!empty($user->end_sub_date) && $user->end_sub_date !=null ?new Carbon($user->end_sub_date) : Carbon::now() )->addDays($current_package->duration);
            $user->nb_members           = $current_package->nb_members-1;
            $user->remaining_uploads    = $user->remaining_uploads + $current_package->product_upload;
            $user->duration             = $user->duration + $current_package->duration;
            $user->save();

            $package = DB::table('customer_packages')
                ->where('customer_packages.id', $params['package_id'])
                ->leftJoin('uploads', "customer_packages.logo", "=", "uploads.id")
                ->join('customer_package_translations', 'customer_packages.id', '=', 'customer_package_translations.customer_package_id')
                ->where('customer_package_translations.lang', "en")
                ->select('customer_packages.*', 'uploads.file_name as url_logo', 'customer_package_translations.name as label')
                ->first();

            $payment_id = DB::table('customer_package_payments')->insertGetId([
                'user_id' => $params['user_id'],
                "customer_package_id" => $params['package_id'],
                "payment_method" => "Bank card",
                "payment_details" => $responseData['id'],
                "approval" => 1,
                "offline_payment" => 2,
                "reciept" => "",
                "qr_code" => $params['qr_code'],
                "vat_total" => $params['vat_total'],
                "amount" => $params['amount'],
                "date_invoice" => $params['date_qr']
            ]);

            if (!empty($params['coupon'])) {
                $coupon = Coupon::where('code', $params['coupon'])->where('type', 'subscription_base')->first();
                if ($coupon) {
                    $couponUsage = new CouponUsage;
                    $couponUsage->user_id = $params['user_id'];
                    $couponUsage->coupon_id = $coupon->id;
                    $couponUsage->save();
                }
            }

            $data = [
                "user_name" => $user->name,
                "email" => $user->email ?? $user->phone,
                "package_name" => $package->label,
                "package_price" => $params['amount'],
                "date" => $params['date_qr'],
                "invoice_id" => $payment_id,
                "qr_code" => $params['qr_code'],
                "vat_val" => $params['vat_total']
            ];
            $to = $user->email;

            $pdf = PdfUtils::generate('email.invoice', (object) $data);

            $attachment = null;
            if ($pdf)
                $attachment = ['name' => 'invoice.pdf', 'file' => $pdf];

            if (!empty($user->phone)) {
                SmsUtility::subscripiton_payment_suceess($user);
            }

            Log::info("registration_source: ". session("registration_source") );
            Log::info("source: ". session("source") );
            Log::info("coupon: ". session("coupon") );
            Log::info("shop_id: ". session("shop_id") );
            // CREATE REPORT
            if( !empty($_GET['registration_source']) &&  $_GET['registration_source'] !== null){
                $sellerSubscriptionReport = new SellerSubscriptionReport();
                $sellerSubscriptionReport->user_id = $params['user_id'];
                $sellerSubscriptionReport->shop_id = $_GET['seller_id'] ;
                $sellerSubscriptionReport->source = $_GET['source'] ;
                $sellerSubscriptionReport->package_id = $params['package_id'];
                $sellerSubscriptionReport->amount = $package->amount;
                $sellerSubscriptionReport->payed_amount = $params['amount'];
                $sellerSubscriptionReport->coupon = $params['coupon'];
                $sellerSubscriptionReport->save();
            }
            // User Email
            EmailUtils::sendMail($to, 'Invoice Green Card SA', $data, $attachment);

            // Admin Email
            EmailUtils::sendMail('dev@greencard-sa.com', 'Admin Notification - Invoice Green Card SA', $data, $attachment);

            return view('frontend.payment.subscription_complete', [
                'paymentResponse' => [
                    'message' => 'Your payment was successful.',
                    'messageAr' => "تم الدفع الخاص بك بنجاح",
                    "success" => true,
                    "exist" => false
                ]
            ]);
        } catch (\Throwable $th) {
            Log::info($th);
            return view('frontend.payment.subscription_complete', [
                'paymentResponse' => [
                    'message' => 'An error occurred while processing payment. Please contact administrator.',
                    'messageAr' => "حدث خطأ أثناء معالجة الدفع. يرجى الاتصال بالمسؤول.",
                    "success" => false,
                    "exist" => false,
                ]
            ]);
        }
    }

    private function sendSMS($login, $password, $mobile)
    {
        $post = (Object) [
            "userName" => "Gcsms",
            "apiKey" => "e2a372ac1e4afaf53677dbf3192eee12",
            "numbers" => $mobile,
            "userSender" => "GREENCARD",
            "msg" => "Login: $login\nPassword: $password",
            "msgEncoding" => "UTF8"
        ];
        // dd($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.msegat.com/gw/sendsms.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Cache-Control: no-cache",
                "content-type:application/json;charset=utf-8"
            )
        );
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }
}
