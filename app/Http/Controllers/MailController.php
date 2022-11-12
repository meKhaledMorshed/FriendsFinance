<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    private array $emailData = [];

    function __construct()
    {
        $result = new EntityCoreController;
        $entity = $result->entity;

        $this->emailData['senderEmail'] = $entity->email;
        $this->emailData['senderName'] = $entity->name;
    }

    // send email 
    private function sendemail($template)
    {
        try {
            Mail::send($template, $this->emailData, function ($message) {
                $message->to($this->emailData['receiverEmail'], $this->emailData['receiverName'])->subject($this->emailData['subject']);
                $message->from($this->emailData['senderEmail'], $this->emailData['senderName']);
            });
            return true;
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from sendemail @ MialController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return false;
        }
    }
    // email for sending otp 
    public function sendotp(string $template, $token, $receiverEmail = null, $receiverName = null, $subject = null)
    {
        try {

            $userdata = new UserController();
            $user     = $userdata->user;

            $this->emailData['token']         = $token;
            $this->emailData['receiverEmail'] = $receiverEmail ?? $user->email;
            $this->emailData['receiverName']  = $receiverName ?? $user->name;
            $this->emailData['subject']       = $subject ?: 'Two-fector authentication code';

            $send = $this->sendemail($template);

            $send ?: throw new Exception('OTP not send');
            return true;
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from sendotp @ MialController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return false;
        }
    }

    // email for sending temporary password to new user 
    // public function sendUserCreated($tempPass, $receiverEmail, $receiverName, $subject = null)
    // {
    //     try {
    //         $this->emailData['tempPass']      = $tempPass;
    //         $this->emailData['receiverEmail'] = $receiverEmail;
    //         $this->emailData['receiverName']  = $receiverName;
    //         $this->emailData['subject']       = $subject ?: 'Wellcome to ' . config('app.name');

    //         Mail::send('emails.sendUserCreated', $this->emailData, function ($message) {
    //             $message->to($this->emailData['receiverEmail'], $this->emailData['receiverName'])->subject($this->emailData['subject']);
    //             $message->from($this->emailData['senderEmail'], $this->emailData['senderName']);
    //         });
    //         return true;
    //     } catch (Exception $except) {
    //         return false;
    //     }
    // }
}
