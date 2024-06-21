<?php

namespace App\Http\Controllers;

use App\Models\CustomerPackagePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;
use Str;
use Log;

class CustomerOfflinePackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function offline_customer_package_payment(Request $request)
    {
        //return $request;
        //if (empty($request->user_id) || empty($request->package_id) || empty($request->payment_option) || empty($request->reciept)) {
        Log::info("offline_customer_package_payment" );
        Log::info($request->all());
        if (empty($request->reciept) || empty($request->fileName) || empty($request->user_id) || empty($request->package_id) || empty($request->payment_option) || empty($request->qr_code) || empty($request->date_qr)) {
            return response()->json([
                'message' => 'Received invalid arguments.',
                'messageAr' => '',
                "success" => false,
                'status'=>400,
                "exist" => false
            ], 200);
        }

        $customer_package = new CustomerPackagePayment;
        $customer_package->user_id = $request->user_id;
        $customer_package->customer_package_id = $request->package_id;
        $customer_package->payment_method = $request->payment_option;
        //$customer_package->payment_details = $request->trx_id;
        $customer_package->approval = 0;
        $customer_package->offline_payment = 1;

        $customer_package->vat_total = $request->vat_total;
        $customer_package->amount = $request->amount;
        $customer_package->qr_code = $request->qr_code;
        $customer_package->date_invoice = $request->date_qr;
        $fileNameParts = explode('.', $request->input("fileName"));
        $ext = end($fileNameParts);
// decode the base64 file
        $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$request->input('reciept')));
        // .jpg .png .pdf
        $imageName = Str::random(8).".".$ext ;
        file_put_contents(public_path().'/uploads/customer_package_payment_reciept/'.$imageName, $file);
        \Storage::disk('public')->put($imageName, $file); // works for uploading

        $customer_package->reciept = $imageName;
        $customer_package->save();

        return response()->json([
            'message' => 'Offline payment has been done. Please wait for approval.',
            'messageAr' => 'تم إجراء الدفع دون اتصال بالإنترنت. يرجى انتظار الموافقة.',
            "success" => true,
            'status'=>200,
            "exist" => false
        ], 200);
    }
}
