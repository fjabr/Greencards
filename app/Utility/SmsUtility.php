<?php
namespace App\Utility;

use App\Models\SmsTemplate;
use App\Models\User;
use Log;

class SmsUtility
{
    public static function phone_number_verification($user = '')
    {
        $sms_template   = SmsTemplate::where('identifier','phone_number_verification')->first();
        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[code]]', $user->verification_code, $sms_body);
        $sms_body       = str_replace('[[site_name]]', env('APP_NAME'), $sms_body);
        $template_id    = $sms_template->template_id;
        try {
            return sendSMS($user->phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }

    public static function password_reset($user = '')
    {
        $sms_template   = SmsTemplate::where('identifier','password_reset')->first();
        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[code]]', $user->verification_code, $sms_body);
        $template_id    = $sms_template->template_id;
        try {
            sendSMS($user->phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }

    public static function order_placement($phone='', $order='')
    {
        $sms_template   = SmsTemplate::where('identifier','order_placement')->first();
        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[order_code]]', $order->code, $sms_body);
        $template_id    = $sms_template->template_id;
        try {
            sendSMS($phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }

    public static function delivery_status_change($phone='', $order)
    {
        $sms_template   = SmsTemplate::where('identifier','delivery_status_change')->first();
        $sms_body       = $sms_template->sms_body;
        $delivery_status = translate(ucfirst(str_replace('_', ' ', $order->delivery_status)));

        $sms_body       = str_replace('[[delivery_status]]', $delivery_status, $sms_body);
        $sms_body       = str_replace('[[order_code]]', $order->code, $sms_body);
        $template_id    = $sms_template->template_id;

        try {
            sendSMS($phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }

    public static function payment_status_change($phone='', $order='')
    {
        $sms_template   = SmsTemplate::where('identifier','payment_status_change')->first();
        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[payment_status]]', $order->payment_status, $sms_body);
        $sms_body       = str_replace('[[order_code]]', $order->code, $sms_body);
        $template_id    = $sms_template->template_id;
        try {
            sendSMS($phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }

    public static function assign_delivery_boy($phone='', $code='')
    {
        $sms_template   = SmsTemplate::where('identifier','assign_delivery_boy')->first();
        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[order_code]]', $code, $sms_body);
        $template_id    = $sms_template->template_id;
        try {
            sendSMS($phone, env('APP_NAME'), $sms_body, $template_id);
        } catch (\Exception $e) {

        }
    }
    public static function sale_new_sub(User $user, $password)
    {
        $sms_template   = SmsTemplate::where('identifier','sale_new_sub')->first();

        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[app_store]]',get_setting('greenCard_app_store_link') , $sms_body);
        $sms_body       = str_replace('[[play_store]]',get_setting('greenCard_play_store_link') , $sms_body);
        $sms_body       = str_replace('[[password]]', $password, $sms_body);

        $template_id    = $sms_template->template_id;
        try {
            Log::info("Sending SMS to user : ".$user->phone);

            return sendSMS($user->phone, env('greenCard_app_store_link'), $sms_body, $template_id) ;
        } catch (\Exception $ex) {
            Log::error($ex);
            return false;

        }
    }


    public static function send_reset_password(User $user, $password)
    {
        $sms_template   = SmsTemplate::where('identifier','sale_new_sub')->first();

        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[app_store]]',get_setting('greenCard_app_store_link') , $sms_body);
        $sms_body       = str_replace('[[play_store]]',get_setting('greenCard_play_store_link') , $sms_body);
        $sms_body       = str_replace('[[password]]', $password, $sms_body);
        $sms_body       = str_replace('[[phone]]', $user->phone, $sms_body);

        $template_id    = $sms_template->template_id;
        try {
            Log::info("Sending SMS to user : ".$user->phone);

            return sendSMS($user->phone, env('greenCard_app_store_link'), $sms_body, $template_id) ;
        } catch (\Exception $ex) {
            Log::error($ex);
            return false;

        }
    }

    public static function sent_invite_to_family_member(User $user,User $parrent, $password)
    {
        $sms_template   = SmsTemplate::where('identifier','add_family_member')->first();

        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[app_store]]',get_setting('greenCard_app_store_link') , $sms_body);
        $sms_body       = str_replace('[[play_store]]',get_setting('greenCard_play_store_link') , $sms_body);
        $sms_body       = str_replace('[[password]]', $password, $sms_body);
        $sms_body       = str_replace('[[phone]]', $user->phone, $sms_body);
        $sms_body       = str_replace('[[parrent_name]]', $parrent->name, $sms_body);

        $template_id    = $sms_template->template_id;
        try {
            Log::info("Sending SMS 'sent_invite_to_family_member' to user : ".$user->phone);

            return sendSMS($user->phone, env('greenCard_app_store_link'), $sms_body, $template_id) ;
        } catch (\Exception $ex) {
            Log::error($ex);
            return false;

        }
    }
    public static function subscripiton_payment_suceess($user)
    {
        $sms_template   = SmsTemplate::where('identifier','subscripiton_payment_suceess')->first();

        $sms_body       = $sms_template->sms_body;
        $sms_body       = str_replace('[[app_store]]',get_setting('greenCard_app_store_link') , $sms_body);
        $sms_body       = str_replace('[[play_store]]',get_setting('greenCard_play_store_link') , $sms_body);

        $template_id    = $sms_template->template_id;
        try {
            Log::info("Sending SMS 'subscripiton_payment_suceess' to user : ".$user->phone);
            return sendSMS($user->phone, env('greenCard_app_store_link'), $sms_body, $template_id) ;
        } catch (\Exception $ex) {
            Log::error($ex);
            return false;

        }
    }


}

?>
