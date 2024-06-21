<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DeletedUser;
use App\Notifications\AppEmailVerificationNotification;
use Hash;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Socialite;
use App\Models\Cart;
use App\Rules\Recaptcha;
use App\Services\SocialRevoke;
use Illuminate\Validation\Rule;
use Log;
use Mail;
use Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $messages = array(
            'name.required' => translate('Name is required'),
            'email_or_phone.required' => $request->register_by == 'email' ? translate('Email is required') : translate('Phone is required'),
            'email_or_phone.email' => translate('Email must be a valid email address'),
            'email_or_phone.numeric' => translate('Phone must be a number.'),
            'email_or_phone.unique' => $request->register_by == 'email' ? translate('The email has already been taken') : translate('The phone has already been taken'),
            'password.required' => translate('Password is required'),
            'password.confirmed' => translate('Password confirmation does not match'),
            'password.min' => translate('Minimum 6 digits required for password'),
            'country.required' => translate('Country is required'),
        );
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:6|confirmed',
            'email_or_phone' => [
                'required',
                Rule::when($request->register_by === 'email', ['email', 'unique:users,email']),
                Rule::when($request->register_by === 'phone', ['numeric', 'unique:users,phone']),
            ],
            'country'=>'required'
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()
            ]);
        }

        $user = new User([
            'name' => $request->name,
            'gender' => $request->gender,
            'date_birth' => $request->date_birth,
            'nationality' => $request->nationality,
            'country' => $request->country,
            'password' => bcrypt($request->password),
            'user_type' => "customer",
            'verification_code' => rand(100000, 999999),
        ]);

        if( $request->register_by == 'email'){

            $user->email = $request->email_or_phone ;

        }else if($request->register_by == 'phone'){
            $user->phone = $request->email_or_phone ;

        }

        $user->email_verified_at = null;
        if ($user->email != null) {
            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            }
        }

        if ($user->email_verified_at == null) {
            if ($request->register_by == 'email') {
                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                }
            } else {
                $otpController = new OTPVerificationController();
                $otpController->send_code($user);
            }
        }

        $user->save();

        //create token
        $user->createToken('tokens')->plainTextToken;

        return response()->json([
            'result' => true,
            'message' => translate('Registration Successful. Please verify and log in to your account.'),
            'user_id' => $user->id
        ], 201);
    }

    public function resendCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->verification_code = rand(100000, 999999);

        if ($request->verify_by == 'email') {
            $user->notify(new AppEmailVerificationNotification());
        } else {
            $otpController = new OTPVerificationController();
            $otpController->send_code($user);
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => translate('Verification code is sent again'),
        ], 200);
    }

    public function confirmCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_code = null;
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your account is now verified.Please login'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Code does not match, you can request for resending the code'),
            ], 200);
        }
    }

    public function login(Request $request)
    {
        /*$request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
        'remember_me' => 'boolean'
        ]);*/

        $delivery_boy_condition = $request->has('user_type') && $request->user_type == 'delivery_boy';
        $seller_condition = $request->has('user_type') && $request->user_type == 'seller';

        if ($delivery_boy_condition) {
            $user = User::whereIn('user_type', ['delivery_boy'])
                ->where('email', $request->email)
                ->orWhere('phone', $request->email)
                ->first();
        } elseif ($seller_condition) {
            $user = User::whereIn('user_type', ['seller'])
                ->where('email', $request->email)
                ->orWhere('phone', $request->email)
                ->first();
        } else {
            $user = User::whereIn('user_type', ['customer'])
                ->where('email', $request->email)
                ->orWhere('phone', $request->email)
                ->first();
        }

        // if (!$delivery_boy_condition) {
        if (!$delivery_boy_condition && !$seller_condition) {
            if (\App\Utility\PayhereUtility::create_wallet_reference($request->identity_matrix) == false) {
                return response()->json(['result' => false, 'message' => 'Identity matrix error', 'user' => null], 401);
            }
        }

        if ($user != null) {
            if (!$user->banned) {
                if (Hash::check($request->password, $user->password)) {

                    if ($user->email_verified_at == null) {
                        return response()->json(['result' => false, 'message' => translate('Please verify your account'), 'user' => null], 401);
                    }
                    return $this->loginSuccess($user);
                } else {
                    return response()->json(['result' => false, 'message' => translate('Unauthorized'), 'user' => null], 401);
                }
            } else {
                return response()->json(['result' => false, 'message' => translate('User is banned'), 'user' => null], 401);
            }
        } else {
            return response()->json(['result' => false, 'message' => translate('User not found'), 'user' => null], 401);
        }

    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->is_subscribed = isSubscribedUser($user);
        return response()->json($user);
    }

    public function logout(Request $request)
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged out')
        ]);
    }

    public function delete(Request $request)
    {

        $user = request()->user();
        $userDeleted = new DeletedUser();
        $userDeleted->referred_by = $user->referred_by;
        $userDeleted->provider_id = $user->provider_id;
        $userDeleted->user_type = $user->user_type;
        $userDeleted->name = $user->name;
        $userDeleted->email = $user->email;
        $userDeleted->email_verified_at = $user->email_verified_at;
        $userDeleted->verification_code = $user->verification_code;
        $userDeleted->new_email_verificiation_code = $user->new_email_verificiation_code;
        $userDeleted->password = $user->password;
        $userDeleted->remember_token = $user->remember_token;
        $userDeleted->device_token = $user->device_token;
        $userDeleted->avatar = $user->avatar;
        $userDeleted->avatar_original = $user->avatar_original;
        $userDeleted->address = $user->address;
        $userDeleted->country = $user->country;
        $userDeleted->state = $user->state;
        $userDeleted->city = $user->city;
        $userDeleted->postal_code = $user->postal_code;
        $userDeleted->phone = $user->phone;
        $userDeleted->balance = $user->balance;
        $userDeleted->banned = $user->banned;
        $userDeleted->referral_code = $user->referral_code;
        $userDeleted->customer_package_id = $user->customer_package_id;
        $userDeleted->remaining_uploads = $user->remaining_uploads;
        $userDeleted->save();
        $user->tokens()->delete();
        User::find($user->id)->delete();
        return response()->json([
            'result' => true,
            'message' => translate('Successfully deleted user')
        ]);
    }

    public function socialLogin(Request $request)
    {
        if (!$request->provider) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
                'user' => null
            ]);
        }

        //
        Log::info("social providfer  =" . $request->social_provider);
        Log::info("social accouont = " . $request->provider);
        Log::info( $request->all());
        switch ($request->social_provider) {
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile', 'email']);
                break;
            case 'apple':
                $existingUserByProviderId = User::where('provider_id', $request->provider)->first();
                if (!empty($existingUserByProviderId)) {
                    return $this->loginSuccess($existingUserByProviderId);
                } else {
                    if (empty($request->email)) {
                        return response()->json(['result' => false, 'message' => translate('No social account matches'),'message_ar'=>'لا يوجد أي حساب مطابق', 'user' => null]);
                    }
                    $existingUserByMail = User::where('email', $request->email)->first();
                    if ($existingUserByMail) {
                        $existingUserByMail->provider_id = $request->provider;
                        $existingUserByMail->save();
                        return $this->loginSuccess($existingUserByMail);
                    } else {
                        $user = $this->register($request);
                        return $this->loginSuccess($user);
                    }
                }
                break;
            default:
                $social_user = null;
        }
        if ($social_user == null) {
            return response()->json(['result' => false, 'message' => translate('No social provider matches'),'message_ar'=>'لا يوجد أي حساب مطابق',  'user' => null]);
        }


        //

        $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

        if ($existingUserByProviderId) {
            return $this->loginSuccess($existingUserByProviderId);
        } else {

            $existingUserByMail = User::where('email', $request->email)->first();
            if ($existingUserByMail) {
                //return $this->loginSuccess($existingUserByMail);
                return response()->json(['result' => false, 'message' => translate('You can not login with this provider'), 'user' => null]);
            } else {

                $user = $this->register($request);
            }
        }
        return $this->loginSuccess($user);
    }

    public function register($request)
    {
        $user = new User;
        $user->email = $request->email;
        $user->provider_id = $request->provider;
        $user->name = $request->input("name");
        $user->email_verified_at = Carbon::now();
        $user->password = '123456';

        $user->save();
        $data = [
            "email" => $user->email,
            "password" => $user->password
        ];

        $to = $user->email;

        try {
            Mail::send('email.signup', $data, function ($messages) use ($to) {
                $messages->to($to);
                $messages->subject('WELCOME TO YOUR GREEN CARD');
            });
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $user;

    }
    protected function loginSuccess($user)
    {
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged in'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar_original),
                'phone' => $user->phone,
                'customer_package_id' => $user->customer_package_id,
                'end_sub_date' => $user->end_sub_date,
                'start_sub_date' => $user->start_sub_date,
                'is_subscribed' =>isSubscribedUser($user)
            ]
        ]);
    }


    public function account_deletion()
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }

        // if (auth()->user()->provider && auth()->user()->provider != 'apple') {
        //     $social_revoke =  new SocialRevoke;
        //     $revoke_output = $social_revoke->apply(auth()->user()->provider);

        //     if ($revoke_output) {
        //     }
        // }

        $auth_user = auth()->user();
        $auth_user->tokens()->where('id', $auth_user->currentAccessToken()->id)->delete();
        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        return response()->json([
            "result" => true,
            "message" => translate('Your account deletion successfully done')
        ]);
    }
}
