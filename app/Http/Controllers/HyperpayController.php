<?php
namespace App\Http\Controllers;


use Redirect;
use Session;
use DB;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class HyperpayController extends Controller
{
	public function mada()
	{

		$accesstoken = env('HYPERPAY_ACCESS_TOKEN');
		$entityid = env('HYPERPAY_ENTITYID');
	    // $combined_order_id = $request->combined_order_id;
	    // $amount = $request->amount;
	    // $user_id = $request->user_id;
	    // $user = User::find($request->user_id);

		if(Session::has('payment_type')){
			if(Session::get('payment_type') == 'cart_payment'){
				$combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
				$amount = $combined_order->grand_total;
				$address = json_decode($combined_order->shipping_address);

				$cnt = explode(" ",$address->country);
				$firstletter = ucfirst(substr($cnt['0'],0,1));
				$secletter = ucfirst(substr($cnt['1'],0,1));
				$countrycode = $firstletter."".$secletter;
				//$url = "https://test.oppwa.com/v1/checkouts";
				$url = "https://oppwa.com/v1/checkouts";


				$data = "entityId=$entityid".
				"&amount=$amount".
				"&currency=SAR".
				"&paymentType=DB".
				"&merchantTransactionId=$combined_order->id".
				"&customer.email=$address->email".
				"&billing.street1=$address->address".
				"&billing.city=$address->city".
				"&billing.state=$address->state".
				"&billing.country=$countrycode".
				"&billing.postcode=$address->postal_code".
				"&customer.givenName=$address->name".
				"&customer.surname=$address->name";

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Authorization:Bearer '.$accesstoken));
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
        	return curl_error($ch);
        }
		
        $arr = json_decode($responseData,true);
		Log::info("--- from hyperpay get payment checkout-----");
		Log::info($arr);
		Log::info($arr['id']);
		Log::info("responseData from hyperpay".$responseData);
        curl_close($ch);

        return view('frontend.hyperpay.index')->with('checkoutId',$arr['id']);
     }
  }

}

public function visa()
{

	$accesstoken = env('HYPERPAYVISA_ACCESS_TOKEN');
	$entityid = env('HYPERPAYVISA_ENTITYID');
	 
	if(Session::has('payment_type')){
		if(Session::get('payment_type') == 'cart_payment'){
			$combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
			$amount = $combined_order->grand_total;
			$address = json_decode($combined_order->shipping_address);
			
			$cnt = explode(" ",$address->country);
			$firstletter = ucfirst(substr($cnt['0'],0,1));
			$secletter = ucfirst(substr($cnt['1'],0,1));
			$countrycode = $firstletter."".$secletter;
				//$url = "https://test.oppwa.com/v1/checkouts";
			$url = "https://oppwa.com/v1/checkouts";


			$data = "entityId=$entityid" .
			"&amount=$amount" .
			"&currency=SAR" .
			"&paymentType=DB" .
			"&merchantTransactionId=$combined_order->id" .
			"&customer.email=$address->email" .
			"&billing.street1=$address->address" .
			"&billing.city=$address->city" .
			"&billing.state=$address->state" .
			"&billing.country=$countrycode" .
			"&billing.postcode=$address->postal_code" .
			"&customer.givenName=$address->name" .
			"&customer.surname=$address->name";


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization:Bearer '.$accesstoken));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
        	return curl_error($ch);
        }

        $arr = json_decode($responseData,true);
        curl_close($ch);

        return view('frontend.hyperpay.visa')->with('checkoutId',$arr['id']);
     }
  }

}


public function stcpay()
{


	$accesstoken = env('HYPERPAYSTCPAY_ACCESS_TOKEN');
	$entityid = env('HYPERPAYSTCPAY_ENTITYID');
	// $accesstoken = "OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg==";
	// $entityid = "8ac7a4ca7d97962b017d997ca72b06de";

	if(Session::has('payment_type')){
		if(Session::get('payment_type') == 'cart_payment'){
			$combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
			$amount = $combined_order->grand_total;
			$address = json_decode($combined_order->shipping_address);
			
			$cnt = explode(" ",$address->country);
			$firstletter = ucfirst(substr($cnt['0'],0,1));
			$secletter = ucfirst(substr($cnt['1'],0,1));
			$countrycode = $firstletter."".$secletter;
			//$url = "https://test.oppwa.com/v1/checkouts";
			$url = "https://oppwa.com/v1/checkouts";


			$data = "entityId=$entityid" .
			"&amount=$amount" .
			"&currency=SAR" .
			"&paymentType=DB" .
			"&merchantTransactionId=$combined_order->id" .
			"&customer.email=$address->email" .
			"&billing.street1=$address->address" .
			"&billing.city=$address->city" .
			"&billing.state=$address->state" .
			"&billing.country=$countrycode" .
			"&billing.postcode=$address->postal_code" .
			"&customer.givenName=$address->name" .
			"&customer.surname=$address->name";


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization:Bearer '.$accesstoken));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
        	return curl_error($ch);
        }

        $arr = json_decode($responseData,true);
        curl_close($ch);

        return view('frontend.hyperpay.stcpay')->with('checkoutId',$arr['id']);
     }
  }

}


