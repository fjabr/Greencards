<?php

namespace App\Http\Controllers;

use App\Models\CombinedOrder;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Log;
use DB;
use Illuminate\Support\Facades\Auth;
class OrderPaymentController extends Controller
{
    //

    function orderPayment(Request $request)
    {
        $params = [
            "amount" => floatval($request->input('amount')),
            "qr_code" => $request->input("qr_code"),
            "vat_total" => $request->input("vat_total"),
            "user_id" => $request->input("user_id"),
            "card_type" => $request->input("card_type"),
            "date_qr" => $request->input("date_qr"),
            "coupon" => $request->input("coupon"),
            "combined_order_id" => $request->input("combined_order_id"),
            "isRechargeWallet" => $request->input("isRechargeWallet"),
        ];


        if (env("HYPERPAY_ACTIVE") != "on") {
            return view('frontend.payment.order_payment', [
                'paymentResponse' => [
                    'message' => 'Payment online not active for now, please try again',
                    'messageAr' => "الدفع عبر الإنترنت غير نشط في الوقت الحالي ، يرجى المحاولة مرة أخرى",
                    "success" => false,
                    "exist" => false,
                ]
            ]);
        }

        $user = User::where('id', $request->input("user_id"))->first();
        if (empty($user)) {
            return view('frontend.payment.order_payment', [
                'paymentResponse' => [
                    'message' => 'You have already pay.',
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }

        Auth::login($user);

        $combinedOrder = CombinedOrder::where("id",$request->input("combined_order_id"))->where('user_id',$user->id)->first();

        if ((empty($combinedOrder) && $request->input("isRechargeWallet")!=1) || floatval($request->input('amount')) == null) {
            return view('frontend.payment.order_payment', [
                'paymentResponse' => [
                    'message' => translate('Failed to pay.'),
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }



        return view('frontend.payment.order_payment', [
            'paymentResponse' => [
                'success' => true,
                'data' => $params,
                'query' => http_build_query($params)
            ],
        ]);
    }


    public function orderCheckout(Request $request, $id)
    {
        $params = [
            "amount" => $request->input("amount"),
            "qr_code" => $request->input("qr_code"),
            "vat_total" => $request->input("vat_total"),
            "user_id" => $request->input("user_id"),
            "card_type" => $request->input("card_type"),
            "date_qr" => $request->input("date_qr"),
            "coupon" => $request->input("coupon"),
            "combined_order_id" => $request->input("combined_order_id"),
            "isRechargeWallet" => $request->input("isRechargeWallet"),
        ];


        $customer = User::where('id', $request->input("user_id"))->first();
        if (empty($customer)) {
            return view('frontend.payment.order_payment', [
                'paymentResponse' => [
                    'message' => 'You have already pay.',
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }

        Auth::login($customer);
        $combinedOrder = CombinedOrder::where("id",$request->input("combined_order_id"))->where('user_id',$customer->id)->first();
        if (empty($combinedOrder)&& $request->input("isRechargeWallet")!=1) {
            return view('frontend.payment.order_payment', [
                'paymentResponse' => [
                    'message' => translate('Failed to pay.'),
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }


        $amount = floatval($request->input('amount'));

        return view('frontend.payment.payment_checkout', [
            'id' => $id,
            'card_type' => $request->input('card_type'),
            'amount' => $amount,
            'query' => http_build_query($params)
        ]);
    }



    public function orderComplete(Request $request) {

        $resourcePath = urldecode($request->input('resourcePath'));
        $params = [
            "amount" => $request->input("amount"),
            "qr_code" => $request->input("qr_code"),
            "vat_total" => $request->input("vat_total"),
            "user_id" => $request->input("user_id"),
            "card_type" => $request->input("card_type"),
            "date_qr" => $request->input("date_qr"),
            "coupon" => $request->input("coupon"),
            "combined_order_id" => $request->input("combined_order_id")
        ];

        if (env("HYPERPAY_MODE_TEST") == "on") {
            if ($params['card_type'] === 'MADA') $entityId = '8ac7a4ca7d97962b017d997e2b8d06e4';
            if ($params['card_type'] === 'VISA') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
            if ($params['card_type'] === 'MASTER') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
            if ($params['card_type'] === 'STC_PAY') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
            $baseUrl = "https://eu-test.oppwa.com/".$resourcePath."?entityId=".$entityId;
            $token = "OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg==";
        } else {
            if ($params['card_type'] === 'MADA') $entityId = env('HYPERPAY_ID');
            if ($params['card_type'] === 'VISA') $entityId = env('HYPERPAYVISA_ENTITYID');
            if ($params['card_type'] === 'MASTER') $entityId = env('HYPERPAYMASTER_ENTITYID');
            if ($params['card_type'] === 'STC_PAY') $entityId = env('HYPERPAYMASTER_ENTITYID');
            $baseUrl = "https://oppwa.com/".$resourcePath."?entityId=".$entityId;
            $token = "OGFjZGE0Y2E4MDQ2NGZhMTAxODA1ZmZhNDgyNDA1Y2J8NE5zZ1B6NFpzbg==";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        $curlError = null;
        if(curl_errno($ch)) {
            $curlError = curl_error($ch);
        }
        curl_close($ch);

        $responseData = json_decode($responseData, true);
        $res = $responseData['result'];
        Log::info("data payment");
        Log::info($res);
        Log::info("code : ".$res['code']);
        Log::info(preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $res['code'])?"match code":"code didn't match");
        if(!preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $res['code'])){
            return view('frontend.payment.order_complete', [
                'paymentResponse' => [
                    'message' => 'Your payment was not successful. Please try again.',
                    'messageAr' => "الدفع الخاص بك لم يكن ناجحا. حاول مرة اخرى.",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }
        $customer = User::where('id', $request->input("user_id"))->first();
        if (empty($customer)) {
            return view('frontend.payment.order_complete', [
                'paymentResponse' => [
                    'message' => 'You have already pay.',
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => false,
                    "exist" => true,
                ]
            ]);
        }
        if($request->input("isRechargeWallet") == 1){
            $wallet = new Wallet();
            $wallet->user_id = $params["user_id"];
            $wallet->amount =  $params["amount"];
            $wallet->payment_method =  $params["card_type"];
            $wallet->payment_details = $responseData['id'];
            $wallet->approval = 1;
            $wallet->offline_payment = 0;
            $wallet->save();
            $customer->balance =  $wallet->amount + $customer->balance;
            $customer->save();
            return view('frontend.payment.order_complete', [
                'paymentResponse' => [
                    'message' => translate("you have succussfuly rechaarged your wallet "),
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => true,
                    "exist" => true,
                ]
            ]);
        }


        Auth::login($customer);

        DB::table('hyperpaytra')->insert(
            array(
                'transactionid'     =>   $responseData['id'],
                'paymentBrand'   =>   $responseData['paymentBrand']
            )
        );

        $checkoutController = new CheckoutController;
        return $checkoutController->checkout_done($request->input('combined_order_id'), json_encode($responseData));
    }

}
