<?php

namespace App\Http\Controllers;

use App\Exports\CustomerListExport;
use App\Exports\CustomerPackagePaymentExport;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPackagePayment;
use App\Models\User;
use App\Models\Order;
use DB;
use Excel;
use Exception;

class CustomerController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_customers'])->only('index');
        $this->middleware(['permission:login_as_customer'])->only('login');
        $this->middleware(['permission:ban_customer'])->only('ban');
        $this->middleware(['permission:delete_customer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $users = User::where('user_type', 'customer')->orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')
                ->orWhere('email', 'like', '%'.$sort_search.'%')
                ->orWhere('id', 'like', '%'.$sort_search.'%')
                ->orWhere('phone', 'like', '%'.$sort_search.'%');
            });
        }
        $users = $users->paginate(15);
        return view('backend.customer.customers.index', compact('users', 'sort_search'));
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
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|unique:users|email',
            'phone'         => 'required|unique:users',
        ]);

        $response['status'] = 'Error';

        $user = User::create($request->all());

        $customer = new Customer;

        $customer->user_id = $user->id;
        $customer->save();

        if (isset($user->id)) {
            $html = '';
            $html .= '<option value="">
                        '. translate("Walk In Customer") .'
                    </option>';
            foreach(Customer::all() as $key => $customer){
                if ($customer->user) {
                    $html .= '<option value="'.$customer->user->id.'" data-contact="'.$customer->user->email.'">
                                '.$customer->user->name.'
                            </option>';
                }
            }

            $response['status'] = 'Success';
            $response['html'] = $html;
        }

        echo json_encode($response);
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
        $user = User::findOrFail(decrypt($id));
        return view("backend.customer.customers.edit",compact("user"));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        DB::beginTransaction();
        try{
            if($request->has("email") && !empty($request->input("email")) ){
                $tmpUser = User::where("email",$request->input("email"))
                    ->where("id",'<>',$user->id)
                    ->first();
                if(!empty($tmpUser)){
                    throw new Exception(translate("Email already existed"),99);
                }else {
                    $user->email = $request->input("email");
                }
            }

            if($request->has("phone") && !empty($request->input("phone")) ){
                $tmpUser = User::where("phone",$request->input("phone"))
                    ->where("id",'<>',$user->id)
                    ->first();
                if(!empty($tmpUser) ){
                    throw new Exception(translate("Phone already existed"),99);
                }else{
                    $user->phone = $request->input("phone");
                }
            }

            if($request->has("name") && !empty($request->input("name")) ){
                $user->name = $request->input("name");
            }
            $user->save();

            flash(translate("User Updated successfully"))->success();
            DB::commit();
            return redirect()->route('customers.index');


        }catch(Exception $ex){
            if($ex->getCode() == 99){
                flash($ex->getMessage())->error();
            }else{
                flash(translate("An Error has Accured"))->error();
            }
            DB::rollBack();
            return back();
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        $user->customer_products()->delete();
        $user->delete();
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('customers.index');
    }

    public function bulk_customer_delete(Request $request) {
        if($request->id) {
            foreach ($request->id as $customer_id) {
                $customer = User::findOrFail($customer_id);
                $customer->customer_products()->delete();
                $this->destroy($customer_id);
            }
        }

        return 1;
    }

    public function login($id)
    {
        $user = User::findOrFail(decrypt($id));

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }
    public function resendInvoice( $id)
    {
        $customerPackagePayment = CustomerPackagePayment::findOrFail(decrypt($id));

        sendInvoiceByEmail($customerPackagePayment);

        return back();
    }

    public function ban($id) {
        $user = User::findOrFail(decrypt($id));

        if($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $user->save();

        return back();
    }

    function export_customers_request(Request $request) {
        return Excel::download(new CustomerListExport($request->search), 'CustomersList.csv');
    }
}
