<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClaimCouponRequest;
use App\Models\Address;
use App\Models\Branch;
use App\Models\Cart;
use App\Models\City;
use App\Models\Coupon;
use App\Models\User;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Category;
use App\Models\ManualPaymentMethod;
use App\Models\Offer;
use App\Models\Shop;
use App\Notifications\AppEmailVerificationNotification;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Exception;
use Log;
use PDF;

use function PHPSTORM_META\map;

class MobileController extends Controller
{

    public function getOfflinePayment(Request $request)
    {
        $manualPaymentMethod = ManualPaymentMethod::where("type", 'mobile_app_offline_payment')->first();
        if (empty($manualPaymentMethod)) {
            return response()->json([
                'status' => 1,
                'data' => "Couldn't find any payment"
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $manualPaymentMethod
        ]);
    }
    public function getInvoice($src)
    {

        $url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . $src . "&choe=UTF-8";
        $img = file_get_contents($url);
        return response($img)->header('Content-type', 'image/png');
        // $now = Carbon::now();

        // //return "hi";

        // $sellerNameBuf = $this->getTLVForValue("1", "salah hospital");
        // $vatRegistrationNameBuf = $this->getTLVForValue("2", "31234567890123");
        // $timeStampBuf = $this->getTLVForValue("3", $now);
        // $taxTotalNameBuf = $this->getTLVForValue("4", "1000.00");
        // $taxTotalBuf = $this-> getTLVForValue("6", "150.00");

        // $tagsBufsArray = $sellerNameBuf.$vatRegistrationNameBuf.$timeStampBuf.$taxTotalNameBuf.$taxTotalBuf;

        // return base64_encode($tagsBufsArray);
    }

    private function getTLVForValue($tag, $val)
    {
        $tagBuf = $this->strigToBinary($tag);
        $tagValueLenBuf = $this->strigToBinary(strlen($val));
        $tagValueBuf = $this->strigToBinary($val);


        return $tagBuf . "" . $tagValueLenBuf . "" . $tagValueBuf;
    }

    private function strigToBinary($string)
    {
        $characters = str_split($string);

        $binary = [];
        foreach ($characters as $character) {
            $data = unpack('H*', $character);
            $binary[] = "0" . base_convert($data[1], 16, 2);
        }

        return implode('', $binary);
    }

    public function getCategories($package_id)
    {
        $categories = DB::table('categories')
            ->where('categories.parent_id', 0)
            ->where("is_ecom_category",0)
            // ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
            // ->where('category_translations.lang', "en")
            // ->orWhere('category_translations.lang', 'ar')
            ->leftJoin('uploads', 'categories.banner', '=', 'uploads.id')
            ->select('categories.*', "uploads.file_name as banner_url", 'uploads.external_link as external_link')
            ->groupby('categories.id')
            ->get();
        $data = [];
        foreach ($categories as $key => $category) {
            $cat_en = DB::table('category_translations')->where("category_id", "=", $category->id)
                ->where("lang", "=", "en")->select('category_translations.name as enName')->first();
            $cat_ar = DB::table('category_translations')->where("category_id", "=", $category->id)
                ->where("lang", "=", "ar")->select('category_translations.name as arName')->first();

            // return [$cat_en , $cat_ar];
            if ($cat_en != null) {
                $category->enName = $cat_en->enName;
            } else {
                $category->enName = null;
            }

            if ($cat_ar != null) {
                $category->arName = $cat_ar->arName;
            } else {
                $category->arName = null;
            }


            array_push($data, $category);
        }

        if ($package_id == 0) {
            return response()->json(['status' => 200, 'total_results' => count($data), 'categories' => $data]);
        }
        $categories_valid = [];
        foreach ($categories as $key => $category) {
            $packages = explode(",", $category->packages_allowed);
            if (in_array($package_id, $packages)) {
                array_push($categories_valid, $category);
            }
        }

        return response()->json(['status' => 200, 'total_results' => count($categories_valid), 'categories' => $categories_valid]);
    }

    public function getCountries()
    {
        $countries = DB::table('countries')
            ->get();

        return response()->json(['status' => 200, "success" => true, 'total_results' => count($countries), 'countries' => $countries]);
    }

    public function getPackages()
    {
        $packages = DB::table('customer_packages')
            ->join('customer_package_translations', 'customer_packages.id', '=', 'customer_package_translations.customer_package_id')
            ->where('customer_package_translations.lang', "en")
            ->where('customer_packages.hidden', 0)
            ->select('customer_packages.*', 'customer_package_translations.name as label')
            ->orderBy('customer_packages.id', 'desc')
            ->get();

        return response()->json(['status' => 200, 'total_results' => count($packages), 'data' => $packages]);
    }

    public function login(Request $req)
    {
        $validator = $req->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'personal_fire_base_token' => 'string',
        ]);

        Log::info("fire base token : " . $req->input('personal_fire_base_token'));
        $user = User::where('email', $req->email)
            ->where('user_type', "customer")
            ->where("end_sub_date",">", Carbon::now())
            ->first();

