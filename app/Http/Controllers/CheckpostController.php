<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CheckpostController extends Controller
{

    /* =================== Logout method =================== */
    public function logout($notice = null)
    {
        Session()->flush();
        $notice = $notice ?: 'You have successfully logout 🔐';
        return redirect('login')->with('notice', $notice);
    }
    /* =================== /Logout method =================== */

    /* ================ view login page ======================== */
    public function login()
    {
        Session()->pull('userID');
        return view('common.login');
    }
    /* ================ /view login page ======================== */

    /* ================ check login request or credentials ======================== */
    public function check_login_credentials(Request $request)
    {
        $request->validate([
            'email' => 'Required|email',
            'password' => 'Required|min:6|max:32'
        ]);
        try {
            // check user exist 
            $user = User::with('userinfo')->where('email', $request->email)->first();

            $user ?: throw new Exception("Credential not match.");

            // ckeck password match or not 
            $password = Hash::check($request->password, $user->password);

            $password ?: throw new Exception("Sorry Credential not match.");

            // check user is Authorized
            $user->userinfo->isAuth == 1 ?: throw new Exception('Sorry, you are not authorized to login.');
            // check user is active
            $user->isActive == 1 ?: throw new Exception('Sorry, your account is not active to login.');

            //put userID in session
            session()->put('userID', $user->id);
            session()->put('twoFA', false);

            // ckeck twoFA not active & login or continue next 
            if ($user->twoFA == 0) {
                session()->put('twoFA', true);
                session()->put('login', true);
                return redirect()->route('/')->with('success', 'Welcome to your account.');
            }
            //prepare an otp to send for 2fa verification
            $otp = $this->prepare_otp();

            $otp !== false ?: throw new Exception('Something went wrong, pls try again.');

            // get email subject from session if any 
            $subject = 'Two-factor authentication code for login';

            $mail = new MailController();
            $sendotp = $mail->sendotp('emails.sendotp', $otp, subject: $subject);

            $sendotp ?: throw new Exception('Failed to send otp, pls try again.');

            // redirect to login checkpost page to twoFA virification  
            $msg = 'We have sent a Two-factor authentication to your email : ' . $request->email;
            return redirect()->route('login.checkpost')->with('notice', $msg);

            //try end here
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from check_login_credentials@CheckpostController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            $error = $except->getMessage() ?: 'An error occurred while login.';
            return back()->with('error', $error);
        }
    }
    /* ================ /check login request or credentials ======================== */

    /* ================= prepare_otp ================= */
    public function prepare_otp($new_otp = true, $otp = null, $time_in_seconds = null,)
    {
        /* This function always return
            an otp => (OTP Token means it true) or 
            false => (problem) or 
            null => (already one otp send within time) 
        */
        $userID     = session()->get('userID');
        $otp        = $otp ?: rand(100000, 999999);
        $expiryTime = $time_in_seconds ?: time() + 1200; /* Add 1200 Seconds(20 Minutes) with current time number  */
        try {
            $results = DB::table('twofatokens')->where('uid', '=', $userID)->first();
            if (!$results) {
                $insert = DB::insert('insert into twofatokens (uid, token, expiryTime) values (?, ?, ?)', [$userID, $otp, $expiryTime]);
                $insert ?: throw new Exception('OTP not inserted to Database');
                return   $otp;
            }

            $id = $results->id;
            $dbTime = $results->expiryTime;
            $validity = $results->validity;
            $now = time();

            // check conditions to update existing otp  
            if (($dbTime < $now) or ($validity == 0) or ($new_otp == true)) {
                $update = DB::table('twofatokens')->where('id', $id)->update(['token' => $otp, 'expiryTime' => $expiryTime, 'validity' => 1]);
                $update ?: throw new Exception('OTP not updated to Database');
                return   $otp;
            }
            return null;
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from prepare_otp@RegistrationController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return false;
        }
    }
    /* ================= /prepare_otp ================= */

    /* ================= send TwoFA code again ================= */
    public function sendcodeagain()
    {
        // save a token to authorize
        $userID     = session()->get('userID');
        $otp        = rand(100000, 999999);
        $expiryTime = time() + 1200; /* Add 1200 Seconds(20 Minutes) with current time number  */

        DB::table('twofatokens')->where('uid', $userID)->update(['token' => $otp, 'expiryTime' => $expiryTime, 'validity' => 1]);

        // send OTP to email 
        $subject = 'New verification code';
        $mail = new MailController();
        $send = $mail->sendotp('emails.sendotp', $otp, subject: $subject);

        return  $send ? "<i class='bx bx-mail-send'></i> Code sent to your Email" : 'sending failed';
    }
    /* ================= /send TwoFA code again ================= */

    /* ================= checking login 2fa ================= */
    public function check_login_2fa(Request $request)
    {

        $request->validate(['otp' => 'required|min:6']);

        try {
            //checking otp correct or not
            $otp = $this->checkotp($request->otp);
            $otp == true ?: throw new Exception($otp);

            // set twoFA & login session true.
            session()->put('twoFA', true);
            session()->put('login', true);

            $user = DB::table('users')->select('users.isAdmin')->where('users.id', '=', session()->get('userID'))->first();

            // possible redirect @ admin login case
            if ($user->isAdmin == 1) {
                session()->put('adminLogin', true);
                return redirect()->route('admin.dashboard')->with('notice', 'Welcome to Admin Panel 🙂');
            }
            // possible redirect @ normal user login case
            return redirect()->route('user.home')->with('notice', "Hello $user->name 🎉");
        } catch (Exception $except) {
            return back()->with('error', $except->getMessage());
        }
    }
    /* ================= checking login 2fa ================= */

    /* ================= checking otp ================= */
    public function checkotp($otp)
    {
        /* this method always return true or error as string */
        try {
            $userID = session()->get('userID') ?: null;
            // check otp with db 
            $results = DB::table('twofatokens')
                ->where('uid', '=', $userID)
                ->where('token', '=', $otp)
                ->where('validity', '=', 1)
                ->first();

            $results ?: throw new Exception("Invalid OTP ⚠️");

            // check otp expiry time 
            $dbTime = $results->expiryTime;
            $now = time();
            $dbTime > $now ?: throw new Exception("This OTP already expired ⛔");

            // update token validity as false 
            $id = $results->id;
            DB::table('twofatokens')->where('id', '=', $id)->update(['validity' => 0]);

            return true;
            //
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from checkotp@CheckpostController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return $e->getMessage();
        }
    }
    /* ================= /checking otp ================= */

    /* ============= password_reseting_check ================= */
    public function password_reseting_check(Request $request)
    {

        $request->validate(['email'    => 'required|email|max:100']);

        try {
            $user = User::with('userinfo')->where('email', $request->email)->first();

            $user ?: throw new Exception('Incorrect email address.');

            // check user is authorized
            if ($user->userinfo->isAuth != 1) {
                return back()->with('error', 'Sorry, your account is unauthorized.');
            }
            // check user is active
            if ($user->isActive != 1) {
                return back()->with('error', 'Sorry, you are not is not active.');
            }
            session()->put('userID', $user->id);
            session()->put('twoFA', false);

            //prepare an otp to send for 2fa verification
            $otp = $this->prepare_otp(); //need first argument as false

            if ($otp === false) {
                throw new Exception('Something went wrong, please try again.');
            }
            if ($otp == null) {
                // as otp sent within 20 min, page redirect to password checkpost page to twoFA virification  
                return redirect()->route('password.checkpost')->with('notice', 'Otp already sent, please check yor email.');
            }

            // set subject & send email
            $subject = 'OTP for password reset';
            $mail = new MailController();
            $sendotp = $mail->sendotp('emails.sendotp', $otp, subject: $subject);

            $sendotp ?: throw new Exception('otp sending failed, pls try again.');

            // redirect to login checkpost page to twoFA virification  
            return redirect()->route('password.checkpost')->with('notice', 'Please find the email we sent with a six digit otp.');

            //
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from password_reseting_check@CheckpostController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return back()->with('error', $except->getMessage());
        }
    }
    /* ============= /password_reseting_check ================= */

    /* ============= /reset_password ================= */
    public function reset_password(Request $request)
    {
        $request->validate([
            'otp' => 'Required',
            'password' => 'Required|min:6',
            'confirmpassword' => 'Required|min:6'
        ]);
        try {
            $userID = session()->get('userID');
            //check otp correct or not
            $otp = $this->checkotp($request->otp);

            $otp ?: throw new Exception($otp);

            //check passwords are same
            $request->password === $request->confirmpassword ?: throw new Exception('Password must be same.');

            //Make password hash 
            $password = Hash::make($request->password);

            // update users password  
            DB::table('users')->where('id', '=', $userID)->update(['password' => $password]);

            // set twoFA & login session true.
            session()->put('twoFA', true);
            session()->put('login', true);

            // possible redirect @ admin login case
            $user = new UserController();
            if ($user->admin == true) {
                session()->put('adminLogin', true);
                return redirect()->route('admin.dashboard')->with('notice', 'Welcome to Admin Panel 🙂');
            }
            // possible redirect @ normal user login case
            return redirect()->route('user.home')->with('notice', "Hello $user->name 🎉");
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from reset_password@CheckpostController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
    /* ============= /reset_password ================= */
}
