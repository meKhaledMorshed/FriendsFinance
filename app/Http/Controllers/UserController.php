<?php

namespace App\Http\Controllers;

use App\Models\Select_option;
use App\Models\User;
use App\Models\User_document;
use App\Models\User_info;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public $user;

    function __construct($requestedUserID = null)
    {

        $uID = $requestedUserID ?: $this->activeUserID();

        $user = User::find($uID);
        if ($user) {
            $userData = [];
            $userData['userID'] = $user->id;
            $userData['email']  = $user->email;
            $userData['mobile'] = $user->ccc . $user->mobile;
            $userData['admin']  = $user->isAdmin == 1 ?? false;
            $userData['name']   = $user->userinfo->name;
            $userData['photo']  = $user->userinfo->photo;

            $this->user = (object) $userData;
        }
    }

    private function activeUserID()
    {
        return session()->get('userID');
    }


    // ============================================================== Start create ======================================================================


    // return view of user create page 
    public function userForm()
    {
        $genders = Select_option::where('group', 'Gender')->where('type', 'Option')->where('isActive', 1)->where('isAuth', 1)
            ->orderBy('optionName', 'asc')->get(['optionName', 'optionValue']);

        return view('backend.user.create', compact('genders'));
    }

    // handale user create request 
    public function create(Request $request, $scope = null)
    {
        try {
            $keys = ['new', 'contact', 'document'];

            if (in_array($scope, $keys) && $scope === 'new') {
                return $this->createNewUser($request);
            }
            if (in_array($scope, $keys) && $scope === 'contact') {
                return $this->createUserContact($request);
            }
            if (in_array($scope, $keys) && $scope === 'document') {
                return $this->createUserDocument($request);
            }

            throw new Exception('Incorrect url parameter.');
            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from create@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Data not Created. Please check input fields are correct', 400); //400 Bad Request
        }
    }

    // for new user creation 
    private function createNewUser($request)
    {
        $request->validate([
            'name'            => 'nullable|min:4|max:40',
            'dob'             => 'nullable|date|after:01/01/1970|before:tomorrow',
            'gender'          => 'nullable|exists:select_options,optionValue',
            'email'           => 'nullable|email|max:100',
            'ccc'             => 'nullable|exists:select_options,optionValue',
            'mobile'          => 'nullable|regex:/[0-9]{8,14}/',
            'profession'      => 'nullable|exists:select_options,optionValue',
            'nidNumber'       => 'nullable|regex:/[A-Za-z0-9]{8,25}/',
            'father'          => 'nullable|min:3|max:40',
            'mother'          => 'nullable|min:3|max:40',
            'spouse'          => 'nullable|min:3|max:40',

            'pCountry'        => 'nullable|exists:select_options,optionValue',
            'pDistrict'       => 'nullable|exists:select_options,optionValue',
            'pPoliceStation'  => 'nullable|exists:select_options,optionValue',
            'pPostOffice'     => 'nullable|exists:select_options,optionValue',
            'pHouse'          => 'nullable|max:100',
            'pArea'           => 'nullable|max:100',

            'sameAddress'     => 'nullable|boolean',

            'fCountry'        => 'nullable|exists:select_options,optionValue',
            'fDistrict'       => 'nullable|exists:select_options,optionValue',
            'fPoliceStation'  => 'nullable|exists:select_options,optionValue',
            'fPostOffice'     => 'nullable|exists:select_options,optionValue',
            'fHouse'          => 'nullable|max:100',
            'fArea'           => 'nullable|max:100',

            'nid'             => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
            'signature'       => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
            'remarks'         => 'nullable|string|max:255'

        ]);

        try {

            $inputs = ['name' => 'Fullname', 'dob' => 'Date of Birth', 'gender' => 'Gender', 'email' => 'Email', 'ccc' => 'Country calling code', 'mobile' => 'Mobile Number', 'profession' => 'Profession', 'father' => 'Father name', 'mother' => 'Mother name', 'pCountry' => 'Country', 'pDistrict' => 'District', 'pPoliceStation' => 'Police Station', 'pPostOffice' => 'Post Office', 'pHouse' => 'House name or number', 'nid' => 'NID Card', 'photo' => 'Photo', 'signature' => 'Signature'];

            if (!isset($request->sameAddress)) {
                $fAddrinputs = ['fCountry' => 'Permanent Country', 'fDistrict' => 'Permanent District', 'fPoliceStation' => 'Permanent Police Station', 'fPostOffice' => 'Permanent Post Office', 'fHouse' => 'Permanent House name or number'];
                $inputs = array_merge($inputs, $fAddrinputs);
            }

            foreach ($inputs as $input => $text) {
                if ($request->$input == null) {
                    return response($text . ' is required', 400);
                }
            }

            $ck_exist = User::where('email', $request->email)->count();
            if ($ck_exist > 0) {
                return response('This email address already exits. try with another.', 400);
            }

            $ck_exist = User::where('ccc', $request->ccc)->where('mobile', $request->mobile)->count();
            if ($ck_exist > 0) {
                return response('Mobile number already exits. try with another.', 400);
            }

            // Generate unique username 
            $lastUserid = DB::table('users')->orderBy('id', 'DESC')->first('id');
            $lastUserid = $lastUserid->id;
            $username = str_replace(" ", ".", $request->name) . '.' . ($lastUserid + 1) . '.' . rand(100, 999);

            $tempPass = uniqid(); // will send to user with email

            DB::beginTransaction();

            // save data to users table
            $user = [
                'username'     => $username,
                'email'        => $request->email,
                'mobile'       => $request->mobile,
                'ccc'          => $request->ccc,
                'password'     => Hash::make($tempPass),
                'twoFA'        => 0,
                'isAdmin'      => 0,
                'isActive'     => 0
            ];
            $uid = DB::table('users')->insertGetId($user) ?: throw new Exception('User not created.');

            // save data to User_infos table 
            $User_infos = [
                'uid'        => $uid,
                'name'       => $request->name,
                'birthday'   => $request->dob,
                'gender'     => $request->gender,
                'photo'      => uniqid('photo.' . $uid . '.') . '.' . $request->photo->extension(),
                'signature'  => uniqid('signature.' . $uid . '.') . '.' . $request->signature->extension(),
                'mother'     => $request->mother,
                'father'     => $request->father,
                'spouse'     => $request->spouse,
                'profession' => $request->profession,
                'remarks'    => $request->remarks,
                'insertedBy' => $this->activeUserID()
            ];

            DB::table('User_infos')->insert($User_infos) ?: throw new Exception('User info not inserted.');
            // move photo to server folder
            $request->photo->move(public_path('assets/photos'), $User_infos['photo']) ?: throw new Exception('User photo not moved to folder.');
            $request->signature->move(public_path('assets/documents/signatures'), $User_infos['signature']) ?: throw new Exception('User signature not moved to folder.');

            // save data to address table
            $address = [
                'uid'           => $uid,
                'house'         => $request->pHouse,
                'area'          => $request->pArea,
                'postOffice'    => $request->pPostOffice,
                'policeStation' => $request->pPoliceStation,
                'district'      => $request->pDistrict,
                'country'       => $request->pCountry,
                'type'          => 'Present',
                'isActive'      => 0,
                'insertedBy' => $this->activeUserID()
            ];

            DB::table('user_addresses')->insert($address) ?: throw new Exception('Present Address not inserted.');

            $address['type'] = 'Permanent';

            if (!isset($request->sameAddress)) {

                $address['house']         = $request->fHouse;
                $address['area']          = $request->fArea;
                $address['postOffice']    = $request->fPostOffice;
                $address['policeStation'] = $request->fPoliceStation;
                $address['district']      = $request->fDistrict;
                $address['country']       = $request->fCountry;
            }

            DB::table('user_addresses')->insert($address) ?: throw new Exception('Permanent Address not inserted.');

            // save data to users table
            $doc = [
                'uid'        => $uid,
                'docName'    => 'National ID Card',
                'docNumber'  => $request->nidNumber,
                'document'   => uniqid('nid.' . $uid . '.') . time() . '.' . $request->nid->extension(),
                'type'       => 'NID',
                'isActive'   => 0,
                'insertedBy' => $this->activeUserID()
            ];

            DB::table('user_documents')->insert($doc) ?: throw new Exception('NID information not inserted.');
            // move photo to server folder
            $request->nid->move(public_path('assets/documents'), $doc['document']) ?: throw new Exception('NID card not moved to folder.');

            // send credintials to new user with greetings 
            $subject = "Greetings from " . config('app.name');

            $mail = new MailController();
            $send = $mail->sendotp('emails.sendUserCreated', $tempPass, $request->email, $request->name, $subject);
            $send ?: throw new Exception('Greetings email sending failed.');

            // save data permanently 
            DB::commit();
            //send response to Requester
            return response('User created successfully. User ID is ' . $uid, 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from createNewUser@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('User not created.', 400);
        }
    }

    // to add new contact for existing user 
    private function createUserContact($request)
    {
    }

    // to add new document for existing user 
    private function createUserDocument($request)
    {
        $request->validate([
            'uid'        => 'required|exists:users,id',
            'name'       => 'nullable|string|min:3|max:50',
            'number'     => 'nullable|min:3|max:50',
            'type'       => 'required|in:NID,PASSPORT,BRC,DL,OTHERS',
            'remarks'    => 'nullable|string',
            'doc'        => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
        ]);

        try {

            if ($request->name == null) {
                return response('Document name is required', 400);
            }

            if ($request->doc == null) {
                return response('No file choosen.', 400);
            }

            // save data to User_infos table 
            $data = [
                'uid'        => $request->uid,
                'docName'    => $request->name,
                'docNumber'  => strtoupper($request->number),
                'document'   => uniqid($request->type . '.' . $request->uid . '.') . '.' . time() . '.' . $request->doc->extension(),
                'type'       => $request->type,
                'remarks'    => $request->remarks,
                'isActive'   => 0,
                'insertedBy' => $this->activeUserID()
            ];

            DB::beginTransaction();

            DB::table('user_documents')->insert($data) ?: throw new Exception('Document not inserted.');
            // move photo to server folder
            $request->doc->move(public_path('assets/documents/'), $data['document']) ?: throw new Exception('Document not moved to folder.');

            DB::commit();

            //send response to Requester
            return response('Document successfully inserted.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from createUserDocument@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Document not inserted.', 400);
        }
    }

    private function createAlternateContact($request)
    {
        $request->validate([
            'uid'        => 'exists:users,id',
            'contact'    => 'string|min:8|max:50',
            'type'       => 'required|in:email,mobile,others',
            'status'     => 'required|boolean'
        ]);

        try {
            // save data to User_infos table 
            $data = [
                'uid'        => $request->uid,
                'contact'    => $request->contact,
                'type'       => $request->type,
                'isActive'   => $request->status,
                'insertedBy' => $this->activeUserID()
            ];

            DB::beginTransaction();

            DB::table('alternate_contacts')->insert($data) ?: throw new Exception('Contact not inserted.');

            DB::commit();

            //send response to Requester
            return response('Contact successfully inserted.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from createAlternateContact@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Contact not inserted.', 400);
        }
    }


    // ============================================================== End create ========================================================================
    // --------------------------------------------------------------------------------------------------------------------------------------------------
    // ============================================================== Start View User by ID =============================================================

    public function userView($id = null)
    {
        try {
            if ($id == null) {
                return redirect()->route('admin.user')->with('notice', 'Please provide an user ID to view details.');
            }

            $user  = User::with(['userinfo', 'presentAddress', 'permanentAddress'])->find($id);

            return $user != null ? view('backend.user.view', compact('user')) : redirect()->route('admin.user')->with('error', 'No User found to view details.');

            //try end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from userView@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return redirect()->route('admin.user')->with('notice', 'No User found');
        }
    }

    // ============================================================== End View User by ID =================================================================
    // ----------------------------------------------------------------------------------------------------------------------------------------------------
    //================================================================= Start Update ======================================================================

    // user update page 
    public function updateUser($id)
    {
        try {
            $countries = Select_option::where('group', 'Country')
                ->where('type', 'Option')
                ->where('isActive', 1)->where('isAuth', 1)
                ->orderBy('optionName', 'asc')
                ->get(['optionValue']);

            $genders = Select_option::where('group', 'Gender')
                ->where('type', 'Option')
                ->where('isActive', 1)
                ->where('isAuth', 1)
                ->orderBy('optionName', 'asc')
                ->get(['optionName', 'optionValue']);

            $cccs = Select_option::where('group', 'CCC')
                ->where('type', 'Option')
                ->where('isActive', 1)
                ->where('isAuth', 1)
                ->orderBy('optionName', 'asc')
                ->get(['optionName', 'optionValue']);

            $professions = Select_option::where('group', 'Profession')
                ->where('type', 'Option')
                ->where('isActive', 1)
                ->where('isAuth', 1)
                ->orderBy('optionName', 'asc')
                ->get('optionValue');

            $docNames = User_document::where('isAuth', 1)->orderBy('docName', 'asc')->get('docName')->unique('docName');

            $user  = User::with(['userinfo', 'presentAddress', 'permanentAddress', 'documents', 'contacts'])->find($id);
            $user != null ?: throw new Exception('No User found');

            return view('backend.user.update', compact('user', 'genders', 'cccs', 'countries', 'docNames', 'professions'));

            //try end
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updateuser@UserController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return redirect()->route('admin.user')->with('notice', 'No User found');
        }
    }

    // handale user update request 
    public function update(Request $request, $scope = null)
    {
        try {
            if ($request->uid == $this->activeUserID()) {
                return response('Self  modification is restricted.', 400);
            }

            $keys = ['credintials', 'personalinfo', 'photo', 'signature', 'address', 'document', 'alternate-contact'];

            if (!in_array($scope, $keys)) {
                return response('Incorrect url.', 400);
            }

            if ($scope === 'credintials') {
                return $this->updatecredintials($request);
            }
            if ($scope === 'personalinfo') {
                return $this->updatePersonalinfo($request);
            }
            if ($scope === 'photo') {
                return $this->updatePhoto($request);
            }
            if ($scope === 'signature') {
                return $this->updateSignature($request);
            }
            if ($scope === 'address') {
                return $this->updateAddress($request);
            }
            // for insert new document 
            if ($scope === 'document' && (!isset($request->id) || $request->id == null)) {
                return $this->createUserDocument($request);
            }
            // for documents update 
            if ($scope === 'document' && $request->id != null) {
                return $this->updateDocument($request);
            }
            // for insert new alternate-contact 
            if ($scope === 'alternate-contact' && (!isset($request->id) || $request->id == null)) {
                return $this->createAlternateContact($request);
            }
            // for update alternate-contact 
            if ($scope === 'alternate-contact' && $request->id != null) {
                return $this->updateAlternateContact($request);
            }

            throw new Exception('Somethig went wrong.');
            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updateUserInfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Data not updated.', 400);
        }
    }

    private function updatecredintials($request)
    {
        $request->validate([
            'uid'      => 'required|exists:users,id',
            'email'    => 'required|email|max:100',
            'ccc'      => 'nullable|exists:select_options,optionValue',
            'mobile'   => 'nullable|regex:/[0-9]{8,14}/',
            'twoFA'    => 'required|boolean',
            'isActive' => 'required|boolean'
        ]);

        try {
            if ($request->uid == $this->activeUserID()) {

                return response("Self id modify is not possible.", 403);
            }

            $ck_exist = User::where('id', '!=', $request->uid)->where('email', $request->email)->count();

            if ($ck_exist > 0) {
                return response('This email address already exits. try with another.', 400);
            }

            $ck_exist = User::where('id', '!=', $request->uid)->where('ccc', $request->ccc)->where('mobile', $request->mobile)->count();

            if ($ck_exist > 0) {
                return response('Mobile number already exits. try with another.', 400);
            }

            // save data to users table
            $data = [
                'email'    => $request->email,
                'mobile'   => $request->mobile,
                'ccc'      => $request->ccc,
                'twoFA'    => $request->twoFA,
                'isActive' => $request->isActive
            ];

            DB::beginTransaction();

            DB::table('users')->where('id', $request->uid)->update($data) ?: throw new Exception('Information not Updated.');

            DB::commit();

            //send response to Requester
            return response('Information successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updateUserInfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Information not updated.', 400);
        }
    }

    private function updatePersonalinfo($request)
    {
        $request->validate([
            'uid'          => 'required|exists:users,id',
            'id'           => 'required|exists:user_infos,id',
            'name'         => 'required|min:4|max:40',
            'dob'          => 'required|date',
            'gender'       => 'required|exists:select_options,optionValue',
            'profession'   => 'required|min:2|max:100',

            'father'       => 'required|min:3|max:40',
            'mother'       => 'required|min:3|max:40',
            'spouse'       => 'nullable|min:3|max:40',

            'remarks'      => 'nullable|string|max:255'

        ]);

        try {
            // save data to User_infos table 
            $userinfos = [
                'name'       => $request->name,
                'birthday'   => $request->dob,
                'gender'     => $request->gender,
                'mother'     => $request->mother,
                'father'     => $request->father,
                'spouse'     => $request->spouse,
                'profession' => $request->profession,
                'remarks'    => $request->remarks,
                'modifiedBy' => $this->activeUserID(),
                'isAuth'     => null,
                'authBy'     => null
            ];

            DB::beginTransaction();

            DB::table('User_infos')->where('uid', $request->uid)->update($userinfos) ?: throw new Exception('Information not Updated.');

            DB::commit();

            //send response to Requester
            return response('Information successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updatePersonalinfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Data not updated.', 400);
        }
    }

    private function updatePhoto($request)
    {
        $request->validate([
            'uid'        => 'required|exists:users,id',
            'id'         => 'required|exists:user_infos,id',
            'photo'      => 'image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400'
        ]);

        try {

            if ($request->hasfile('photo')) {
                $result = DB::table('User_infos')->find($request->id, 'photo');
                $oldFile = $result->photo;
            } else {
                return response('No file choosen.', 400);
            }
            // save data to User_infos table 
            $data = [
                'photo'      => uniqid('photo_' . $request->uid . '_') . time() . '.' . $request->photo->extension(),
                'modifiedBy' => $this->activeUserID(),
                'isAuth'     => null,
                'authBy'     => null
            ];

            DB::beginTransaction();

            DB::table('User_infos')->where('uid', $request->uid)->update($data) ?: throw new Exception('User photo not Updated.');

            // move photo to server folder
            $request->photo->move(public_path('assets/photos'), $data['photo']) ?: throw new Exception('User photo not moved to folder.');

            DB::commit();

            $oldFile == null ?: unlink(public_path('assets/photos/' . $oldFile));

            //send response to Requester
            return response('Photo successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updatePersonalinfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Photo not updated.', 400);
        }
    }

    private function updateSignature($request)
    {
        $request->validate([
            'uid'        => 'required|exists:users,id',
            'id'         => 'required|exists:user_infos,id',
            'signature'  => 'image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
        ]);

        try {

            if ($request->hasfile('signature')) {
                $result = DB::table('User_infos')->find($request->id, 'signature');
                $oldFile = $result->signature;
            } else {
                return response('No file choosen.', 400);
            }

            // save data to User_infos table 
            $data = [
                'signature'  => uniqid('signature_' . $request->uid . '_') . time() . '.' . $request->signature->extension(),
                'modifiedBy' => $this->activeUserID(),
                'isAuth'     => null,
                'authBy'     => null
            ];

            DB::beginTransaction();

            DB::table('User_infos')->where('uid', $request->uid)->update($data) ?: throw new Exception('Signature not Updated.');
            // move photo to server folder
            $request->signature->move(public_path('assets/documents/signatures/'), $data['signature']) ?: throw new Exception('Signature not moved to folder.');

            DB::commit();

            $oldFile == null ?: unlink(public_path('assets/documents/signatures/' . $oldFile));

            //send response to Requester
            return response('Signature successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updatePersonalinfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Signature not updated.', 400);
        }
    }
    private function updateAddress($request)
    {
        $request->validate([
            'uid'           => 'required|exists:users,id',

            'pCountry'        => 'nullable|exists:select_options,optionValue',
            'pDistrict'       => 'nullable|exists:select_options,optionValue',
            'pPoliceStation'  => 'nullable|exists:select_options,optionValue',
            'pPostOffice'     => 'nullable|exists:select_options,optionValue',
            'pHouse'          => 'nullable|max:100',
            'pArea'           => 'nullable|max:100',

            'sameAddress'     => 'nullable|boolean',

            'fCountry'        => 'nullable|exists:select_options,optionValue',
            'fDistrict'       => 'nullable|exists:select_options,optionValue',
            'fPoliceStation'  => 'nullable|exists:select_options,optionValue',
            'fPostOffice'     => 'nullable|exists:select_options,optionValue',
            'fHouse'          => 'nullable|max:100',
            'fArea'           => 'nullable|max:100'
        ]);

        try {

            $inputs = ['pCountry' => 'Country', 'pDistrict' => 'District', 'pPoliceStation' => 'Police Station', 'pPostOffice' => 'Post Office', 'pHouse' => 'House name or number'];

            if (!isset($request->sameAddress)) {
                $fAddrinputs = ['fCountry' => 'Permanent Country', 'fDistrict' => 'Permanent District', 'fPoliceStation' => 'Permanent Police Station', 'fPostOffice' => 'Permanent Post Office', 'fHouse' => 'Permanent House name or number'];
                $inputs = array_merge($inputs, $fAddrinputs);
            }

            foreach ($inputs as $input => $text) {
                if ($request->$input == null) {
                    return response($text . ' is required', 400);
                }
            }

            // save data to address table
            $address = [
                'house'         => $request->pHouse,
                'area'          => $request->pArea,
                'postOffice'    => $request->pPostOffice,
                'policeStation' => $request->pPoliceStation,
                'district'      => $request->pDistrict,
                'country'       => $request->pCountry,
                'remarks'       => $request->pRemarks,

                'isActive'      => 0,
                'modifiedBy'    => $this->activeUserID(),
                'isAuth'        => null,
                'authBy'        => null
            ];

            DB::beginTransaction();

            DB::table('user_addresses')->where('uid', $request->uid)->where('type', 'Present')->update($address) ?: throw new Exception('Present Address not updated.');

            if (!isset($request->sameAddress)) {

                $address['house']         = $request->fHouse;
                $address['area']          = $request->fArea;
                $address['postOffice']    = $request->fPostOffice;
                $address['policeStation'] = $request->fPoliceStation;
                $address['district']      = $request->fDistrict;
                $address['country']       = $request->fCountry;
                $address['remarks']       = $request->fRemarks;
            }

            DB::table('user_addresses')->where('uid', $request->uid)->where('type', 'Permanent')->update($address) ?: throw new Exception('Permanent Address not updated.');

            DB::commit();

            //send response to Requester
            return response('Address successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updateAddress@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Address not updated.', 400);
        }
    }
    private function updateDocument($request)
    {
        $request->validate([
            'id'         => 'required|exists:user_documents,id',
            'uid'        => 'required|exists:users,id',
            'name'       => 'nullable|string|min:3|max:50',
            'number'     => 'nullable|min:3|max:50',
            'type'       => 'required|in:NID,PASSPORT,BRC,DL,OTHERS',
            'status'     => 'nullable|boolean',
            'remarks'    => 'nullable|string',
            'doc'        => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
        ]);

        try {

            if ($request->name == null) {
                return response('Document name is required', 400);
            }

            // save data to User_infos table 
            $data = [
                'docName'    => $request->name,
                'docNumber'  => strtoupper($request->number),
                'type'       => $request->type,
                'remarks'    => $request->remarks,
                'isActive'   => $request->status,
                'modifiedBy' => $this->activeUserID(),
                'isAuth'     => null,
                'authBy'     => null
            ];

            if ($request->hasfile('doc')) {
                $result = DB::table('user_documents')->find($request->id, 'document');
                $oldFile = $result->document;
                $data['document'] = uniqid($request->type . '.' . $request->uid . '.') . '.' . time() . '.' . $request->doc->extension();
            }

            DB::beginTransaction();

            DB::table('user_documents')->where('id', $request->id)->update($data) ?: throw new Exception('Document not Updated.');

            // move photo to server folder
            if ($request->hasfile('doc')) {
                $request->doc->move(public_path('assets/documents/'), $data['document']) ?: throw new Exception('Document not moved to folder.');
                $oldFile == null ?: unlink(public_path('assets/documents/' . $oldFile));
            }

            DB::commit();

            //send response to Requester
            return response('Document successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updatePersonalinfo@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Document not updated.', 400);
        }
    }

    private function updateAlternateContact($request)
    {
        $request->validate([
            'id'         => 'exists:alternate_contacts,id',
            'uid'        => 'exists:users,id',
            'contact'    => 'string|min:8|max:50',
            'type'       => 'required|in:email,mobile,others',
            'status'     => 'required|boolean'
        ]);

        try {
            // save data to User_infos table 
            $data = [
                'contact'    => $request->contact,
                'type'       => $request->type,
                'isActive'   => $request->status,
                'insertedBy' => $this->activeUserID()
            ];

            DB::beginTransaction();

            DB::table('alternate_contacts')->where('id', $request->id)->update($data) ?: throw new Exception('Contact not updated.');

            DB::commit();

            //send response to Requester
            return response('Contact successfully updated.', 201);

            //try end
        } catch (Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from updateAlternateContact@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Contact not updated.', 400);
        }
    }

    // ============================================================== End Update ========================================================================
    // --------------------------------------------------------------------------------------------------------------------------------------------------
    // ============================================================== Start others =======================================================================


    // ============================================================== End others ===============================================================================
    // ---------------------------------------------------------------------------------------------------------------------------------------------------------
    // ============================================================== Start api requests ========================================================================


    // api request for user  user page 
    public function pullUser($filter = null)
    {
        try {

            // for active or inactive user 
            $filterArray = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $filterArray)) {

                $filter = $filterArray[$filter];

                $users  = User::with(['userinfo', 'presentAddress', 'permanentAddress'])->where('isActive', $filter)->latest('id')->limit(10)->get();
                $users->count() > 0 ?: throw new Exception('No User found');
                return response($users->toJson(JSON_PRETTY_PRINT));
            }

            // for admin user  
            if ($filter == 'admin' || $filter == 'Admin') {

                $users  = User::with(['userinfo', 'presentAddress', 'permanentAddress'])->where('isAdmin', 1)->latest('id')->limit(10)->get();
                $users->count() > 0 ?: throw new Exception('No User found');
                return response($users->toJson(JSON_PRETTY_PRINT));
            }

            // in case of filter by user ID, name, email, mobile 
            if ($filter != null) {

                $users  = User::with(['userinfo', 'presentAddress', 'permanentAddress'])->where('id', $filter)
                    ->orWhere('username', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('mobile', 'like', '%' . $filter . '%')
                    ->latest('id')->limit(10)->get();
                $users->count() > 0 ?: throw new Exception('No User found');
                return response($users->toJson(JSON_PRETTY_PRINT));
            }

            $users  = User::with(['userinfo', 'presentAddress', 'permanentAddress'])->latest('id')->limit(10)->get();
            $users->count() > 0 ?: throw new Exception('No User found');
            return response($users->toJson(JSON_PRETTY_PRINT));

            //try end
        } catch (Exception $except) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullUser@UserController | " . date('d M Y H:i:s', time()) . " | " . $except->getMessage());
            return response('No User found', 404);
        }
    }

    // api request for get User Name
    public function getUserName($id = null)
    {
        try {
            $id != null ?: throw new Exception('No user id given.');

            $name = User_info::where('uid', $id)->first('name');

            $name->count() > 0 ?: throw new Exception('No User found');

            return response($name->name);

            //end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from getUserName@UserController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No User found', 404);
        }
    }

    // ============================================================== End api requests ========================================================================




    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


}
