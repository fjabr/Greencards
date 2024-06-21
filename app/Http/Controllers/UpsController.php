<?php
namespace App\Http\Controllers;


use Redirect;
use Session;
use DB;
use Auth;
use App\Models\Order;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;

class UpsController extends Controller{
	public function shipping($name,$phone,$email,$addressline,$city,$postalcode,$countrycode){

		  //$url = "https://wwwcie.ups.com/ship/v1/shipments";
	    //$url = "https://onlinetools.ups.com/ship/v1/shipments?additionaladdressvalidation=Jeddah";
	    $url = "https://onlinetools.ups.com/ship/v1/shipments";

		$data = [
			"UPSSecurity" => [
				"UsernameToken" => [
					"Username" =>"Ecomgreencard",
					"Password" => "Saudia@12345"
				],
				"ServiceAccessToken" => [
					"AccessLicenseNumber" => "5DADF7A60E8615DA"
				]
			],
			"ShipmentRequest" => [
				"Description" => "Test ORDER",
				"Request" => [
					"RequestOption"=>"validate"
				],
				"Shipment" => [
					"Description" => "Items to be Shipped",
					"Shipper" => [
						"Name" => "Green Card Development",
						"AttentionName" => "Green Card Development",
						"Phone" => [
							"Number" => "+971111111"
						],
						"ShipperNumber" => "W8V080",
						"FaxNumber" => "+971111111",
						"Address" => [
							"AddressLine" => ["Hamra district",
							"Jamjoom Center Gate 6",
							"Tower 1, 7th floor Office No. 743"],
							"City" => "JEDDAH",
							"StateProvinceCode" => "",
							"PostalCode" => "11111",
							"CountryCode" => "AR"
						]
					],
					"ShipTo" => [
						"Name" => $name,
						"AttentionName" => $name,
						"Phone" => [
							"Number" => $phone
						],
						"EMailAddress" => $email,
						"Address" => [
							"AddressLine" => [$addressline],
							"City" => $city,
							//"StateProvinceCode" => "NY",
							"PostalCode" => $postalcode,
							"CountryCode" => $countrycode
						]
					],
					"PaymentInformation" => [
						"ShipmentCharge" => [
							"Type" => "01",
							"BillShipper" => [
								"AccountNumber" => "W8V080"
							]
						]
					],
					"Service" => [
						"Code" => "65",
						"Description" => "UPS Saver"
					],
					"Package" => [
						[
							"ReferenceNumber" => [
								[
									"Value" => "154802031864_18-08349-98633"
								],
								[
									"Value" => "seo@dekulture.com"
								],
								[
									"Value" => "seonew@dekulture.com"
								]
							],
							"Description" => "Customer Supply Package",
							"Packaging" => [
								"Code" => "02"
							],
							"PackageWeight" => [
								"UnitOfMeasurement" => [
									"Code" => "KGS"
								],
								"Weight" => "1"
							]
						]
					],

					"LabelSpecification" => [
						"LabelImageFormat" => [
							"Code" => "ZPL","Description" => "ZPL"
						],
						"HTTPUserAgent" => "Mozilla/4.5",
						"LabelStockSize" => [
							"Height" => "6",
							"Width" => "4"
						]
					]
				]
			]
		];

		$headers = array(
			"Content-Type: application/json",
			"transId: Transaction123",
			"transactionSrc: TestTrack",
			"AccessLicenseNumber: 5DADF7A60E8615DA",
			"Username: Ecomgreencard",
			"Password: Saudia@12345",
			"Accept: application/json"
		);


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		//return json_decode($responseData);
		$arr = json_decode($responseData,true);
		return $arr;
	//return $arr['response']['errors']['0']['message'];
	}

	public function trackshipment(Request $request){
		// $id = Auth::user()->id;
  //       print_r($id);

    // $tabdata = DB::select('select * from orders ');
    // print_r($tabdata);

	// $tables = DB::select('SHOW TABLES');
 //    $user= new Order;
 //    $table = $user->getTable();
 //    $columns  = \Schema::getColumnListing($table);
 //    dd($columns);
	//$query = “SHOW COLUMNS FROM $tables[60]”;

		$data = [];
		if(isset($_GET['trackingnumber'])){
			$tracknum = $request->get('trackingnumber');
			//$url = "https://wwwcie.ups.com/track/v1/details/" .$tracknum;
		   $url = "https://onlinetools.ups.com/track/v1/details/" .$tracknum;

			$headers = array(
				"Content-Type: application/json",
				"transId: Transaction123",
				"transactionSrc: TestTrack",
				"AccessLicenseNumber: 5DADF7A60E8615DA",
				"Username: Ecomgreencard",
				"Password: Saudia@12345",
				"Accept: application/json"
			);


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

			$responseData = curl_exec($ch);
			if(curl_errno($ch)) {
				return curl_error($ch);
			}
			curl_close($ch);
			$data = json_decode($responseData,true);
			return $data;
			//return view('frontend.upstraking.index', compact('data'));
		  //return view('frontend.upstraking.index')->with('data',$data);
		}
		return view('frontend.upstraking.index');

	}

}
