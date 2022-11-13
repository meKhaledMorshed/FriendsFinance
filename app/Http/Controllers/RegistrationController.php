<?php

namespace App\Http\Controllers;

use App\Models\Select_option;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{

    public function createentity(Request $request)
    {
        $request->validate([
            'name'    => 'required|max:255',
            'email'   => 'required|email|max:100',
            'dob'     => 'required|date',
            'address' => 'min:10|max:255|nullable',
            'logo'    => 'required|image|mimes:jpg,jpeg,png,PNG | max:10240'
        ]);

        try {

            $file = file_exists('assets/json/select_options.json');
            if ($file) {
                Select_option::truncate();
                $options = file_get_contents('assets/json/select_options.json');
                $options = json_decode($options, true);

                foreach ($options as $option) {
                    DB::table('select_options')->insert($option);
                }
            }

            $entity = [];
            // put all data in array 
            $entity['name']            = $request->name;
            $entity['email']           = $request->email;
            $entity['establishedDate'] = $request->dob;
            $entity['address']         = $request->address;
            $entity['logo']            = 'entity_logo.' . $request->logo->extension();

            $json_encoded = json_encode($entity, JSON_PRETTY_PRINT);
            $save_data = file_put_contents('assets/json/entity_data.json', $json_encoded);

            if (!$save_data) {
                throw new Exception('Data insert not successful.');
            }

            // check move image to server folder 
            $logo_upload = $request->logo->move(public_path('assets/img/logo'), $entity['logo']);

            if (!$logo_upload) {
                throw new Exception('Logo upload failed.');
            }

            return redirect('default-admin');
        } catch (Exception $except) {

            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from createentity@RegistrationController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());

            return back()->with('notice', $except->getMessage());
        }
    }
    // default admin creating page 
    public function defaultadmin()
    {
        $data = new EntityCoreController();
        if (!isset($data->entity->name)) {
            return Redirect('addentity');
        }

        $genders = Select_option::where('parentValue', 'Gender')->get();
        $cccs = Select_option::where('parentValue', 'CCC')->get();

        return view('addentity.default-admin', compact('genders', 'cccs'));
    }

    public function createadmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:100',
            'password' => 'required|min:6|max:32',
            'dob'      => 'required|date',
            'ccc'      => 'required',
            'tel'      => 'required|min:7|max:15',
            'gender'   => 'required',
            'photo'    => 'required|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400'
        ]);

        try {

            DB::beginTransaction();

            // array of users table data 
            $user = [
                'username'     => str_replace(" ", "", $request->name),
                'email'        => $request->email,
                'mobile'       => $request->tel,
                'ccc'          => $request->ccc,
                'password'     => Hash::make($request->password),
                'twoFA'        => 1,
                'isAdmin'      => 1,
                'isActive'     => 1
            ];

            // save data to users table
            $uid = DB::table('users')->insertGetId($user);

            // array of user infos 
            $User_infos = [
                'uid'                => $uid,
                'name'               => $request->name,
                'birthday'           => $request->dob,
                'gender'             => $request->gender,
                'photo'              => $user['username'] . '_' . time() . '.' . $request->photo->extension(),
                'remarks'            => 'Deafult User & Admin',
                'insertedBy'         => $uid,
                'isAuth'             => 1,
                'authBy'             => $uid
            ];

            // save data to User_infos table
            DB::table('User_infos')->insert($User_infos) ?? throw new Exception('User data not inserted.');
            // move photo to server folder
            $request->photo->move(public_path('assets/photos'), $User_infos['photo']);

            $address = [
                'uid'           => $uid,
                'house'         => '-',
                'area'          => '-',
                'postOffice'    => '-',
                'policeStation' => '-',
                'district'      => '-',
                'country'       => '-',
                'type'          => 'Present',
                'isActive'      => 0,
                'insertedBy'    => $uid
            ];

            DB::table('user_addresses')->insert($address) ?: throw new Exception('Present Address not inserted.');
            $address['type'] = 'Permanent';
            DB::table('user_addresses')->insert($address) ?: throw new Exception('Permanent Address not inserted.');

            // save data to document table
            $doc = [
                'uid'        => $uid,
                'docName'    => 'National ID Card',
                'type'       => 'NID',
                'insertedBy' => $uid
            ];

            DB::table('user_documents')->insert($doc) ?: throw new Exception('NID information not inserted.');

            // array of branches table 
            $branch = [
                'branchName' => 'Core Branch',
                'type'       => 'Core',
                'isActive'   => 1,
                'insertedBy' => $uid,
                'isAuth'     => 1,
                'authBy'     => $uid
            ];

            // save data to branches table
            $brID = DB::table('branches')->insertGetId($branch) ?? throw new Exception('branches data not inserted.');

            // array of branches table 
            $title = [
                'type'       => 'Elected',
                'isActive'   => 1,
                'insertedBy' => $uid,
                'isAuth'     => 1,
                'authBy'     => $uid
            ];
            $titles = ['Chairman', 'MD', 'CEO'];

            for ($i = 0; $i < count($titles); $i++) {

                $title['title'] = $titles[$i];

                // save admin_titles (designations)
                $titleID = DB::table('admin_titles')->insertGetId($title) ?? throw new Exception('admin_titles data not inserted.');
            }
            // array of admins  table 
            $admin = [
                'uid'        => $uid,
                'titleID'    => $titleID,
                'branchID'   => $brID,
                'role'       => 'Master',
                'assignDate' => date("Y-m-d"),
                'remarks'    => 'The default admin',
                'isActive'   => 1,
                'insertedBy' => $uid,
                'isAuth'     => 1,
                'authBy'     => $uid
            ];

            // save data to admins  table
            $adminID = DB::table('admins')->insertGetId($admin) ?? throw new Exception('Admin not created.');

            $permit = [
                'adminID'      => $adminID,
                'readPermit'   => 1,
                'writePermit'  => 1,
                'editPermit'   => 1,
                'deletePermit' => 1,
                'permitBy'     => $uid
            ];

            // save permissions 
            DB::table('permissions')->insert($permit) ?? throw new Exception('Permissions not created.');

            // Resarve account_categories array
            $categories = [
                'isActive'   => 0,
                'insertedBy' => $uid,
                'isAuth'     => 1,
                'authBy'     => $uid
            ];
            for ($i = 1; $i <= 10; $i++) {

                $categories['category'] = "Reserve Category $i";

                // save account_categories  
                DB::table('account_categories')->insert($categories) ?? throw new Exception('Resarve categories not added.');
            }

            // code.. to hold session for login
            session()->put('userID', $uid);
            session()->put('twoFA', false);

            $checkpost = new CheckpostController();
            $otp = $checkpost->prepare_otp();

            if ($otp === false) {
                throw new Exception('Something went wrong, pls try again.');
            }
            // get email subject from session if any 
            $subject = 'Confirmation code for your account login';

            $mail = new MailController();
            $sendotp = $mail->sendotp('emails.sendotp', $otp, subject: $subject);
            $sendotp ?: throw new Exception('Failed to send otp, pls try again.');

            // Data in all table save successful 
            DB::commit();

            // redirect to login checkpost page 
            $notice = "We have sent an email with your code. Please check your email : $request->email  .";
            return redirect()->route('login.checkpost')->with('notice', $notice);
        } catch (Exception $except) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from createadmin@RegirtrationController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            $notice = "Data insert not successful. " . $except->getMessage();
            return back()->with('notice', $notice);
        }
    }
}
