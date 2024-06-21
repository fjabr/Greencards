<?php

namespace App\Http\Controllers;

use App\Exports\CustomerPackagePaymentExport;
use App\Utility\EmailUtils;
use App\Utility\PdfUtils;
use Illuminate\Http\Request;
use App\Models\CustomerPackagePayment;
use App\Models\CustomerPackage;
use App\Models\User;
use App\Utility\SmsUtility;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Log;

class CustomerPackagePaymentController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_offline_customer_package_payments'])->only('offline_payment_request');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function export_offline_payment_request(Request $request)
    {
        $date_range = $request->input("date_range");
        $dates = $this->extractDates($date_range);
        return Excel::download(new CustomerPackagePaymentExport($request->search,$dates["start_date"], $dates["end_date"], null,1), 'CustomerPackagePayment.csv');
    }
    private function extractDates($dateString)
    {
        if($dateString == null || empty($dateString )) {
            return [
                'start_date' => null,
                'end_date' => null
            ];
        };
        // Split the string based on the hyphen delimiter
        $dates = explode('-', $dateString);

        // Trim whitespace from the start date and end date
        $startDate = trim($dates[0]);
        $endDate = trim($dates[1]);

        // Parse the dates using Carbon
        $carbonStartDate = Carbon::createFromFormat('m/d/Y', $startDate);
        $carbonEndDate = Carbon::createFromFormat('m/d/Y', $endDate);

        // Format the dates as desired
        $formattedStartDate = $carbonStartDate->format('Y-m-d');
        $formattedEndDate = $carbonEndDate->format('Y-m-d');

        return [
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate
        ];
    }

    public function offline_payment_request(Request $request){

        $sort_search = null;
        $date_range = null;

        $package_payment_requests = CustomerPackagePayment::where('offline_payment',1)
        ->join("users",'users.id',"customer_package_payments.user_id")
            ->select("customer_package_payments.*")
            ->orderBy('customer_package_payments.id', 'desc');

        if ($request->has('search')){
            $sort_search = $request->search;
            $package_payment_requests->where(function ($q) use ($sort_search){
                $q->where('users.name', 'like', '%'.$sort_search.'%')
                ->orWhere('users.email', 'like', '%'.$sort_search.'%')
                ->orWhere('users.id', 'like', '%'.$sort_search.'%')
                ->orWhere('users.phone', 'like', '%'.$sort_search.'%');
            });
        }

        if($request->has("date_range")){
            $date_range = $request->input("date_range");
            if($date_range !== null ) {
                $dates = $this->extractDates($date_range);
                $package_payment_requests->where(function ($q) use ($dates){
                    $q->where('customer_package_payments.created_at', '>=', $dates['start_date'])
                    ->where('customer_package_payments.created_at', '<=', $dates['end_date']);
                });
            }
        }


        $package_payment_requests = $package_payment_requests->paginate(20);
        return view('manual_payment_methods.customer_package_payment_request', compact('package_payment_requests','sort_search','date_range'));
    }

    public function offline_payment_approval(Request $request)
    {
        $package_payment    = CustomerPackagePayment::findOrFail($request->id);
        $package_details    = CustomerPackage::findOrFail($package_payment->customer_package_id);
        $package_payment->approval      = $request->status;
        if($package_payment->save()){
            $user                       = $package_payment->user;
            $user->customer_package_id  = $package_payment->customer_package_id;
            $user->start_sub_date = !empty($user->start_sub_date) && $user->start_sub_date!= null ? new Carbon($user->start_sub_date) : new Carbon();
            $user->end_sub_date = (!empty($user->end_sub_date) && $user->end_sub_date !=null ?new Carbon($user->end_sub_date) : Carbon::now() )->addDays($package_details->duration);
            $user->nb_members = $package_details->nb_members-1;
            $user->remaining_uploads    = $user->remaining_uploads + $package_details->product_upload;
            $user->duration    = $user->duration + $package_details->duration;
            $user->save();

            $data = [
                "user_name" => $user->name,
                "email" => $user->email??$user->phone,
                "package_name" => $package_details->name,
                "package_price" => $package_payment->amount,
                "date" => $package_payment->date_invoice,
                "invoice_id" => $package_payment->id,
                "qr_code" => $package_payment->qr_code,
                "vat_val" => $package_payment->vat_total
            ];
            $to = $user->email;
            $pdf = PdfUtils::generate('email.invoice', $data);
            if(empty($user->phone)){
                SmsUtility::subscripiton_payment_suceess($user);
            }

            $attachment = null;
            if ($pdf) $attachment = ['name' => 'invoice.pdf', 'file' => $pdf];

            // User Email
            EmailUtils::sendMail($to, 'Invoice Green Card SA', $data, $attachment);

            // Admin Email
            EmailUtils::sendMail('dev@greencard-sa.com', 'Admin Notification - Invoice Green Card SA', $data, $attachment);

            return 1;
        }
        return 0;
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
}