public function visaresponse(Request $request){
	if($request->get('id')){
		 // $url = "https://test.oppwa.com/v1/checkouts/".$request->get('id')."/payment";
		 // $url .= "?entityId=8ac7a4ca7d97962b017d997ca72b06de";
      $url = "https://oppwa.com/v1/checkouts/".$request->get('id')."/payment";
		$url .= "?entityId=8acda4ca80464fa101805ffbceec05e2";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		// 	'Authorization:Bearer OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg=='));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization:Bearer OGFjZGE0Y2E4MDQ2NGZhMTAxODA1ZmZhNDgyNDA1Y2J8NE5zZ1B6NFpzbg=='));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		//return json_decode($responseData);
		$arr = json_decode($responseData,true);
		if($arr['result']['code'] == "000.100.110"){
		DB::table('hyperpaytra')->insert(
			array(
				'transactionid'     =>   $arr['id'], 
				'paymentBrand'   =>   $arr['paymentBrand']
			)
		);
		$payment_detalis = null;
		if(Session::has('payment_type')){
			if(Session::get('payment_type') == 'cart_payment'){
				$checkoutController = new CheckoutController;
				return $checkoutController->checkout_done(Session::get('combined_order_id'), json_encode($payment_detalis));
			}
		}
   }else{
   	return "something went wrong, please try again";
   }
   // $arr = json_decode($responseData,true);
   // if($arr['result']['code'] == "000.100.110"){
   // 	DB::table('hyperpaytra')->insert(
   // 		array(
   // 			'transactionid'     =>   $arr['id'], 
   // 			'paymentBrand'   =>   $arr['paymentBrand']
   // 		)
   // 	);
   // 	$payment_detalis = null;
   // 	if(Session::has('payment_type')){
   // 		if(Session::get('payment_type') == 'cart_payment'){
   // 			$checkoutController = new CheckoutController;
   // 			return $checkoutController->checkout_done(Session::get('combined_order_id'), json_encode($payment_detalis));
   // 		}
   // 	}
   //    //return view('frontend.thankyou.index');
   // }else{
   // 	return "something went wrong, please try again";
   // }
}

}

public function response(Request $request){
	if($request->get('id')){
		// $url = "https://test.oppwa.com/v1/checkouts/".$request->get('id')."/payment";

		// $url .= "?entityId=8ac7a4ca7d97962b017d997e2b8d06e4";
		$url = "https://oppwa.com/v1/checkouts/".$request->get('id')."/payment";

		$url .= "?entityId=8acda4ca80464fa101805ffc9f8d05ef";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization:Bearer OGFjZGE0Y2E4MDQ2NGZhMTAxODA1ZmZhNDgyNDA1Y2J8NE5zZ1B6NFpzbg=='));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		//return json_decode($responseData);
		$arr = json_decode($responseData,true);
		Log::info("--- from hyperpay get payment status-----");
		Log::info($arr);
		Log::info($arr['id']);
		Log::info("responseData from hyperpay".$responseData);
		if($arr['result']['code'] == "000.100.110"){
			DB::table('hyperpaytra')->insert(
				array(
					'transactionid'     =>   $arr['id'], 
					'paymentBrand'   =>   $arr['paymentBrand']
				)
			);
			$payment_detalis = null;
			if(Session::has('payment_type')){
				if(Session::get('payment_type') == 'cart_payment'){
					$checkoutController = new CheckoutController;
					return $checkoutController->checkout_done(Session::get('combined_order_id'), json_encode($payment_detalis));
				}
			}
			//return view('frontend.thankyou.index');
		}else{
			return "something went wrong, please try again";
		}
	}

}


}