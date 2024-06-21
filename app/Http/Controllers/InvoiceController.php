<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\CustomerPackage;
use App\Models\CustomerPackagePayment;
use App\Models\Language;
use App\Models\Order;
use App\Models\User;
use App\Utility\EmailUtils;
use App\Utility\PdfUtils;
use Session;
use PDF;
use Config;
use Exception;
use Illuminate\Http\Request;
use Prgayman\Zatca\Facades\Zatca;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{



    public function send_bill_via_email(Request $request,$id)
    {
        try{
            $package_payment    = CustomerPackagePayment::findOrFail(decrypt($id));

            $package_details    = CustomerPackage::findOrFail($package_payment->customer_package_id);

            $user = auth()->user();
            $data =[
                "user_name" => $user->name,
                "payment_id" => $package_payment->id,
                "email" => $user->email,
                "package_name" => $package_details->name,
                "package_price" => $package_payment->amount,
                "date" => $package_payment->date_invoice,
                "invoice_id" => $package_payment->id,
                "qr_code" => $package_payment->qr_code,
                "vat_val" => $package_payment->vat_total,
            ];

            $to = $user->email;
            $pdf = PdfUtils::generate('email.invoice', $data);

            $attachment = null;
            if ($pdf) $attachment = ['name' => 'invoice.pdf', 'file' => $pdf];

            // User Email
            EmailUtils::sendMail($to, 'Invoice Green Card SA', $data, $attachment);
            EmailUtils::sendMail('care@greencard-sa.com', 'Admin Invoice Green Card SA', $data, $attachment);

            flash("email has  been sent")->success();
            return back();
        }catch(Exception $ex){
            flash("Error accured, email has no been sent")->error();
            return back();
        }





    }
    public function download_bill(Request $request,$id)
    {
        $package_payment    = CustomerPackagePayment::findOrFail(decrypt($id));

        $package_details    = CustomerPackage::findOrFail($package_payment->customer_package_id);

        $user = auth()->user();
        $data =(object)[
            "user_name" => $user->name,
            "payment_id" => $package_payment->id,
            "email" => $user->email,
            "package_name" => $package_details->name,
            "package_price" => $package_payment->amount,
            "date" => $package_payment->date_invoice,
            "invoice_id" => $package_payment->id,
            "qr_code" => $package_payment->qr_code,
            "vat_val" => $package_payment->vat_total,
        ];
        if(empty($user->email) || $user->email== null){
            $data->email = $user->phone;
        }
        return PDF::loadView('email.invoice',[
            'data' => $data,
            'font_family' => "'Roboto','sans-serif'",
        ], [], [])->download('invoice-'.$package_payment->id.'.pdf');




    }
    public function bill_details(Request $request,$id)
    {

        $package_payment    = CustomerPackagePayment::findOrFail(decrypt($id));

        $package_details    = CustomerPackage::findOrFail($package_payment->customer_package_id);

        $user = auth()->user();

        return view("bills.bill_details",[
            "user_name" => $user->name,
            "payment_id" => $package_payment->id,
            "email" => $user->email,
            "mobile" => $user->phone,
            "package_name" => $package_details->name,
            "package_price" => $package_payment->amount,
            "date" => $package_payment->date_invoice,
            "invoice_id" => $package_payment->id,
            "qr_code" => $package_payment->qr_code,
            "vat_val" => $package_payment->vat_total,
            'user' => $user,
            'font_family' => "'Roboto','sans-serif'",
        ]);
    }

    public function myBill(Request $request, $cryptedId){
        $user = User::find(decrypt($cryptedId));
        if(empty($user)){
            abort(404);
        }
        auth()->login($user);
        return redirect('/my-bills');
    }
    public function myBills(Request $request){
        $user =  auth()->user();
        $customerPackagePayment = CustomerPackagePayment::where("user_id",$user->id)->where("approval",1)->get();

        return view('bills.index',compact("customerPackagePayment"));
    }
    public function getMyBillPageUrl(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "my_bill_url" => url('/my-bill',encrypt($user->id))
        ]);
    }
    //download invoice
    public function invoice_download($id)
    {
        if(Session::has('currency_code')){
            $currency_code = Session::get('currency_code');
        }
        else{
            $currency_code = Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        $language_code = Session::get('locale', Config::get('app.locale'));

        if(Language::where('code', $language_code)->first()->rtl == 1){
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }else{
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if($currency_code == 'BDT' || $language_code == 'bd'){
            // bengali font
            $font_family = "'Hind Siliguri','sans-serif'";
        }elseif($currency_code == 'KHR' || $language_code == 'kh'){
            // khmer font
            $font_family = "'Hanuman','sans-serif'";
        }elseif($currency_code == 'AMD'){
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
        // }elseif($currency_code == 'ILS'){
        //     // Israeli font
        //     $font_family = "'Varela Round','sans-serif'";
        }elseif($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'ar' || $currency_code == 'IQD' || $language_code == 'ir' || $language_code == 'om' || $currency_code == 'ROM' || $currency_code == 'SDG' || $currency_code == 'ILS'){
            // middle east/arabic/Israeli font
            $font_family = "'Baloo Bhaijaan 2','sans-serif'";
        }elseif($currency_code == 'THB'){
            // thai font
            $font_family = "'Kanit','sans-serif'";
        }else{
            // general for all
            $font_family = "'DejaVu Sans','Roboto','sans-serif'";
        }

        // $config = ['instanceConfigurator' => function($mpdf) {
        //     $mpdf->showImageErrors = true;
        // }];
        // mpdf config will be used in 4th params of loadview

        $config = [];

        $order = Order::findOrFail($id);

        Log::info( $order->user);
        $qrCode = Zatca::sellerName(get_setting("owner_company_name"))
            ->vatRegistrationNumber(get_setting("owner_vat_no"))
            ->timestamp($order->created_at)
            ->totalWithVat($order->grand_total)
            ->vatTotal($order->orderDetails->sum('tax'))
            ->toQrCode();


        if(!$order->seller()->exists()){
            return PDF::loadView('backend.invoices.customer_invoice',[
                'order' => $order,
                'font_family' => $font_family,
                'direction' => $direction,
                'text_align' => $text_align,
                'not_text_align' => $not_text_align,
                'buyerData' => $buyerVerificationData,
                'qrcode' => $qrCode
            ], [], $config)->stream('document.pdf');
        }else{

        return PDF::loadView('backend.invoices.invoice',[
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'buyerData' => $order->user,
            'qrcode' => $qrCode
        ], [], $config)->stream('document.pdf');
        }
    }


    public function pluckFromJson(array $array, $key)
    {
       $value = array_values(collect($array)->filter(function ($val) use ($key){
            return $val->label == $key;
        })->all())[0]->value ?? '';

       return strtoupper($value);
    }

    public function mapBuyerVerificationData($order)
    {
        if($order->seller()->exists() && $order->seller->user->seller->verification_info != null){

            $sellerVerification = json_decode($order->user->seller->verification_info);
            $buyerData['name'] = $this->pluckFromJson($sellerVerification, 'Your name');
            $buyerData['building_no'] = $this->pluckFromJson($sellerVerification, 'Building No');
            $buyerData['street_name'] = $this->pluckFromJson($sellerVerification, 'Street Name');
            $buyerData['district'] = $this->pluckFromJson($sellerVerification, 'District');
            $buyerData['city'] = $this->pluckFromJson($sellerVerification, 'City');
            $buyerData['country'] = $this->pluckFromJson($sellerVerification, 'Country');
            $buyerData['postal_code'] = $this->pluckFromJson($sellerVerification, 'Postal Code');
            $buyerData['vat_no'] = $this->pluckFromJson($sellerVerification, 'VAT No.');
            $buyerData['additional_no'] = $this->pluckFromJson($sellerVerification, 'Additional No');
            $buyerData['other_seller_id'] = $this->pluckFromJson($sellerVerification, 'Other Seller ID');

        }else{
            $buyerData = [];
        }
        return $buyerData;
    }
}