        if ($user != null) {
            $token = $user->createToken('API Token')->plainTextToken;
            if (Hash::check($req->password, $user->password)) {
                Log::info("saving personal_fire_base_token " . $req->personal_fire_base_token);
                $user->personal_fire_base_token = $req->input("personal_fire_base_token");
                $user->save();
                return response()->json([
                    'message' => "login successfully",
                    "success" => true,
                    "user" => $user,
                    'access_token' => $token
                ]);
            } else {
                return response()->json([
                    'message' => "invalid email or password",
                    'messageAr' => "البريد الإلكتروني أو كلمة السر خاطئة",
                    "success" => false,
                    "user" => $user
                ]);
            }
        } else {
            return response()->json([
                'message' => "invalid email",
                'messageAr' => "بريد إلكتروني خاطئ",
                "success" => false,
                "user" => null
            ]);
        }
    }

    public function signupWithPhoneNumber(Request $req)
    {
        $req->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile' => 'required',
            'gender' => 'string',
            'password' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $user = User::where('phone', $req->mobile)->first();
            if (!empty($user)) {
                return response()->json([
                    'message' => "Registration Failed. Phone already exist!.",
                    'messageAr' => "فشل في التسجيل. رقم الهاتف موجود مسبقا",
                    "error" => "",
                    "success" => false
                ], 201);
            }

            $user = new User();
            $user->name = $req->first_name . " " . $req->last_name;
            $user->user_type = "customer";
            $user->phone = $req->mobile;
            $user->gender = $req->gender;
            $user->date_birth = $req->date_birth;
            $user->nationality = $req->nationality;
            $user->country = $req->country;
            $user->password = Hash::make($req->password);
            $user->verification_code = rand(1000, 9999);
            $user->register_by = "phone";

            if ($req->has("email") && !empty($req->input("email"))) {
                $users = User::where('email', $req->input("email"))->get();
                if (count($users) > 0) {
                    return response()->json([
                        'message' => "Registration Failed. Email already exist!.",
                        'messageAr' => "فشل في التسجيل. رقم الهاتف موجود مسبقا",
                        "error" => "",
                        "success" => false
                    ], 201);
                }
                $user->email = $req->input("email");
                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
            SmsUtility::sale_new_sub($user, $req->password);
            $otpController = new OTPVerificationController();
            if (!$otpController->send_code($user)) {
                throw new Exception(
                    translate("Unable to send OTP SMS, please provide a valid phone number"),
                    90
                );
            }
            $user->save();
            DB::commit();
            return response()->json([
                'message' => 'Registration Successful. Please verify and log in to your account.',
                'messageAr' => "تم التسجيل بنجاح. يرجى التحقق وتسجيل الدخول إلى حسابك",
                "success" => true,
                "user" => $user,
            ], 201);
        } catch (Exception $ex) {
            $message = "Registration Failed. Please verify your informations.";
            $messageAr = "فشل في التسجيل. يرجى التحقق من المعلومات الخاصة بك";
            DB::rollBack();
            if ($ex->getCode() == 90) {
                $message = $ex->getMessage();
            }
            return response()->json([
                'message' => $message,
                'messageAr' => $messageAr,
                "error" => $ex,
                "success" => false
            ], 201);
        }
    }

    public function loginWithPhoneNumber(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('phone', $request->phone)
            ->where('user_type', "customer")
           // ->where("end_sub_date",">", Carbon::now())
            ->first();

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->email_verified_at == null) {
                    return response()->json([
                        'message' => "Please validate your account",
                        'messageAr' => "يرجى التحقق من صحة حسابك",
                        "success" => true,
                        "user" => $user,
                        "code" => 300
                    ]);
                }
                $token = $user->createToken('API Token')->plainTextToken;
                $user->personal_fire_base_token = $request->input("personal_fire_base_token");
                $user->save();
                return response()->json([
                    'message' => "login successfully",
                    "success" => true,
                    "user" => $user,
                    'access_token' => $token
                ]);
            } else {
                return response()->json([
                    'message' => "invalid phone or password",
                    'messageAr' => "رقم الهاتف أو كلمة السر خاطئة",
                    "success" => false,
                    "user" => null
                ]);
            }
        } else {
            return response()->json([
                'message' => "invalid phone or password",
                'messageAr' => "رقم الهاتف أو كلمة السر خاطئة",
                "success" => false,
                "user" => null
            ]);
        }
    }

    public function confirmCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'verification_code' => 'required',
        ]);


        DB::beginTransaction();
        try {
            $user = User::where('id', $request->user_id)->first();
            if (empty($user)) {
                throw new Exception();
            }
            if ($user->verification_code == $request->verification_code) {
                $user->email_verified_at = Carbon::now();
                $user->verification_code = null;
                $user->save();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => translate('Your account is now verified.Please login'),
                    'messageAr' => "تم التحقق من حسابك الآن الرجاء تسجيل الدخول",
                ], 200);
            } else {
                throw new Exception();
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => translate('Code does not match, you can request for resending the code'),
                'messageAr' => "الرمز غير متطابق ، يمكنك طلب إعادة إرسال الرمز",
            ], 200);
        }
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required'
        ]);
        DB::beginTransaction();
        try {

            $user = User::where('id', $request->user_id)->first();
            if (empty($user)) {
                throw new Exception();
            }
            $user->verification_code = rand(1000, 9999);

            if (!empty($user->email)) {
                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
            $otpController = new OTPVerificationController();
            if (!$otpController->send_code($user)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('Error occurred when trying to send verification code, can you try later or try to reach the support '),
                    'message' => 'حدث خطأ أثناء محاولة إرسال رمز التحقق ، هل يمكنك المحاولة لاحقًا أو محاولة الوصول إلى الدعم',
                ], 200);
            }

            $user->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => translate('Verification code is sent again'),
                'messageAr' => "تم إرسال رمز التحقق مرة أخرى",
            ], 200);
        } catch (Exception $ex) {

            $message = "Re Send code Failed. Please verify your informations.";
            $messageAr = "فشل. يرجى التحقق من المعلومات الخاصة بك";
            return response()->json([
                'message' => $message,
                'messageAr' => $messageAr,
                "error" => $ex,
                "success" => false
            ], 201);
        }
    }


    public function loginGoogle(Request $req)
    {
        $req->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $req->email)
            ->where('user_type', "customer")
            ->where("end_sub_date",">", Carbon::now())
            ->first();

        if ($user != null) {
            $token = $user->createToken('API Token')->plainTextToken;

            $user->personal_fire_base_token = $req->input("personal_fire_base_token");
            $user->save();
            return response()->json([
                'message' => "login successfully",
                "success" => true,
                "user" => $user,
                'access_token' => $token
            ]);
        } else {
            return response()->json([
                'message' => "invalid email",
                'messageAr' => "بريد إلكتروني خاطئ",
                "success" => false,
                "user" => null
            ]);
        }
    }

    public function signup(Request $req)
    {
        // $validator = $req->validate([
        //     'first_name' => 'required|string',
        //     'last_name' => 'required|string',
        //     'email' => 'required',
        //     'mobile' => 'required',
        //     'gender' => 'required|string',
        //     'date_birth' => 'required|string',
        //     'nationality' => 'required|string',
        //     'country' => 'required|string',
        // ]);


        try {
            $users = DB::table('users')->where('email', $req->email)->get();
            // dd($users);
            if (count($users) > 0) {
                return response()->json([
                    'message' => "Registration Failed. Email already exist!.",
                    'messageAr' => "فشل في التسجيل. البريد الالكتروني موجود مسبقا",
                    "error" => "",
                    "success" => false
                ], 201);
            }
            //$password = Str::random(8);
            $password = "123456";
            $mobile = str_replace('+', '', $req->mobile);

            $user = DB::table('users')->insertGetId(
                [
                    'name' => $req->first_name . " " . $req->last_name,
                    "email" => $req->email,
                    "user_type" => "customer",
                    "phone" => $req->mobile,
                    "gender" => $req->gender,
                    "date_birth" => $req->date_birth,
                    "nationality" => $req->nationality,
                    "country" => $req->country,
                    "password" => \bcrypt($password),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            $data = [
                "email" => $req->email,
                "password" => $password
            ];

            $to = $req->email;

            try {
                Mail::send('email.signup', $data, function ($messages) use ($to) {
                    $messages->to($to);
                    $messages->subject('WELCOME TO YOUR GREEN CARD');
                });
            } catch (\Throwable $th) {
                //throw $th;
            }

            try {
                $response = $this->sendSMS($req->email, $password, $mobile);
            } catch (\Throwable $th) {
                //throw $th;
            }

            // $response = $this->sendSMS($req->email, $password, $mobile);

            // dd($response);

            // Send SMS
            return response()->json([
                'message' => 'Registration Successful. Please verify and log in to your account.',
                'messageAr' => "تم التسجيل بنجاح. يرجى التحقق وتسجيل الدخول إلى حسابك",
                "success" => true,
                "user" => $user,
                "password" => $password
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Registration Failed. Please verify your informations.",
                'messageAr' => "فشل في التسجيل. يرجى التحقق من المعلومات الخاصة بك",
                "error" => $th,
                "success" => false
            ], 201);
        }
    }


    private function sendSMS($login, $password, $mobile)
    {
        $post = (object)[
            "userName" => "Gcsms",
            "apiKey" => "e2a372ac1e4afaf53677dbf3192eee12",
            "numbers" => $mobile,
            "userSender" => "GREENCARD",
            "msg" => "Login: $login\nPassword: $password",
            "msgEncoding" => "UTF8"
        ];
        // dd($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('MSEGAT_API_BASE_URL').'/gw/sendsms.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cache-Control: no-cache",
            "content-type:application/json;charset=utf-8"
        ));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    private function sendMail($data, $to)
    {
        $pdf = null;
        $d = (object) $data;
        try {
            $pdf = PDF::loadView('email.invoice', [
                'data' => $d,
                'font_family' => "'Roboto','sans-serif'",
            ], [], []);
        } catch (\Throwable $th) {
        }

        if ($pdf !== null) {
            Mail::send('email.email', $data, function ($messages) use ($to, $pdf) {
                $messages->to($to);
                $messages->subject('Invoice Green Card SA')->attachData($pdf->output(), "invoice.pdf");
            });
        } else {
            Mail::send('email.email', $data, function ($messages) use ($to) {
                $messages->to($to);
                $messages->subject('Invoice Green Card SA');
            });
        }
    }

    public function test()
    {
        return response()->json([
            'message' => "test",
            "success" => false
        ]);
    }

    private function apply_coupon_code($amount, $couponCode, $user_id)
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        if ($coupon == null) return $this->_couponResponse('danger', 'Invalid coupon!', 0);

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', $user_id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);
                    $coupon_discount = 0;
                    if ($coupon->discount_type == 'percent') {
                        $coupon_discount = ($amount * $coupon->discount) / 100;
                        if ($coupon_discount > $coupon_details->max_discount) {
                            $coupon_discount = $coupon_details->max_discount;
                        }
                    } elseif ($coupon->discount_type == 'amount') {
                        $coupon_discount = $coupon->discount;
                    }
                    return $this->_couponResponse('success', 'Coupon has been applied', $coupon_discount);
                } else {
                    return $this->_couponResponse('warning', 'You already used this coupon!', 0);
                }
            } else {
                return $this->_couponResponse('warning', 'Coupon expired!', 0);
            }
        }

        return $this->_couponResponse('danger', 'Invalid coupon!', 0);
    }

    public function _couponResponse($type, $message, $discount)
    {
        $response = array();

        $response['response'] = $type;
        $response['message'] = translate($message);
        $response['coupon_discount'] = $discount;

        return $response;
    }

    public function payment(Request $req)
    {
        $validator = $req->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'card_type' => 'required|string',
            'card_no' => 'required|string',
            'card_month' => 'required|string',
            'card_year' => 'required|string',
            'card_cvv' => 'required|string',
            'amount' => 'required',
            'qr_code' => 'required',
            "vat_total" => 'required',
            "date_qr" => "required",
            'user' => 'required',
            'package' => 'required'
        ]);


        $payment_amount = $req->amount;
        if (!empty($req->coupon)) {
            $discount_func = $this->apply_coupon_code($req->amount, $req->coupon, $req->user["id"]);
            $payment_amount = $payment_amount - $discount_func["coupon_discount"];
        }

        $name = $req->first_name . " " . $req->last_name;
        $customer = DB::table('users')
            ->where('id', $req->user["id"])
            ->where('customer_package_id', "!=", null)
            ->first();
        if (env("HYPERPAY_ACTIVE") == "on") {
            if ($customer == null) {
                $responseData = $this->requestPayment($name, $req->card_no, $req->card_month, $req->card_year, $req->card_cvv, $payment_amount, $req->card_type);
                // test code start
                $res = $responseData;
                //$myfile = fopen("./test-error/newfile.txt", "w") or die("Unable to open file!");
                //$txt = $res;
                //fwrite($myfile, $txt);
                //fclose($myfile);
                // test code end
                $responsePayment = json_decode($responseData, true);
                if (isset($responsePayment['result'])) {
                    $res = $responsePayment['result'];
                    if ($res['code'] == "000.100.110") {
                        //if($res['code'] == "000.200.000"){

                        $customer_id = null;
                        try {
                            $customer_id = DB::table('customers')->insertGetId([
                                'user_id' => $req->user["id"],
                            ]);
                        } catch (\Throwable $th) {
                        }
                        if ($customer_id !== null) {
                            $package = DB::table('customer_packages')->where("id", $req->package["id"])->first();
                            $user = DB::table('users')
                                ->where('id', $req->user["id"])
                                ->update([
                                    'customer_package_id' => $package->id,
                                    'start_sub_date' => Carbon::now(),
                                    'end_sub_date' => Carbon::now()->addDays($package->duration),
                                    'duration' => $package->duration,
                                    'nb_members' => $package->nb_members - 1,
                                ]);

                            $payment_id = DB::table('customer_package_payments')->insertGetId([
                                'user_id' => $req->user["id"],
                                "customer_package_id" => $req->package['id'],
                                "payment_method" => "Bank card",
                                "payment_details" => $responsePayment['id'],
                                "approval" => 1,
                                "offline_payment" => 2,
                                "reciept" => "",
                                "qr_code" => $req->qr_code
                            ]);

                            $data = [
                                "user_name" => $req->first_name . " " . $req->last_name,
                                "email" => $req->user['email'],
                                "package_name" => $req->package['label'],
                                "package_price" => $req->amount,
                                "date" => $req->date_qr,
                                "invoice_id" => $payment_id,
                                "qr_code" => $req->qr_code,
                                "vat_val" => $req->vat_total
                            ];
                            $to = $req->user['email'];

                            try {
                                $this->sendMail($data, $to);
                            } catch (\Throwable $th) {
                            }


                            return response()->json([
                                'message' => 'Your payment was successful.',
                                'messageAr' => "تم الدفع الخاص بك بنجاح",
                                "success" => true,
                                "exist" => false,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Your payment was not successful. Please try again.',
                            'messageAr' => "الدفع الخاص بك لم يكن ناجحا. حاول مرة اخرى",
                            "code" => $res['code'],
                            "success" => false
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'You have already pay.',
                    'messageAr' => "لقد دفعت مسباقا",
                    "success" => true,
                    "exist" => true,
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'Payment online not active for now, please try again',
                'messageAr' => "الدفع عبر الإنترنت غير نشط في الوقت الحالي ، يرجى المحاولة مرة أخرى",
                "success" => false,
                "exist" => false,
            ], 200);
        }


        // return response()->json([
        //     'message' => 'Your payment was not successful. Please try again.',
        //     "success" => false
        // ], 200);
    }

    public function UpdatePayment(Request $req)
    {

        $validator = $req->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'card_type' => 'required|string',
            'card_no' => 'required|string',
            'card_month' => 'required|string',
            'card_year' => 'required|string',
            'card_cvv' => 'required|string',
            'amount' => 'required',
            'qr_code' => 'required',
            "vat_total" => 'required',
            "date_qr" => "required",
            'user' => 'required',
            'package' => 'required'
        ]);

        $payment_amount = $req->amount;
        if (!empty($req->coupon)) {
            $discount_func = $this->apply_coupon_code($req->amount, $req->coupon, $req->user["id"]);
            $payment_amount = $payment_amount - $discount_func["coupon_discount"];
        }

        $name = $req->first_name . " " . $req->last_name;
        if (env("HYPERPAY_ACTIVE") == "on") {
            $responseData = $this->requestPayment($name, $req->card_no, $req->card_month, $req->card_year, $req->card_cvv, $payment_amount, $req->card_type);
            $responsePayment = json_decode($responseData, true);
            if (isset($responsePayment['result'])) {
                $res = $responsePayment['result'];

                // if($res['code'] == "000.100.110") (Old code but actually returning 000.200.000)
                if ($res['code'] == "000.200.000") {
                    $package = DB::table('customer_packages')->where("id", $req->user["id"])->first();
                    DB::table('users')
                        ->where('id', $req->user["id"])
                        ->update([
                            'customer_package_id' => $req->package['id'],
                            'start_sub_date' => Carbon::now(),
                            'duration' => $package->duration,
                            'end_sub_date' => Carbon::now()->addDays($package->duration),
                            'nb_members' => $package->nb_members - 1,
                        ]);

                    $payment_id = DB::table('customer_package_payments')->insertGetId([
                        'user_id' => $req->user["id"],
                        "customer_package_id" => $req->package['id'],
                        "payment_method" => "Bank card",
                        "payment_details" => $responsePayment['id'],
                        "approval" => 1,
                        "offline_payment" => 2,
                        "reciept" => "",
                        "qr_code" => $req->qr_code
                    ]);

                    $data = [
                        "user_name" => $req->first_name . " " . $req->last_name,
                        "email" => $req->user['email'],
                        "package_name" => $req->package['label'],
                        "package_price" => $req->amount,
                        "date" => $req->date_qr,
                        "invoice_id" => $payment_id,
                        "qr_code" => $req->qr_code,
                        "vat_val" => $req->vat_total
                    ];
                    $to = $req->user['email'];

                    try {
                        $this->sendMail($data, $to);
                    } catch (\Throwable $th) {
                    }


                    return response()->json([
                        'message' => 'Your payment was successful.',
                        'messageAr' => "تم الدفع الخاص بك بنجاح",
                        "success" => true,
                        "exist" => false,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Your payment was not successful. Please try again.',
                        'messageAr' => "الدفع الخاص بك لم يكن ناجحا. حاول مرة اخرى",
                        "code" => $res['code'],
                        "success" => false
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'message' => 'Payment online not active for now, please try again',
                'messageAr' => "الدفع عبر الإنترنت غير نشط في الوقت الحالي ، يرجى المحاولة مرة أخرى",
                "success" => false,
                "exist" => false,
            ], 200);
        }
    }

    private function requestPayment($name, $card_no, $card_month, $card_year, $card_cvv, $amount, $type_card)
    {
        $url = env("HYPERPAY_MODE_TEST") == "on" ? "https://eu-test.oppwa.com/v1/payments" : "https://eu-prod.oppwa.com/v1/payments";
        $ssl = env("HYPERPAY_SSL") == "on" ? true : false;
        $amount = number_format((float)$amount, 2, '.', '');

        //        $data = "entityId=".env('HYPERPAY_ID', '8a8294174b7ecb28014b9699220015ca') .
        $data = "entityId=" . env('HYPERPAY_ID', '8ac7a4ca7d97962b017d997e2b8d06e4') .
            //            "&testMode=EXTERNAL" .
            "&amount=" . $amount .
            "&currency=" . env("HYPERPAY_CURRENCY", "SAR") .
            "&paymentBrand=" . $type_card .
            "&paymentType=DB" .
            "&shopperResultUrl=/heelo00000" .
            "&card.number=" . $card_no .
            "&card.holder=" . $name .
            "&card.expiryMonth=" . $card_month .
            "&card.expiryYear=" . $card_year .
            "&card.cvv=" . $card_cvv;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //                       'Authorization:Bearer '.env("HYPERPAY_TOKEN", "OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg=")));
            'Authorization:Bearer ' . env("HYPERPAY_TOKEN", "OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg==")
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }


    public function getShops(Request $request, Category $category)
    {
        $shopsQueryBuilder = Shop::where('categories', 'like', '%"' . $category->id . '"%');
        if($request->has("cities") && !empty($request->input("cities"))){
            $shopsQueryBuilder->join("branches",'branches.shop_id','=','shops.id')->where(function ($query)use ($request) {
                $query->whereIn("shops.city_id",explode(',', $request->input("cities")))
                        ->orWhereIn("branches.city_id",explode(',', $request->input("cities")));
            });
        }
       $shops=$shopsQueryBuilder->select(
                'shops.categories as categories',
                'shops.address as address',
                'shops.address_ar as address_ar',
                'shops.category_id as category_id',
                'shops.id as id',
                'shops.logo as logo',
                'shops.name as name',
                'shops.name_ar as name_ar',
                'shops.latitude as latitude',
                'shops.longitude as longitude',
                'shops.default_image as default_image',
                'shops.user_id as user_id',
                'shops.meta_title as title',
                'shops.meta_description as description',
                'shops.meta_description_ar as description_ar',
                'shops.meta_title_ar as title_ar',
                'shops.city_id as city_id'
            )->get();
            foreach ($shops as $shop) {

                $shop->categories = json_decode($shop->categories);

                $shopCity = $shop->city;
                $upload = $shop->upload;
                if (!empty($shopCity)) {
                    $shop->city_name = $shopCity->name;
                    $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
                }
                if (!empty($upload)) {
                    $shop->file_name = $upload->logo_url;
                    $shop->external_link = $upload->external_link;
                }

                $branches = Branch::where("shop_id", $shop->id)->get();
                $availableInCities = [];
                foreach ($branches as $branch) {

                    $city = $branch->branchCity;
                    $tempCity = (object)[];
                    if (!empty($city)) {
                        $tempCity->id = $city->id;
                        $tempCity->name = $city->name;
                        $tempCity->name_ar = $city->getTranslation('name', "ar");
                        array_push($availableInCities, $tempCity);
                    }
                }
                $shop->availableInCities = $availableInCities;
            }


        return response()->json(['status' => 200, 'total_results' => count($shops), 'shops' => $shops]);
    }

    public function getAllShopsLocations(Request $request)
    {
        $shopsQueryBuilder = Shop::whereNotNull('categories')->orWhere("categories", '<>', '');
        if($request->has("cities") && !empty($request->input("cities"))){
            $shopsQueryBuilder->join("branches",'branches.shop_id','=','shops.id')->where(function ($query)use ($request) {
                $query->whereIn("shops.city_id",explode(',', $request->input("cities")))
                        ->orWhereIn("branches.city_id",explode(',', $request->input("cities")));
            });
        }
       $shops=$shopsQueryBuilder->select(
            'shops.categories as categories',
            'shops.address as address',
            'shops.address_ar as address_ar',
            'shops.category_id as category_id',
            'shops.id as id',
            'shops.logo as logo',
            'shops.name as name',
            'shops.name_ar as name_ar',
            'shops.latitude as latitude',
            'shops.longitude as longitude',
            'shops.default_image as default_image',
            'shops.user_id as user_id',
            'shops.meta_title as title',
            'shops.meta_description as description',
            'shops.meta_description_ar as description_ar',
            'shops.meta_title_ar as title_ar',
            'shops.city_id as city_id'
            )->get();

        foreach ($shops as $shop) {

            $shop->categories = json_decode($shop->categories);

            $shopCity = $shop->city;
            $upload = $shop->upload;
            if (!empty($shopCity)) {
                $shop->city_name = $shopCity->name;
                $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
            }
            if (!empty($upload)) {
                $shop->file_name = $upload->logo_url;
                $shop->external_link = $upload->external_link;
            }

            $branches = Branch::where("shop_id", $shop->id)->get();
            $availableInCities = [];
            foreach ($branches as $branch) {

                $city = $branch->branchCity;
                $tempCity = (object)[];
                if (!empty($city)) {
                    $tempCity->id = $city->id;
                    $tempCity->name = $city->name;
                    $tempCity->name_ar = $city->getTranslation('name', "ar");
                    array_push($availableInCities, $tempCity);
                }
            }
            $shop->availableInCities = $availableInCities;
        }

        return response()->json(['status' => 200, 'total_results' => count($shops), 'shops' => $shops]);
    }

    public function getShop($id)
    {
        $shop = Shop::where('id', $id)
            ->first();

        $shopCity = $shop->city;
        if (!empty($shopCity)) {
            $shop->city_name = $shopCity->name;
            $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
        }
        if ($shop != null) {
            $ids = explode(",", $shop->sliders);
            // dd($ids);
            $tags = explode(",", $shop->meta_description);
            $menus = explode("|", $shop->menu);
            // array_push($ids, $shop->id);
            $images = [];
            $images_external_link = [];
            $sliders = DB::table('uploads')
                ->whereIn('id', $ids)
                ->select("file_name as url", "external_link")
                ->get();
            // dd($sliders);
            foreach ($sliders as $sld) {
                array_push($images_external_link, $sld->external_link);
                array_push($images, $sld->url);
            }
            $rate = rand(1, 3);
            // array_push($images, "uploads/all/w8uiFkhfuqyLygqxL1GFm32dPEXo58cgfKy68pjq.jpg");
            $shop->images = $images;
            $shop->tags = $tags;
            $shop->rating = $rate;
            $shop->menus = $menus;
        }

        return response()->json(['status' => 200, 'success' => true, 'shop' => $shop]);
    }

    public function getShopsByLocation(Request $req)
    {
        $shops = Shop::select(
            'shops.categories as categories',
            'shops.address as address',
            'shops.address_ar as address_ar',
            'shops.category_id as category_id',
            'shops.id as id',
            'shops.logo as logo',
            'shops.name as name',
            'shops.name_ar as name_ar',
            'shops.default_image as default_image',
            'shops.user_id as user_id',
            'shops.meta_title as title',
            'shops.meta_description as description',
            'shops.meta_description_ar as description_ar',
            'shops.meta_title_ar as title_ar',
            'shops.city_id as city_id'
        )->get();

        foreach ($shops as $shop) {
            $shop->categories = json_decode($shop->categories);

            $shopCity = $shop->city;
            $upload = $shop->upload;

            if (!empty($shopCity)) {
                $shop->city_name = $shopCity->name;
                $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
            }
            if (!empty($upload)) {
                $shop->file_name = $upload->logo_url;
                $shop->external_link = $upload->external_link;
            }
        }

        return response()->json(['status' => 200, 'total_results' => count($shops), 'shops' => $shops]);
    }

    public function getShopsByCity(Request $request, City  $city)
    {
        $branches = $city->branches;
        $shopsByCity = $city->shops;

        if(empty($branches)){
            return response()->json(
                [
                    'status' => 200,
                    'total_results' => 0,
                    'shops' => []
                ]);
        }

        foreach ($shopsByCity as $shop) {

            $shop->categories = json_decode($shop->categories);

            $shopCity = $shop->city;
            $upload = $shop->upload;
            if (!empty($shopCity)) {
                $shop->city_name = $shopCity->name;
                $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
            }
            if (!empty($upload)) {
                $shop->file_name = $upload->logo_url;
                $shop->external_link = $upload->external_link;
            }

            $branches = Branch::where("shop_id", $shop->id)->get();
            $availableInCities = [];
            foreach ($branches as $branch) {

                $city = $branch->branchCity;
                $tempCity = (object)[];
                if (!empty($city)) {
                    $tempCity->id = $city->id;
                    $tempCity->name = $city->name;
                    $tempCity->name_ar = $city->getTranslation('name', "ar");
                    array_push($availableInCities, $tempCity);
                }
            }
            $shop->availableInCities = $availableInCities;
        }

        $shops = $shopsByCity->toArray();
        foreach ($branches as $branch) {
            $shop=  $branch->shop;
            $shop->categories = json_decode($shop->categories);

            $shopCity = $shop->city;
            $upload = $shop->upload;

            if (!empty($shopCity)) {
                $shop->city_name = $shopCity->name;
                $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
            }
            if (!empty($upload)) {
                $shop->file_name = $upload->logo_url;
                $shop->external_link = $upload->external_link;
            }


            $branches = Branch::where("shop_id", $shop->id)->get();
            $availableInCities = [];
            foreach ($branches as $branch) {

                $city = $branch->branchCity;
                $tempCity = (object)[];
                if (!empty($city)) {
                    $tempCity->id = $city->id;
                    $tempCity->name = $city->name;
                    $tempCity->name_ar = $city->getTranslation('name', "ar");
                    array_push($availableInCities, $tempCity);
                }
            }
            $shop->availableInCities = $availableInCities;
            array_push($shops,$shop);
        }
        return response()->json(
            [
                'status' => 200,
                'total_results' => count($shops),
                'shops' => $shops
            ]);
    }

    public function getShopsByName(Request $req)
    {
        $shops = [];
        if ($req->name != null) {
            $shops = Shop::where('name', "like", '%' . $req->name . '%')
                ->orWhere('name_ar', "like", '%' . $req->name . '%')
                ->select(
                    'shops.categories as categories',
                    'shops.address as address',
                    'shops.address_ar as address_ar',
                    'shops.category_id as category_id',
                    'shops.id as id',
                    'shops.logo as logo',
                    'shops.name as name',
                    'shops.name_ar as name_ar',
                    'shops.default_image as default_image',
                    'shops.user_id as user_id',
                    'shops.meta_title as title',
                    'shops.meta_description as description',
                    'shops.meta_description_ar as description_ar',
                    'shops.meta_title_ar as title_ar',
                    'shops.city_id as city_id'
                )->get();

            foreach ($shops as $shop) {
                $shop->categories = json_decode($shop->categories);

                $shopCity = $shop->city;
                $upload = $shop->upload;

                if (!empty($shopCity)) {
                    $shop->city_name = $shopCity->name;
                    $shop->city_name_ar = $shopCity->getTranslation('name', "ar");
                }
                if (!empty($upload)) {
                    $shop->file_name = $upload->logo_url;
                    $shop->external_link = $upload->external_link;
                }

                $branches = Branch::where("shop_id", $shop->id)->get();
                $availableInCities = [];
                foreach ($branches as $branch) {

                    $city = $branch->branchCity;
                    $tempCity = (object)[];
                    if (!empty($city)) {
                        $tempCity->id = $city->id;
                        $tempCity->name = $city->name;
                        $tempCity->name_ar = $city->getTranslation('name', "ar");
                        array_push($availableInCities, $tempCity);
                    }
                }
                $shop->availableInCities = $availableInCities;
            }
        }
        return response()->json(['status' => 200, 'total_results' => count($shops), 'shops' => $shops]);
    }


    public function sendSmsTest()
    {
        $to = "212679869523";
        $text = "Your Code: 1234";
        $url = env('MSEGAT_API_BASE_URL')."/gw/sendsms.php";
        $data = [
            "apiKey" => env('MSEGAT_API_KEY'),
            "numbers" => $to,
            "userName" => env('MSEGAT_USERNAME'),
            "userSender" => env('MSEGAT_USER_SENDER'),
            "msg" => $text
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }



    public function getOffers($id, $id_user)
    {

        $offers = DB::table('offers')
            ->where('id_shop', $id)
            ->where('is_active', 1)
            ->get();
        $data = [];
        foreach ($offers as $offer) {
            $nb_usage = DB::table('offers_scanned')
                ->where('offer_id', $offer->id)
                ->where('user_id', $id_user)->count();
            $offer->nb_usage = $nb_usage;
            $numbers = range(1000, 9000);
            shuffle($numbers);
            $offer->code = $numbers[0];
            array_push($data, $offer);
        }

        return response()->json(['status' => 200, "success" => true, 'total_results' => count($data), 'offers' => $data]);
    }

    /*

        @Deprecated
    */
    public function resetPasssword(Request $req)
    {
        $req->validate([
            'email' => 'required',
        ]);

        $user = DB::table('users')
            ->where("email", $req->email)->first();
        if ($user != null) {
            //$password = Str::random(8);
            $password = "123456";
            $affected = DB::table('users')
                ->where('email', $req->email)
                ->update(['password' => \bcrypt($password)]);

            if ($affected) {
                $data = [
                    "email" => $req->email,
                    "password" => $password
                ];

                $to = $req->email;
                try {
                    Mail::send('email.reset', $data, function ($messages) use ($to) {
                        $messages->to($to);
                        $messages->subject('GREEN CARD | RESET YOUR PASSWORD');
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }

                return response()->json([
                    'status' => 200,
                    "success" => true,
                    'message' => "Password reset successfully, check your email",
                    'messageAr' => "إعادة تعيين كلمة المرور بنجاح ، تحقق من بريدك الإلكتروني",
                ]);
            }

            return response()->json([
                'status' => 200,
                "success" => true,
                'message' => "Password reset not successfully",
                'messageAr' => "لم يتم إعادة تعيين كلمة المرور بنجاح",
            ]);
        }

        return response()->json([
            'status' => 200,
            "success" => false,
            'message' => "Email not exist",
            'messageAr' => "البريد الالكتروني غير موجود",
        ]);
    }

    public function resetPasssword_V2(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required',
            'send_code_by' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $user = [];
            switch ($request->input("send_code_by")) {
                case 'email':
                    $user = User::where("email", $request->input("email_or_phone"))->first();
                    break;
                case 'phone':
                    $user = User::where("phone", $request->input("email_or_phone"))->first();
                    break;
                default:
                    throw new Exception(translate("Invalid input data"), 400);
                    break;
            }

            if (!empty($user)) {
                //$password = Str::random(8);
                $password = "123456";
                $user->password = Hash::make($password);
                sendPasswordToUser($user, $password, $request->input("send_code_by"));
                $user->save();
                DB::commit();
                return response()->json([
                    'status' => 200,
                    "success" => true,
                    "user" => $user,
                    'message' => "Password reset  successfully",
                    'messageAr' => " تمت إعادة تعيين كلمة المرور بنجاح",
                ]);
            } else {
                throw new Exception("User not found", 404);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            $message = [];
            $code = $ex->getCode();
            switch ($ex->getCode()) {
                case 400:
                    $message = [
                        'status' => 400,
                        "success" => false,
                        'message' => "Invalid input data",
                        'messageAr' => "بيانات الإدخال غير صالحة",
                    ];
                    break;
                case 404:
                    $message = [
                        'status' => 404,
                        "success" => false,
                        'message' => "User Not Found",
                        'messageAr' => "لم يتم العثور على المستخدم",
                    ];
                    break;

                default:
                    $code = 500;
                    break;
            }
            return response()->json($message, $code);
        }
    }
    public function scanOffer(Request $req)
    {
        $req->validate([
            'user_id' => 'required',
            'offer_id' => 'required',
            'code' => 'required',
            'limit_user' => 'required',
        ]);

        // check for the package
        if ($req->limit_user) {
            $offers = DB::table('offers_scanned')
                ->where('user_id', $req->user_id)
                ->where('offer_id', $req->offer_id)
                ->get();

            if ($req->nb_limit > count($offers)) {
                $id = DB::table('offers_scanned')->insertGetId(
                    [
                        'user_id' => $req->user_id,
                        'offer_id' => $req->offer_id,
                        'code' => $req->code,
                        "approved" => 1,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now()
                    ]
                );

                if ($id != null) {
                    $offer = DB::table('offers')
                        ->where('id', $req->offer_id)
                        ->first();
                    if ($offer != null) {
                        $nb_points = $offer->nb_points;
                        DB::table('users')
                            ->where('id', $req->user_id)
                            ->increment('nb_points', $nb_points);
                    }
                    return response()->json([
                        'status' => 200,
                        "success" => true,
                        'msg' => "offer saved",
                        'msgAr' => "تم حفظ العرض الخاص بك",
                        'remaining' => $req->nb_limit - (count($offers) + 1)
                    ]);
                }
                return response()->json([
                    'status' => 200,
                    "success" => false,
                    'msg' => "offer not valid",
                    'msgAr' => "العرض غير صالح",
                ]);
            }

            return response()->json([
                'status' => 200,
                "success" => false,
                'msg' => "limit usage",
                'msgAr' => "الحد من الاستخدام",
            ]);
        }

        $id = DB::table('offers_scanned')->insertGetId(
            [
                'user_id' => $req->user_id,
                'offer_id' => $req->offer_id,
                "approved" => 1,
                'code' => $req->code,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        );

        if ($id != null) {
            $offer = DB::table('offers')
                ->where('id', $req->offer_id)
                ->first();
            if ($offer != null) {
                $nb_points = $offer->nb_points;
                DB::table('users')
                    ->where('id', $req->user_id)
                    ->increment('nb_points', $nb_points);
            }
            return response()->json([
                'status' => 200,
                "success" => true,
                'msg' => "offer saved",
                'msgAr' => "تم حفظ العرض الخاص بك",
            ]);
        }
        return response()->json([
            'status' => 200,
            "success" => false,
            'msg' => "offer not valid",
            'msgAr' => "العرض غير صالح",
        ]);
    }

    public function approveScanOffer(Request $req)
    {

        $req->validate([
            'user_id' => 'required',
            'offer_id' => 'required',
            'offer_scanned_id' => 'required',
        ]);

        $status = DB::table('offers_scanned')
            ->where('id', $req->offer_scanned_id)
            ->where('user_id', $req->user_id)
            ->where('offer_id', $req->offer_id)
            ->update(['approved' => 1]);

        if ($status) {
            $offer = DB::table('offers')
                ->where('id', $req->offer_id)
                ->first();
            if ($offer != null) {
                if ($offer->ilimitless_usage == 1) {
                    $nb_points = $offer->nb_points;
                    DB::table('users')
                        ->where('id', $req->user_id)
                        ->increment('nb_points', $nb_points);
                    return response()->json(['status' => 200, "success" => true, 'msg' => "offer approved", 'ilimitless_usage' => true]);
                } else {
                    $offers = DB::table('offers_scanned')
                        ->where('user_id', $req->user_id)
                        ->where('offer_id', $req->offer_id)
                        ->get();
                    if (count($offers) <= $offer->member_of_usage) {
                        $nb_points = $offer->nb_points;
                        DB::table('users')
                            ->where('id', $req->user_id)
                            ->increment('nb_points', $nb_points);
                        return response()->json(['status' => 200, "success" => true, 'msg' => "offer approved", 'remaining' => $offer->member_of_usage - count($offers)]);
                    }
                    return response()->json(['status' => 200, "success" => false, 'msg' => "limit scan", 'remaining' => $offer->member_of_usage - count($offers)]);
                }
            }
            return response()->json(['status' => 200, "success" => true, 'msg' => "offer approved"]);
        }
        return response()->json(['status' => 200, "success" => false, 'msg' => "offer not valid"]);
    }

    public function CancelScanOffer(Request $req)
    {
        $req->validate([
            'user_id' => 'required',
            'offer_id' => 'required',
            'offer_scanned_id' => 'required',
        ]);

        $is_approved = DB::table('offers_scanned')
            ->where('id', $req->offer_scanned_id)->first();

        $status = DB::table('offers_scanned')
            ->where('id', $req->offer_scanned_id)
            ->where('user_id', $req->user_id)
            ->where('offer_id', $req->offer_id)
            ->update(['approved' => 0]);

        if ($status && $is_approved != null) {
            $offer = DB::table('offers')
                ->where('id', $req->offer_id)
                ->first();
            if ($offer != null) {
                $nb_points = $offer->nb_points;
                if ($is_approved->approved == 1) {
                    DB::table('users')
                        ->where('id', $req->user_id)
                        ->decrement('nb_points', $nb_points);
                }
                return response()->json(['status' => 200, "success" => true, 'msg' => "offer disabled"]);
            }
            return response()->json(['status' => 200, "success" => true, 'msg' => "offer not valid, scan disabled"]);
        }
        return response()->json(['status' => 200, "success" => false, 'offers' => "offer not valid"]);
    }

    public function refuseScanOffer(Request $req)
    {
        $req->validate([
            'user_id' => 'required',
            'offer_id' => 'required',
            'offer_scanned_id' => 'required',
        ]);

        $is_approved = DB::table('offers_scanned')
            ->where('id', $req->offer_scanned_id)->first();

        $status = DB::table('offers_scanned')
            ->where('id', $req->offer_scanned_id)
            ->where('user_id', $req->user_id)
            ->where('offer_id', $req->offer_id)
            ->update(['approved' => -1]);

        if ($status && $is_approved != null) {
            $offer = DB::table('offers')
                ->where('id', $req->offer_id)
                ->first();
            if ($offer != null) {
                $nb_points = $offer->nb_points;
                if ($is_approved->approved == 1) {
                    DB::table('users')
                        ->where('id', $req->user_id)
                        ->decrement('nb_points', $nb_points);
                }
                return response()->json(['status' => 200, "success" => true, 'msg' => "offer disabled"]);
            }
            return response()->json(['status' => 200, "success" => true, 'msg' => "offer not valid, scan disabled"]);
        }
        return response()->json(['status' => 200, "success" => false, 'offers' => "offer not valid"]);
    }

    public function getOrders($id)
    {
        $orders = DB::table('offers_scanned')
            ->where('offers_scanned.user_id', $id)
            ->join('offers', "offers.id", "=", "offers_scanned.offer_id")
            ->join('shops', "shops.id", "=", "offers.id_shop")
            ->orderby("offers_scanned.created_at", "desc")
            ->select('offers.*', 'offers_scanned.*', 'offers_scanned.id as parant_id', "shops.name as shop_name")
            ->get();

        return response()->json(['status' => 200, "success" => true, "total" => count($orders), 'orders' => $orders]);
    }

    public function getPackage($id)
    {
        $package = DB::table('customer_packages')
            ->where('customer_packages.id', $id)
            ->leftJoin('uploads', "customer_packages.logo", "=", "uploads.id")
            ->join('customer_package_translations', 'customer_packages.id', '=', 'customer_package_translations.customer_package_id')
            ->where('customer_package_translations.lang', "en")
            ->select('customer_packages.*', 'uploads.file_name as url_logo', 'uploads.external_link as external_link', 'customer_package_translations.name as label')
            ->first();

        return response()->json(['status' => 200, "success" => true, 'package' => $package]);
    }

    public function getUser($id)
    {
        $user = DB::table('users')
            ->where('users.id', $id)
            ->leftJoin('uploads', "users.avatar", "=", "uploads.id")
            ->select('users.*', 'uploads.file_name as url_avatar', 'uploads.external_link as external_link')
            ->first();

        return response()->json(['status' => 200, "success" => true, 'user' => $user]);
    }

    public function getFamily($id)
    {


        $users = DB::table('users')->where('parant_id', $id)->get();

        return response()->json([
            'total' => count($users),
            "success" => true,
            "users" => $users,
        ], 201);
    }

    public function deleteFamily(Request $req)
    {

        $validator = $req->validate([
            'parant_id' => 'required',
            'id' => 'required',
        ]);

        $status = DB::table('users')
            ->where('id', $req->id)
            ->where('parant_id', $req->parant_id)
            ->delete();

        $users = DB::table('users')->where('parant_id', $req->parant_id)->get();

        return response()->json([
            'total' => count($users),
            "success" => true,
            "users" => $users,
        ], 201);
    }

    public function addFamily(Request $req)
    {
        $req->validate([
            'parant_id' => 'required',
            'customer_package_id' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'date_birth' => 'required|string',

        ]);

        DB::beginTransaction();
        try {
            // check if the parent existed
            $parent = User::where("id", $req->input("parant_id"))->first();
            if (empty($parent)) {
                throw new Exception("parent not fount", 404);
            }

            // check the user can add member to family
            $package = CustomerPackage::where("id", $parent->customer_package_id)->first();
            if (empty($package)) {
                throw new Exception("package not fount", 404);
            }


            if ($parent->nb_members <= 0) {
                throw new Exception("parent can't add more members to family", 400);
            }
            // TO DO

            if ($req->has("email") && !empty($req->input("email"))) {
                $user = User::where('phone', $req->input("email"))->first();
                if (!empty($user)) {
                    return response()->json([
                        'message' => "Registration Failed. Email already exist!.",
                        'messageAr' => "فشل في التسجيل. البريد الالكتروني موجود مسبقا",
                        "error" => "",
                        "success" => false
                    ], 201);
                }
            }
            if ($req->has("mobile") && !empty($req->input("mobile"))) {
                $user = User::where('phone', $req->input("mobile"))->first();
                if (!empty($user)) {
                    return response()->json([
                        'message' => "Registration Failed. The Mobile Phone already exist!.",
                        'messageAr' => "فشل في التسجيل. الهاتف موجود مسبقا",
                        "error" => "",
                        "success" => false
                    ], 201);
                }
            }

            //$password = Str::random(8);
            $password = "123456";
            $user = new User();
            $flag = false;
            if ($req->has("mobile") && !empty($req->input("mobile"))) {
                $flag = true;
                $user->phone = $req->input("mobile");
            }
            if ($req->has("email") && !empty($req->input("email"))) {
                $flag = true;
                $user->email = $req->input("email");
            }

            if (!$flag) {
                return response()->json([
                    'message' => "Registration Failed. you need to provide email or mobile",
                    'messageAr' => "فشل في التسجيل. تحتاج إلى تقديم بريد إلكتروني أو هاتف محمول",
                    "success" => false
                ], 201);
            }

            $user->parant_id = $parent->id;
            $user->customer_package_id = $req->input("customer_package_id");
            $user->name = $req->first_name . " " . $req->last_name;
            $user->gender = $req->input("gender");
            $user->date_birth = $req->input("date_birth");
            $user->password = \bcrypt($password);
            $user->start_sub_date = Carbon::now();
            $user->end_sub_date = $parent->end_sub_date;
            $user->nb_members = 0;
            $parent->nb_members = $parent->nb_members - 1;
            $user->email_verified_at = Carbon::now();

            $parent->save();
            $user->save();
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->save();

            $data = [
                "email" => $req->email,
                "password" => $password
            ];

            $to = $req->email;

            try {
                Mail::send('email.signup', $data, function ($messages) use ($to) {
                    $messages->to($to);
                    $messages->subject('WELCOME TO YOUR GREEN CARD');
                });
            } catch (\Throwable $th) {
                Log::error("Email not sent to family member " . $user);
                Log::error($th);
            }
            if ($req->has("mobile") && !empty($req->has("mobile"))) {
                // notify user by SMS
                try {
                    SmsUtility::sent_invite_to_family_member($user, $parent, $password);
                } catch (\Throwable $th) {
                    Log::error("SMS not sent to family member " . $user);
                    Log::error($th);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Registration Successful.',
                'messageAr' => "تم التسجيل بنجاح",
                "success" => true,
                "user" => $user,
            ], 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return response()->json([
                'message' => "Registration Failed.",
                'messageAr' => "فشل في التسجيل",
                "error" => $ex->getMessage(),
                "success" => false
            ], 201);
        }
    }

    public function updateFamily(Request $req)
    {
        $validator = $req->validate([
            'id' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email',
            'mobile' => 'required',
            'gender' => 'required|string',
            'date_birth' => 'required|string',

        ]);


        try {


            $user = DB::table('users')
                ->where('id', $req->id)
                ->update(
                    [
                        'name' => $req->first_name . " " . $req->last_name,
                        "email" => $req->email,
                        "phone" => $req->mobile,
                        "gender" => $req->gender,
                        "date_birth" => $req->date_birth,
                    ]
                );


            return response()->json([
                'message' => 'Update successfully',
                'messageAr' => "تم االتحديث بنجاح",
                "success" => true,
                "user" => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Update Failed.",
                'messageAr' => "فشل في  التحديث",
                "error" => $th,
                "success" => false
            ], 201);
        }
    }


    public function getProducts()
    {

        $products = DB::table('products')
            ->leftJoin('uploads', "products.thumbnail_img", "=", "uploads.id")
            ->where("products.earn_point", ">", 0)
            ->select('products.*', 'uploads.file_name as url_product', 'uploads.external_link as external_link')
            ->get();

        return response()->json(['status' => 200, "success" => true, 'total' => count($products), 'products' => $products]);
    }

    public function getWishlists($id)
    {
        $shops = DB::table('wishlists_shops')
            ->where("wishlists_shops.user_id", $id)
            ->join("shops", "wishlists_shops.shop_id", "=", "shops.id")
            ->leftJoin('uploads', "shops.logo", "=", "uploads.id")
            ->select('wishlists_shops.id as wish_id', 'shops.*', 'uploads.file_name as url_logo', 'uploads.external_link as external_link')
            ->get();

        return response()->json(['status' => 200, "success" => true, 'total' => count($shops), 'data' => $shops]);
    }

    public function deleteFromWishLists($id)
    {
        $delete = DB::table('wishlists_shops')->where('id', $id)->delete();

        if ($delete) {
            return response()->json([
                'message' => 'Your item deleted successfully.',
                'messageAr' => "تم حذف العنصر الخاص بك بنجاح",
                "success" => true,
            ], 201);
        }

        return response()->json([
            'message' => 'Your item not deleted try again.',
            'messageAr' => "لم يتم حذف العنصر الخاص بك حاول مرة أخرى",
            "success" => false,
        ], 201);
    }

    public function AddToWishlists(Request $req)
    {

        $shops = DB::table('wishlists_shops')
            ->where("user_id", $req->user_id)
            ->where("shop_id", $req->shop_id)
            ->get();

        if (count($shops) > 0) {
            return response()->json([
                'message' => 'Your shop already exist on wishlist.',
                'messageAr' => "متجرك موجود بالفعل في قائمة الرغبات",
                "success" => true,
            ], 201);
        }
        $insert = DB::table('wishlists_shops')->insert(
            [
                'user_id' => $req->user_id,
                'shop_id' => $req->shop_id
            ]
        );

        if ($insert) {
            return response()->json([
                'message' => 'Shop added to wishlist successfully.',
                'messageAr' => "تم تمت إضافة المتجر إلى قائمة الرغبات بنجاح",
                "success" => true,
            ], 201);
        }

        return response()->json([
            'message' => 'Your shop not added to wishlist try again.',
            'messageAr' => "تم تمت إلم يتم إضافة متجرك إلى قائمة الرغبات حاول مرة أخرى",
            "success" => false,
        ], 201);
    }

    // public function getNotifications($id){
    //     $notifications = DB::table('notifications_mobile')->orderBy('created_at', 'desc')->get();

    //     $data = [];
    //     foreach($notifications as $notif){
    //         if($notif->for_all == 1){
    //             array_push($data, $notif);
    //         }else{
    //             $users = explode(",",$notif->users);
    //             if(in_array($id, $users)){
    //                 array_push($data, $notif);
    //             }
    //         }
    //     }

    //     return response()->json(['status'=>200, "success" => true, 'total'=>count($data), 'data'=>$data]);
    // }

    public function ShopsByTypeOffers($type)
    {
        $list_types = [];
        $types_offers = DB::table("offers_types")
            ->where("name", "like", $type)
            ->get();
        foreach ($types_offers as $tf) {
            array_push($list_types, $tf->id);
        }

        $shops = DB::table("shops")
            ->join("offers", "shops.id", "=", "offers.id_shop")
            ->leftJoin('uploads', "shops.logo", "=", "uploads.id")
            ->whereIn("offers.type_id", $list_types)
            ->select('shops.*', 'uploads.file_name as logo_url', 'uploads.external_link as external_link')
            ->groupBy('shops.id')
            ->get();

        return response()->json(['status' => 200, "success" => true, 'total' => count($shops), 'shops' => $shops]);
    }

    public function updateUser(Request $req)
    {
        $user = $req->user();
        if($req->has("email") && !empty($req->input("email"))){
            $existingUser = User::where("email",$req->input("email"))
                ->where("id",'<>',$user->id)->first();
            if(!empty($existingUser)){
                return response()->json([
                    'message' => 'Your information not updated! try again',
                    'messageAr' => "لم يتم تحديث المعلومات الخاصة بك! حاول مرة أخرى",
                    "success" => true,
                ], 201);
            }
        }
        if($req->has("phone") && !empty($req->input("phone"))){
            $existingUser = User::where("phone",'like',"%".$req->input("phone")."%")
                ->where("id",'<>',$user->id)->first();
            if(!empty($existingUser)){
                return response()->json([
                    'message' => 'Your information not updated! try again',
                    'messageAr' => "لم يتم تحديث المعلومات الخاصة بك! حاول مرة أخرى",
                    "success" => true,
                ], 201);
            }
        }
        $user->email = $req->email;
        $user->date_birth = $req->date_birth;
        $user->phone = $req->phone;

        return response()->json([
            'message' => 'Your information was updated successfully',
            'messageAr' => "تم تحديث المعلومات الخاصة بك بنجاح",
            "success" => true,
        ], 201);

    }

    public function updatePwd(Request $req)
    {
        $affected = DB::table("users")->where("id", $req->id)
            ->update(['password' => \bcrypt($req->pwd)]);

        if ($affected) {
            return response()->json([
                'message' => 'Your information was updated successfully',
                'messageAr' => "تم تحديث المعلومات الخاصة بك بنجاح",
                "success" => true,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Your information not updated! try again',
                'messageAr' => "لم يتم تحديث المعلومات الخاصة بك! حاول مرة أخرى",
                "success" => true,
            ], 201);
        }
    }



    public function updateLocationUser(Request $req)
    {
        $affected = DB::table("users")->where("id", $req->id)
            ->update([
                "country" => $req->country,
            ]);

        if ($affected) {
            return response()->json([
                'message' => 'Your information was updated successfully',
                'messageAr' => "تم تحديث المعلومات الخاصة بك بنجاح",
                "success" => true,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Your information not updated! try again',
                'messageAr' => "لم يتم تحديث المعلومات الخاصة بك! حاول مرة أخرى",
                "success" => true,
            ], 201);
        }
    }

    public function getCoupons($id)
    {
        $coupons = DB::table("coupons")->where("user_id", $id)->get();
        return response()->json([
            'coupons' => $coupons,
            "success" => true,
        ], 201);
    }

    public function getBranches($id)
    {
        $branches = DB::table("branches")->where("shop_id", $id)->get();
        return response()->json([
            'branches' => $branches,
            "success" => true,
        ], 201);
    }

    /**
     * Saim's Work
     */

    public function getNotifications($id)
    {
        $notifications = DB::table('notifications_mobile')->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($notifications as $notif) {
            if ($notif->for_all == 1) {
                $seen_by = $users = explode(",", $notif->seen_by);
                if (in_array($id, $seen_by)) {
                    $notif->seen = true;
                    array_push($data, $notif);
                } else {
                    $notif->seen = false;
                    array_push($data, $notif);
                }
                // array_push($data, $notif);
            } else {
                $users = explode(",", $notif->users);
                if (in_array($id, $users)) {
                    $seen_by = $users = explode(",", $notif->seen_by);
                    if (in_array($id, $seen_by)) {
                        $notif->seen = true;
                        array_push($data, $notif);
                    } else {
                        $notif->seen = false;
                        array_push($data, $notif);
                    }
                }
            }
        }
        return response()->json(['status' => 200, "success" => true, 'total' => count($data), 'data' => $data]);
    }

    public function getTerms($id)
    {
        $terms = DB::table('sellers_terms')->where('shop_id', $id)->first();

        return response()->json([
            // 'branches' => $branches,
            'terms' => $terms,
            "success" => true,
        ], 201);
    }

    public function getNearbyShops(Request $req)
    {
        $shops = DB::table('shops')
            ->where('categories', 'like', '%"' . $req->category_id . '"%')
            ->leftJoin('uploads', 'shops.logo', '=', 'uploads.id')
            ->select("shops.*", 'uploads.file_name as logo_url', 'uploads.external_link as external_link', \DB::raw("6371 * acos(cos(radians(" . $req->lat . "))
     * cos(radians(shops.latitude))
     * cos(radians(shops.longitude) - radians(" . $req->lng . "))
     + sin(radians(" . $req->lat . "))
     * sin(radians(shops.latitude))) AS distance"))
            ->having('distance', '<', 5)
            // ->select('shops.*', 'uploads.file_name as logo_url')
            ->get();

        return response()->json(['status' => 200, 'total_results' => count($shops), 'shops' => $shops]);
    }

    public function seenNotification(Request $req)
    {
        $id = $req->id;
        $notifications = DB::table('notifications_mobile')->orderBy('created_at', 'desc')->get();

        $data = [];
        $affected = false;
        foreach ($notifications as $notif) {
            $seen_by = explode(",", $notif->seen_by);
            if ($notif->for_all == 1 && !in_array($id, $seen_by)) {
                array_push($seen_by, $id);
                $seen_by = implode(",", $seen_by);
                $affected = DB::table("notifications_mobile")->where("id", $notif->id)
                    ->update([
                        "seen_by" => $seen_by,
                    ]);
            } else {
                $users = explode(",", $notif->users);
                // $seen_by = $users = explode(",",$notif->seen_by);
                if (in_array($id, $users) && !in_array($id, $seen_by)) {
                    array_push($seen_by, $id);
                    $seen_by = implode(",", $seen_by);
                    $affected = DB::table("notifications_mobile")->where("id", $notif->id)
                        ->update([
                            "seen_by" => $seen_by,
                        ]);
                }
            }
        }




        return response()->json([
            'message' => 'Your information was updated successfully',
            'messageAr' => "تم تحديث المعلومات الخاصة بك بنجاح",
            "success" => true,
        ], 201);
    }

    public function hyperpay(Request $req)
    {

        $url = "https://test.oppwa.com/v1/payments";
        if ($req->card_type == 'VISA') {
            $data = "entityId=" . env('HYPERPAYVISA_ENTITYID');
        } else if ($req->card_type == 'MASTER') {
            $data = "entityId=" . env('HYPERPAY_ENTITYID');
        } else if ($req->card_type == 'APPLEPAY') {
            $data = "entityId=" . env('HYPERPAYAPPLEPAY_ENTITYID');
        } else if ($req->card_type == 'AMEX') {
            $data = "entityId=" . env('HYPERPAY_ENTITYID');
        } else if ($req->card_type == 'MADA') {
            $data = "entityId=" . env('HYPERPAY_ENTITYID');
        }
        $data .= "&amount=" . $req->amount .
            "&currency=SAR" .
            "&paymentBrand=" . $req->card_type .
            // "&paymentType=DB" .
            "&card.number=" . $req->card_number .
            "&card.holder=" . $req->first_name . " " . $req->last_name .
            "&card.expiryMonth=" . $req->exp_month .
            "&card.expiryYear=" . $req->exp_year;

        if ($req->card_type == 'VISA') {
            $data .= "&card.cvv=" . $req->cvv .
                "&paymentType=DB" .
                "&standingInstruction.mode=INITIAL" .
                "&standingInstruction.source=CIT" .
                "&createRegistration=true";
        } else if ($req->card_type == 'MASTER') {
            $data .= "&card.cvv=" . $req->cvv . "&paymentType=DB";
        } else if ($req->card_type == 'APPLEPAY') {
            $data .= "&paymentType=PA" .
                "&threeDSecure.verificationId=ABiKYvXjhcB7AAc+K04XAoABFA==" .
                "&threeDSecure.eci=07" .
                "&applePay.source=web";
        } else if ($req->card_type == 'AMEX') {
            $data .= "&card.cvv=" . $req->cvv . "&paymentType=DB";
        } else if ($req->card_type == 'MADA') {
            $data .= "entityId=" . env('HYPERPAY_ENTITYID') . "&card.cvv=" . $req->cvv .
                "&paymentType=PA" .
                "&testMode=EXTERNAL";
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($req->card_type == 'VISA') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . env('HYPERPAYVISA_ACCESS_TOKEN')
            ));
        } else if ($req->card_type == 'MASTER') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN')
            ));
        } else if ($req->card_type == 'APPLEPAY') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . env('HYPERPAYAPPLEPAY_ACCESS_TOKEN')
            ));
        } else if ($req->card_type == 'AMEX') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN')
            ));
        } else if ($req->card_type == 'MADA') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . env('HYPERPAY_ACCESS_TOKEN')
            ));
        }
        // 	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //                   'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData);
        $responseData->success = true;
        return $responseData;
    }



    // end Saim's Work

    public function claimSubscriptionCoupon(ClaimCouponRequest $request)
    {
        $coupon = Coupon::where('code', $request->code)->where('type', 'subscription_base')->first();
        if ($coupon == null) return response()->json(['success' => false, 'message' => 'Coupon is invalid', 'messageAr' => 'القسيمة غير صالحة.', 'status' => 400]);

        if (strtotime(date('d-m-Y')) < $coupon->start_date || strtotime(date('d-m-Y')) > $coupon->end_date) {
            return response()->json(['success' => false, 'message' => 'Coupon has expired.', 'messageAr' => 'انتهت صلاحية القسيمة.', 'status' => 400]);
        }

        $couponSubscription = CouponUsage::where('user_id', $request->user_id)->where('coupon_id', $coupon->id)->first();
        if ($couponSubscription != null) return response()->json(['success' => false, 'message' => 'You have already used this coupon!', 'messageAr' => 'لقد استخدمت بالفعل هذه القسيمة!', 'status' => 400]);

        //$customerPackage = CustomerPackage::where('id', $request->package_id)->first();

        $amount = $request->amount;
        $discountAmount = $coupon->discount;
        if ($coupon->discount_type == 'percent') {
            $discountAmount = ($amount * $coupon->discount) / 100;
        }

        $totalAmount = $amount - $discountAmount;
        if ($totalAmount < 1) {
            return response()->json(['success' => false, 'message' => 'This discount Not allowed', 'messageAr' => 'هذا الخصم غير مسموح به', 'status' => 400]);
        }

        return response()->json([
            'message' => 'Coupon has been applied.',
            'messageAr' => 'تم تطبيق القسيمة.',
            'code' => $request->code,
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'total_amount' => floatval(number_format((float) $totalAmount, 2, '.', '')),
            'discount_amount' => floatval(number_format((float) $discountAmount, 2, '.', '')),
            'package_amount' => $amount,
            "success" => true,
            'status' => 200
        ], 200);
    }


    public function getSendGiftUrl(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "send_gift_url" => url('/send-gift',encrypt($user->id))
        ]);
    }
}
