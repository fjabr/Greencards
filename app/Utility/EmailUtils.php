<?php
namespace App\Utility;

use Mail;

class EmailUtils
{
    public static function sendMail($to, $subject, $data, $attachment) {
        try {
            if($attachment !== null){
                Mail::send('email.email', $data, function ($messages) use ($to, $subject, $attachment) {
                    $messages->to($to);
                    $messages->subject($subject)->attachData($attachment['file'], $attachment['name']);
                });

            }else{
                Mail::send('email.email', $data, function ($messages) use ($to, $subject) {
                    $messages->to($to);
                    $messages->subject($subject);
                });
            }
        } catch (\Exception $exception) {
            //echo $exception->getMessage();
        }
    }
}

?>
