<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CommissionHistory;
use App\Models\CustomerPackage;
use App\Models\CustomerPackagePayment;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Search;
use App\Models\Seller;
use App\Models\SellerSubscriptionReport;
use App\Models\Shop;
use Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:in_house_product_sale_report'])->only('in_house_sale_report');
        $this->middleware(['permission:seller_products_sale_report'])->only('seller_sale_report');
        $this->middleware(['permission:products_stock_report'])->only('stock_report');
        $this->middleware(['permission:product_wishlist_report'])->only('wish_report');
        $this->middleware(['permission:user_search_report'])->only('user_search_report');
        $this->middleware(['permission:commission_history_report'])->only('commission_history');
        $this->middleware(['permission:wallet_transaction_report'])->only('wallet_transaction_history');
    }

    public function sales_reports(Request $request)
    {


        $packagePaymentRequests = CustomerPackagePayment::orderBy('id', 'desc');
        if( $request->has("payment_type") && !empty($request->input("payment_type"))){
            $packagePaymentRequests = $packagePaymentRequests->where('offline_payment', $request->input("payment_type"));
        }
        if( $request->has("package") && !empty($request->input("package"))){
            $packagePaymentRequests = $packagePaymentRequests->where('customer_package_payments.customer_package_id', $request->input("package"));
        }
        if( $request->has("agent") && !empty($request->input("agent"))){

            $packagePaymentRequests = $packagePaymentRequests
                ->join("users", "users.id", "customer_package_payments.user_id")
                ->join("customers", "customers.user_id", "users.id")
                ->where("customers.created_by",$request->input("agent"))
                ->select("customer_package_payments.*");
        }

        if( ($request->has("approval") && !empty($request->input("approval"))) || $request->input("approval") == "0"){
            $packagePaymentRequests = $packagePaymentRequests->where('customer_package_payments.approval', intval($request->input("approval")));
        }
        $packagePaymentRequests =  $packagePaymentRequests->paginate(10);
        $packagePaymentRequests->appends(['agent' => $request->input("agent")]);
        $packagePaymentRequests->appends(['payment_type' => $request->input("payment_type")]);
        $packagePaymentRequests->appends(['approval' => $request->input("approval")]);
        $packagePaymentRequests->appends(['package' => $request->input("package")]);
        $agents = User::join("staff", "staff.user_id", "users.id")
            ->select("users.*")
            ->get();
        $packages = CustomerPackage::all();
        $page = intval($request->input("page"));
        if($page <= 0){
            $index= 1;
        }else {
            $index = ($page-1)*10+1;
        }
        return view('backend.reports.subscription_sale_report', compact('agents','packagePaymentRequests','index','packages'));
    }

    public function stock_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.stock_report', compact('products','sort_by'));
    }

    public function in_house_sale_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.in_house_sale_report', compact('products','sort_by'));
    }

    public function seller_sale_report(Request $request)
    {
        $sort_by =null;
        // $sellers = User::where('user_type', 'seller')->orderBy('created_at', 'desc');
        $sellers = Shop::with('user')->orderBy('created_at', 'desc');
        if ($request->has('verification_status')){
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('verification_status', $sort_by);
        }
        $sellers = $sellers->paginate(10);
        return view('backend.reports.seller_sale_report', compact('sellers','sort_by'));
    }

    public function wish_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(10);
        return view('backend.reports.wish_report', compact('products','sort_by'));
    }

    public function user_search_report(Request $request){
        $searches = Search::orderBy('count', 'desc')->paginate(10);
        return $searches;
        return view('backend.reports.user_search_report', compact('searches'));
    }

    public function commission_history(Request $request) {
        $seller_id = null;
        $date_range = null;

        if(Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        } if($request->seller_id) {
            $seller_id = $request->seller_id;
        }

        $commission_history = CommissionHistory::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id){

            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }

        $commission_history = $commission_history->paginate(10);
        if(Auth::user()->user_type == 'seller') {
            return view('seller.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
        }
        return view('backend.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
    }

    public function wallet_transaction_history(Request $request) {
        $user_id = null;
        $date_range = null;

        if($request->user_id) {
            $user_id = $request->user_id;
        }

        $users_with_wallet = User::whereIn('id', function($query) {
            $query->select('user_id')->from(with(new Wallet)->getTable());
        })->get();

        $wallet_history = Wallet::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $wallet_history = $wallet_history->where('created_at', '>=', $date_range1[0]);
            $wallet_history = $wallet_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($user_id){
            $wallet_history = $wallet_history->where('user_id', '=', $user_id);
        }

        $wallets = $wallet_history->paginate(10);

        return view('backend.reports.wallet_history_report', compact('wallets', 'users_with_wallet', 'user_id', 'date_range'));
    }


    public function seller_subscription_report(Request $request)
    {;
        $sort_search = null;
        $date_range = null;
        $sellerSubscriptions = SellerSubscriptionReport::join("users",'users.id',"seller_subscription_reports.user_id")
        ->select('seller_subscription_reports.*')
        ->orderBy('created_at', 'desc');


        if ($request->has('search')){
            $sort_search = $request->search;
            $sellerSubscriptions->where(function ($q) use ($sort_search){
                $q->where('users.name', 'like', '%'.$sort_search.'%')
                ->orWhere('users.email', 'like', '%'.$sort_search.'%')
                ->orWhere('users.id', 'like', '%'.$sort_search.'%')
                ->orWhere('users.phone', 'like', '%'.$sort_search.'%');
            });
        }

        if($request->filled("date_range")){
            $date_range = $request->input("date_range");
            $dates = $this->extractDates($date_range);
            $sellerSubscriptions->where(function ($q) use ($dates){
                $q->where('seller_subscription_reports.created_at', '>=', $dates['start_date'])
                ->where('seller_subscription_reports.created_at', '<=', $dates['end_date']);
            });
        }

        $sellerSubscriptions = $sellerSubscriptions->paginate(10);
        foreach ($sellerSubscriptions as $sellerSubscription) {
            $shop = null;
            if($sellerSubscription->source === 'branch'){
                $shop = Branch::where('id', $sellerSubscription->shop_id)->first();
                $shop->city = $shop->branchCity;
            }else{
                $shop = Shop::where('id', $sellerSubscription->shop_id)->first();
                $shop->city = $shop->shopCity;
            }
            $sellerSubscription->shop = $shop;
        }


        return view('backend.reports.seller_subscription_report', compact('sellerSubscriptions','sort_search','date_range'));
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
        $dates = explode('/', $dateString);

        // Trim whitespace from the start date and end date
        $formattedStartDate = trim($dates[0]);
        $formattedEndDate = trim($dates[1]);

        return [
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate
        ];
    }
}
