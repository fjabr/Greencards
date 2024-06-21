<?php

namespace App\Http\Controllers;

use App\Exports\CustomerPackagePaymentExport;
use App\Models\CustomerPackagePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;


class OffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function NotificationsMobile(){
        $notifications = DB::table("notifications_mobile")->orderBy('created_at', 'desc')->get();
        $customers = DB::table("users")
                    ->where("user_type", "customer")
                    ->select('id', "name")
                    ->get();


        // return ["notifications" => $notifications, "customers" => $customers];
        return view('backend.notification.all')->with(["notifications" => $notifications, "customers" => $customers]);
    }

    public function AddNotificationsMobile(){

        $customers = DB::table("users")
                    ->where("user_type", "customer")
                    ->select('id', "name")
                    ->get();

        // return ["notifications" => $notifications, "customers" => $customers];
        return view('backend.notification.create')->with(["customers" => $customers]);
    }

  public function saveNotificationsMobile(Request $request)
{
    $userIds = [];
    if ($request->input('for_all') == 0 && $request->filled('users')) {
        $userIds = $request->input('users');
    }

    if (get_setting('google_firebase') == 1) {
        // Retrieve device tokens for specified users
        $deviceTokens = User::whereIn('id', $userIds)->pluck('device_tokens');

        // Prepare the notification data
        $notificationData = new \stdClass();
        $notificationData->device_tokens = $deviceTokens;
        $notificationData->title = 'GreenCard Sa';
        $notificationData->text = $request->input('textNotif');

        // Send the notification
        NotificationUtility::sendFirebaseNotification($notificationData);
    }

    // Insert the notification record in the database
    DB::table('notifications_mobile')->insert([
        'text' => $request->input('textNotif'),
        'for_all' => $request->input('for_all'),
        'users' => $userIds ? implode(',', $userIds) : null,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    // Flash a success message and redirect
    flash(translate('Notification has been inserted successfully'))->success();
    return redirect()->route('notifications_mobile');
}



    public function live_chat($id){
        $subject = $id;
        $name_sub = "";
        switch ($id) {
            case 1:
                $name_sub = "SERVICE PROVIDER";
                break;
            case 2:
                $name_sub = "DIDN'T GET YOUR DISCOUNT";
                break;
            case 3:
                $name_sub = "PAYMENTS";
                break;
            case 4:
                $name_sub = "TRAVEL BOOKING";
                break;
            case 5:
                $name_sub = "ACCOUNT ISSUE";
                break;
            case 6:
                $name_sub = "OTHER";
                break;
        }
        return view('backend.chat.chat', compact('subject',"name_sub"));
    }

    public function index(Request $request)
    {
        $offers = DB::table('offers_types')->get();
        return view('backend.offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('backend.offers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::table('offers_types')->insert([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
        ]);

        flash(translate('Type offer has been inserted successfully'))->success();
        return redirect()->route('offers_types');
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
        $type = DB::table("offers_types")
                ->where("id", $id)
                ->first();

        if($type == null){
            return redirect()->route('offers_types');
        }

        return view('backend.offers.edit', compact('type'));
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
        $affected = DB::table('offers_types')
              ->where('id', $id)
              ->update([
                'name' => $request->name,
                'name_ar' => $request->name_ar,
              ]);
        flash(translate('Type offer has been updated successfully'))->success();
        return redirect()->route('offers_types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = DB::table("offers_types")
                    ->where("id", $id)
                    ->delete();
        return redirect()->route('offers_types');
    }

    public function contracts(Request $request)
    {
        $contracts = DB::table('seller_contract')
                    ->join("sellers", "seller_contract.seller_id", "=" , "sellers.id")
                    ->join("users", "sellers.user_id", "=", "users.id")
                    ->select("seller_contract.*", "users.name")
                    ->get();


        //return $contracts;
        return view('backend.contracts.index', compact('contracts'));
    }

    public function approve_contracts($id){
        $contract = DB::table('seller_contract')
              ->where('id', $id)->first();
        $affected = DB::table('seller_contract')
              ->where('id', $id)
              ->update(['status' => 2]);
        if($affected){
            DB::table('sellers')
              ->where('id', $contract->seller_id)
              ->update(['permission_add_offers' => 2]);
        }

        return redirect()->route("contracts");
    }

    public function refuse_contract(Request $req){
        $contract = DB::table('seller_contract')
              ->where('id', $req->id)->first();
        $affected = DB::table('seller_contract')
              ->where('id', $req->id)
              ->update(['status' => -1, "message" => $req->message]);
        if($affected){
            DB::table('sellers')
              ->where('id', $contract->seller_id)
              ->update(['permission_add_offers' => 1]);
        }

        return response()->json(['status'=>200, "success" => true]);

        // return redirect()->route("contracts");
    }

    public function export_invoices_mobile(Request $request)
    {
        $date_range = $request->input("date_range");
        $dates = $this->extractDates($date_range);


        return Excel::download(new CustomerPackagePaymentExport($request->search,$dates["start_date"], $dates["end_date"], 'Bank card'), 'CustomerPackagePayment.csv');
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

    public function invoices_mobile(Request $request)
    {
        $sort_search = null;
        $date_range = null;
        $invoices = CustomerPackagePayment::where("payment_method", "Bank card")
                    ->join("users", "customer_package_payments.user_id", "=" , "users.id")
                    ->join("customer_packages", "customer_package_payments.customer_package_id", "=", "customer_packages.id")
                    ->select("customer_package_payments.*", "customer_packages.name as packName", "users.name as userName", "users.email as userEmail");


        if ($request->has('search')){
            $sort_search = $request->search;
            $invoices->where(function ($q) use ($sort_search){
                $q->where('users.name', 'like', '%'.$sort_search.'%')
                ->orWhere('users.email', 'like', '%'.$sort_search.'%')
                ->orWhere('users.id', 'like', '%'.$sort_search.'%')
                ->orWhere('users.phone', 'like', '%'.$sort_search.'%');
            });
        }

        if($request->has("date_range")){
            $date_range = $request->input("date_range");
            $dates = $this->extractDates($date_range);
            $invoices->where(function ($q) use ($dates){
                $q->where('customer_package_payments.created_at', '>=', $dates['start_date'])
                ->where('customer_package_payments.created_at', '<=', $dates['end_date']);
            });
        }
        $invoices = $invoices->paginate(20);
        // return $invoices;
        return view('backend.invoices.list_invoices', compact('invoices', 'sort_search','date_range'));
    }

    public function downloadInvoice(CustomerPackagePayment $customerPackagePayment){

        $data = (object)[
            "user_name" => $customerPackagePayment->user->name,
            "email" => $customerPackagePayment->user->email,
            "package_name" => $customerPackagePayment->customer_package->name,
            "package_price" => $customerPackagePayment->amount,
            "date" => $customerPackagePayment->date_invoice,
            "invoice_id" => $customerPackagePayment->id,
            "qr_code" => $customerPackagePayment->qr_code,
            "vat_val" => $customerPackagePayment->vat_total
        ];

        if(empty($customerPackagePayment->user->email) || $customerPackagePayment->user->email == null){
            $data->email = $customerPackagePayment->user->phone;
        }

        return PDF::loadView('email.invoice',[
            'data' => $data,
            'font_family' => "'Roboto','sans-serif'",
        ], [], [])->download('invoice-'.$customerPackagePayment->id.'.pdf');
    }

}
