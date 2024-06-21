<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\ProductStock;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */

  /*************contrat functions ****/

    public function contract(Request $req){


        // return $req;


        $seller_id = DB::table("sellers")->where("user_id" ,"=" ,Auth::user()->id)->first();

        if($seller_id->id == null){
            return redirect("/offers");
        }

        $msg = [];
        $contract = [
              "date_create" => "",
              "company_name" => "",
              "company_name_ar" => "",
              "comm_reg_no" => "",
              "vat_reg" => "",
              "opr_as" => "",
              "opr_as_ar" => "",
              "contact_person" => "",
              "contact_person_ar" => "",
              "email" => "",
              "phone" => "",
              "offer_price" => "",
              "average_price" => "",
              "offers" => "",
              "types_offers" => "",
              "type_offer_discount" => "",
              "activation_peroid_from" => "",
              "activation_peroid_expr" => "",
              "fees" => "",
              "entry_fee" => "",
              "commission" => "",
              "job_title" => "",
              "currency" => "SAR",
              "seller_id" => $seller_id->id
        ];

        if($req->has("date_create") || $req->has("date_create_ar")){
            if($req->date_create != null){
                $contract["date_create"] = $req->date_create;
            }
            if($req->date_create_ar != null){
                $contract["date_create"] = $req->date_create_ar;
            }

            if($req->date_create == null && $req->date_create_ar == null){
                array_push($msg, "Date required");
            }

        }else{
            array_push($msg, "Date required");
        }

        if($req->has("company_name") || $req->has("company_name_ar")){
            if($req->company_name != null){
                $contract["company_name"] = $req->company_name;
            }
            if($req->company_name_ar != null){
                $contract["company_name_ar"] = $req->company_name_ar;
            }

            if($req->company_name == null && $req->company_name_ar == null){
                array_push($msg, "Company name required");
            }

        }else{
            array_push($msg, "Company name required");
        }

        if($req->has("comm_reg_no") || $req->has("comm_reg_no_ar")){
            if($req->comm_reg_no != null){
                $contract["comm_reg_no"] = $req->comm_reg_no;
            }
            if($req->comm_reg_no_ar != null){
                $contract["comm_reg_no"] = $req->comm_reg_no_ar;
            }

            if($req->comm_reg_no == null && $req->comm_reg_no_ar == null){
                array_push($msg, "under Commercial Registration No, is required");
            }

        }else{
            array_push($msg, "under Commercial Registration No, is required");
        }

        if($req->has("vat_reg") || $req->has("vat_reg_ar")){
            if($req->vat_reg != null){
                $contract["vat_reg"] = $req->vat_reg;
            }
            if($req->comm_reg_no_ar != null){
                $contract["vat_reg"] = $req->vat_reg_ar;
            }

            if($req->vat_reg == null && $req->vat_reg_ar == null){
                array_push($msg, "VAT registration number is required");
            }

        }else{
            array_push($msg, "VAT registration number is required");
        }

        if($req->has("opr_as") || $req->has("opr_as_ar")){
            if($req->opr_as != null){
                $contract["opr_as"] = $req->opr_as;
            }
            if($req->opr_as_ar != null){
                $contract["opr_as_ar"] = $req->opr_as_ar;
            }

            if($req->opr_as == null && $req->opr_as_ar == null){
                array_push($msg, "It operates as required");
            }

        }else{
            array_push($msg, "It operates as required");
        }

        if($req->has("contact_person") || $req->has("contact_person_ar")){
            if($req->contact_person != null){
                $contract["contact_person"] = $req->contact_person;
            }
            if($req->contact_person_ar != null){
                $contract["contact_person_ar"] = $req->contact_person_ar;
            }

            if($req->contact_person == null && $req->contact_person_ar == null){
                array_push($msg, "Contact person required");
            }

        }else{
            array_push($msg, "Contact person required");
        }

        if($req->has("email") || $req->has("email_ar")){
            if($req->email != null){
                $contract["email"] = $req->email;
            }
            if($req->email_ar != null){
                $contract["email"] = $req->email_ar;
            }

            if($req->email == null && $req->email_ar == null){
                array_push($msg, "Email required");
            }

        }else{
            array_push($msg, "Email required");
        }

        if($req->has("phone") || $req->has("email_ar")){
            if($req->phone != null){
                $contract["phone"] = $req->phone;
            }
            if($req->phone_ar != null){
                $contract["phone"] = $req->phone_ar;
            }

            if($req->phone == null && $req->phone_ar == null){
                array_push($msg, "Phone required");
            }

        }else{
            array_push($msg, "Phone required");
        }

        if($req->has("offer_price") || $req->has("offer_price_ar")){
            if($req->offer_price != null){
                $contract["offer_price"] = $req->offer_price;
            }
            if($req->offer_price_ar != null){
                $contract["offer_price"] = $req->offer_price_ar;
            }

            if($req->offer_price == null && $req->offer_price_ar == null){
                array_push($msg, "Offer price required");
            }

        }else{
            array_push($msg, "Offer price required");
        }

        if($req->has("average_price") || $req->has("average_price_ar")){
            if($req->average_price != null){
                $contract["average_price"] = $req->average_price;
            }
            if($req->average_price_ar != null){
                $contract["average_price"] = $req->average_price_ar;
            }

            if($req->average_price == null && $req->average_price_ar == null){
                array_push($msg, "Average price Redemption amount required");
            }

        }else{
            array_push($msg, "Average price Redemption amount required");
        }

        if($req->has("offers") || $req->has("offers_ar")){
            if($req->offers != null){
                $contract["offers"] = implode(",", $req->offers);
            }
            if($req->offers_ar != null){
                $contract["offers"] = implode(",", $req->offers_ar);
            }

            if($req->offers == null && $req->offers_ar == null){
                array_push($msg, "Offers required");
            }

        }else{
            array_push($msg, "Offers required");
        }

        if($req->has("types_offers") || $req->has("types_offers_ar")){
            if($req->types_offers != null){
                $contract["types_offers"] = implode(",", $req->types_offers);
            }
            if($req->offers_ar != null){
                $contract["types_offers"] = implode(",", $req->types_offers_ar);
            }

        }

        // Offer type Discount rate
        if($req->has("type_offer_discount") || $req->has("type_offer_discount_ar")){
            if($req->type_offer_discount != null){
                $contract["type_offer_discount"] = $req->type_offer_discount;
            }
            if($req->type_offer_discount_ar != null){
                $contract["type_offer_discount"] = $req->type_offer_discount_ar;
            }

            if($req->type_offer_discount == null && $req->type_offer_discount_ar == null){
                array_push($msg, "Offer type Discount rate is required");
            }

        }else{
            array_push($msg, "Offer type Discount rate is required");
        }

        if($req->has("activation_peroid_from") || $req->has("activation_peroid_from_ar")){
            if($req->activation_peroid_from != null){
                $contract["activation_peroid_from"] = $req->activation_peroid_from;
            }
            if($req->activation_peroid_from_ar != null){
                $contract["activation_peroid_from"] = $req->activation_peroid_from_ar;
            }

            if($req->activation_peroid_from == null && $req->activation_peroid_from_ar == null){
                array_push($msg, "Activation period starts from, is required");
            }

        }else{
            array_push($msg, "Activation period starts from, is required");
        }

        if($req->has("activation_peroid_expr") || $req->has("activation_peroid_expr_ar")){
            if($req->activation_peroid_expr != null){
                $contract["activation_peroid_expr"] = $req->activation_peroid_expr;
            }
            if($req->activation_peroid_expr_ar != null){
                $contract["activation_peroid_expr"] = $req->activation_peroid_expr_ar;
            }

            if($req->activation_peroid_expr == null && $req->activation_peroid_expr_ar == null){
                array_push($msg, "Expiry date required");
            }

        }else{
            array_push($msg, "Expiry date required");
        }

        if($req->has("fees") || $req->has("fees_ar")){
            if($req->fees != null){
                $contract["fees"] = $req->fees;
            }
            if($req->fees_ar != null){
                $contract["fees"] = $req->fees_ar;
            }

            if($req->fees == null && $req->fees_ar == null){
                array_push($msg, "Fees required");
            }

        }else{
            array_push($msg, "Fees required");
        }

        if($req->has("entry_fee") || $req->has("entry_fee_ar")){
            if($req->entry_fee != null){
                $contract["entry_fee"] = $req->entry_fee;
            }
            if($req->entry_fee_ar != null){
                $contract["entry_fee"] = $req->entry_fee_ar;
            }

            if($req->entry_fee == null && $req->entry_fee_ar == null){
                array_push($msg, "Entry Fee required");
            }

        }else{
            array_push($msg, "Entry Fee required");
        }

        if($req->has("commission") || $req->has("commission_ar")){
            if($req->commission != null){
                $contract["commission"] = $req->commission;
            }
            if($req->commission_ar != null){
                $contract["commission"] = $req->commission_ar;
            }

            if($req->commission == null && $req->commission_ar == null){
                array_push($msg, "Commission required");
            }

        }else{
            array_push($msg, "Commission required");
        }

        if($req->has("job_title") || $req->has("job_title_ar")){
            if($req->job_title != null){
                $contract["job_title"] = $req->job_title;
            }
            if($req->commission_ar != null){
                $contract["job_title"] = $req->job_title_ar;
            }

            if($req->job_title == null && $req->job_title_ar == null){
                array_push($msg, "Job title required");
            }

        }else{
            array_push($msg, "Job title required");
        }

        if(count($msg) > 0){
            $user_id = Auth::user()->id;
            $shop = DB::table("shops")
                    ->where("shops.user_id", $user_id)
                    ->leftJoin("uploads", "shops.logo", "=", "uploads.id")
                    ->select("shops.*", "uploads.file_name as logo_url")
                    ->first();
            $seller = DB::table("sellers")
                        ->where("user_id", $user_id)->first();
            $types_offers = DB::table("offers_types")->get();
            // return $types_offers;
            if($shop !== null && $seller !== null){
                Log::info($shop);
                $offers = DB::table("offers")->where("id_shop", $shop->id)->get();
                $name_shop = str_replace(" ", "-", $shop->name);
                $shop_name = $name_shop."-".$shop->id;
                return view('seller.offers.offers')->with([
                    "offers" => $offers, "shop_name" => $shop_name,
                    "shop" => $shop, "seller" => $seller,
                    "types_offers" => $types_offers, "errors_contract" => $msg]);
            }
            return redirect("/dahboard");
        }

        // $req['msg'] = $msg;
        // return $contract;
        $contract_id = DB::table('seller_contract')->insertGetId(
                            [
                                'seller_id' => $contract["seller_id"],
                                'create_date' => $contract["date_create"],
                                'company_name' => $contract["company_name"],
                                'company_name_ar' => $contract["company_name_ar"],
                                'comm_reg_no' => $contract["comm_reg_no"],
                                'vat_no' => $contract["vat_reg"],
                                'operates_as' => $contract["opr_as"],
                                'operates_as_ar' => $contract["opr_as_ar"],
                                'contact_persons' => $contract["contact_person"],
                                'contact_person_ar' => $contract["contact_person_ar"],
                                'email_company' => $contract["email"],
                                'phone_company' => $contract["phone"],
                                'offers' => $contract["offers"],
                                'price_offer' => $contract["offer_price"],
                                'average_price_amount' => $contract["average_price"],
                                'prices_offers' => $contract["type_offer_discount"],
                                'type_offer' => $contract["types_offers"],
                                'date_start' => $contract["activation_peroid_from"],
                                'date_exp' => $contract["activation_peroid_expr"],
                                'fees' => $contract["fees"],
                                'entry_fee' => $contract["entry_fee"],
                                'currency' => $contract["currency"],
                                'commission' => $contract["commission"],
                                'job_name' => $contract["job_title"],
                            ]
                        );
        if($contract_id !== null){
            $affected = DB::table('sellers')
              ->where('id', $contract["seller_id"])
              ->update(['permission_add_offers' => 1]);


        }

        return redirect("/offers");
    }

    public function saveContract(Request $req){


        $validator = $req->validate([
            'contract_file' => 'required|mimes:pdf',
        ]);

        $path = $req->file('contract_file')->store('contracts');

         $seller = DB::table("sellers")->where("user_id" ,"=" ,Auth::user()->id)->first();

        if($path != null && $seller != null){
            $affected = DB::table('seller_contract')
              ->where('seller_id', $seller->id)
              ->update(['status' => 1, "file_url" => $path]);
        }

        return redirect("/offers");
    }

    public function init_contract($id){

        $contract = DB::table('seller_contract')
              ->where('id', $id)->first();
        $affected = DB::table('sellers')
              ->where('id', $contract->seller_id)
              ->update(['permission_add_offers' => 0]);
        if($affected){
            DB::table('seller_contract')->where('id', $id)->delete();
        }

        return redirect("/seller/offers");
    }

    public function offers(){


        if(Auth::user()->user_type == 'seller'){

            $user_id = Auth::user()->id;


            $shop = DB::table("shops")
                    ->where("shops.user_id", $user_id)
                    ->leftJoin("uploads", "shops.logo", "=", "uploads.id")
                    ->select("shops.*", "uploads.file_name as logo_url")
                    ->first();

            $seller = DB::table("sellers")
                        ->where("user_id", $user_id)->first();





            $types_offers = DB::table("offers_types")->get();


            //  dd($types_offers);
            // return $types_offers;
            if($shop !== null && $seller !== null){

                Log::info("test".$shop->name);
                $offers = DB::table("offers")->where("id_shop", $shop->id)->get();

                $name_shop = str_replace(" ", "-", $shop->name);

                $shop_name = $name_shop."-".$shop->id;
                $shop_name = urlencode($shop_name);
                $contract = null;

                // dd($seller->permission_add_offers);


                 if($seller->permission_add_offers == 0){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();

                }

                if($seller->permission_add_offers == 1){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();


                }

                if($seller->permission_add_offers == 2){
                    $contract = DB::table("seller_contract")->where("seller_id", $seller->id)->first();
                }

                // dd($offers,$shop_name ,$shop ,$seller,$types_offers, $contract);
                return view('seller.offers.offers')->with([
                    "offers" => $offers, "shop_name" => $shop_name,
                    "shop" => $shop, "seller" => $seller,
                    "types_offers" => $types_offers, "contract" =>  $contract
                ]);
            }
            return redirect("/dahboard");
        }
        else {
            abort(404);
        }
    }



}
