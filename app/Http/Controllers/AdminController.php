<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Admin_title;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public $admin;

    function __construct($requestedUserID = null)
    {
        $uID = $requestedUserID ?: $this->activeUserID();

        $result = Admin::where('uid', $uID)->first();

        if ($result) {

            $admin = [];

            $admin['userID']       = $result->user->id;
            $admin['email']        = $result->user->email;
            $admin['mobile']       = $result->user->ccc . $result->user->mobile;
            $admin['name']         = $result->userinfo->name;
            $admin['photo']        = $result->userinfo->photo;
            $admin['adminID']      = $result->id;
            $admin['role']         = $result->role;
            $admin['duty']         = $result->duty;
            $admin['title']        = $result->title->title;
            $admin['readPermit']   = $result->permission->readPermit;
            $admin['writePermit']  = $result->permission->writePermit;
            $admin['editPermit']   = $result->permission->editPermit;
            $admin['deletePermit'] = $result->permission->deletePermit;
            $admin['branchID']     = $result->branchID;
            $admin['branch']       = $result->branch->name;

            $this->admin = (object) $admin;
        }
    }

    private function activeUserID()
    {
        return session()->get('userID');
    }


    public function viewAdminPanel()
    {
        $admins = Admin::with('userinfo', 'title', 'branch', 'permission')->get();
        $titles = Admin_title::where('isActive', 1)->where('isAuth', 1)->get(['id', 'definition']);
        $roles = ['master', 'super', 'authorizer', 'accountant', 'teller', 'auditor', 'editor', 'officer'];
        $branches = Branch::where('isActive', 1)->where('isAuth', 1)->get(['id', 'branchName']);

        return view('backend.admin.admin', compact('admins', 'titles', 'roles', 'branches'));
    }

    public function adminPanelForm(Request $request, $form = null)
    {
        try {
            $formName = ['admin_form', 'designation_form'];
            if (!in_array($form, $formName)) {
                return redirect()->route('admin-panel');
            }
            if ($form == 'admin_form' && $request->adminid == null) {
                return $this->craete_admin($request);
            }
            if ($form == 'admin_form' && $request->adminid != null) {
                return $this->update_admin($request);
            }
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from adminPanelForm@AdminController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return back()->with('error', 'Requested subbmission failed.');
        }
    }


    // =================================================== create =======================================================

    private function craete_admin($request)
    {
        try {
            $request->validate([
                'uid'           => 'required|exists:users,id',
                'designation'   => 'required|exists:admin_titles,id',
                'role'          => 'required|in:master,super,authorizer,accountant,teller,auditor,editor,officer',
                'branch'        => 'required|exists:branches,id',
                'joining'       => 'required|date',
                'read'          => 'nullable|boolean',
                'write'         => 'nullable|boolean',
                'edit'          => 'nullable|boolean',
                'delete'        => 'nullable|boolean',
            ]);

            $ckAdmin = DB::table('admins')->where('uid', $request->uid)->count();
            if ($ckAdmin > 0) {
                return back()->with('error', 'This user already have an admin ID.');
            }


            $data = [
                'uid'        => $request->uid,
                'titleID'    => $request->designation,
                'branchID'   => $request->branch,
                'role'       => $request->role,
                'duty'       => $request->duty,
                'assignDate' => $request->joining,
                'remarks'    => $request->remarks,
                'isActive'   => $request->status ?: 0,
                'insertedBy' => $this->activeUserID(),
                'isAuth'     => $request->authorization ?: 0,
                'authBy'     => $this->activeUserID(),
            ];

            DB::beginTransaction();

            $adminID = DB::table('admins')->insertGetId($data) ?: throw new Exception('Admin not created.');

            $data = [
                'adminID'      => $adminID,
                'readPermit'   => $request->read ?: 0,
                'writePermit'  => $request->write ?: 0,
                'editPermit'   => $request->edit ?: 0,
                'deletePermit' => $request->delete ?: 0,
                'permitBy'     => $this->activeUserID(),
            ];
            DB::table('permissions')->insert($data) ?: throw new Exception('Permission not recorded.');

            DB::table('users')->where('id', $request->uid)->update(['isAdmin' => 1]) ?: throw new Exception('User table not updated.');

            DB::commit();
            return back()->with('success', 'Admin successfully created.');

            // end
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from Create_admin@AdminController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return back()->with('error', 'Admin not created.');
        }
    }


    // =================================================== end create ===================================================

    // =================================================== update =======================================================

    private function update_admin($request)
    {
        try {
            $request->validate([
                'adminid'       => 'required|exists:admins,id',
                'uid'           => 'required|exists:admins,uid',
                'designation'   => 'required|exists:admin_titles,id',
                'role'          => 'required|in:master,super,authorizer,accountant,teller,auditor,editor,officer',
                'branch'        => 'required|exists:branches,id',
                'joining'       => 'required|date',
                'retire'        => 'nullable|date',
                'read'          => 'nullable|boolean',
                'write'         => 'nullable|boolean',
                'edit'          => 'nullable|boolean',
                'delete'        => 'nullable|boolean',
            ]);

            if ($request->uid == $this->activeUserID()) {
                return back()->with('error', 'Self modification is restricted.');
            }

            $data = [
                'titleID'    => $request->designation,
                'branchID'   => $request->branch,
                'role'       => $request->role,
                'duty'       => $request->duty,
                'assignDate' => $request->joining,
                'retireDate' => $request->retire,
                'remarks'    => $request->remarks,
                'isActive'   => $request->status ?: 0,
                'modifiedBy' => $this->activeUserID(),
                'isAuth'     => $request->authorization ?: 0,
                'authBy'     => $this->activeUserID(),
            ];

            DB::beginTransaction();

            DB::table('admins')->where('id', $request->adminid)->update($data);

            $data = [
                'readPermit'   => $request->read ?: 0,
                'writePermit'  => $request->write ?: 0,
                'editPermit'   => $request->edit ?: 0,
                'deletePermit' => $request->delete ?: 0,
                'permitBy'     => $this->activeUserID(),
            ];
            DB::table('permissions')->where('adminID', $request->adminid)->update($data);

            DB::commit();

            return back()->with('success', 'Admin successfully Updated.');

            // end
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from Create_admin@AdminController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return back()->with('error', 'Admin not updated.');
        }
    }

    // =================================================== end update ===================================================




    // =================================================== api's for admin panel page ===================================================

    public function handleGetDesignations($filter = null)
    {
        try {

            $keys = ['active' => 1, 'inactive' => 0];
            if (array_key_exists($filter, $keys)) {

                $value = $keys[$filter];

                $results = Admin_title::where('isActive', $value)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw new Exception("No $filter data found.");
                $results = $results->toJson(JSON_PRETTY_PRINT);
                return response($results);
            }

            $keys = ['authorize' => 1, 'unauthorize' => 0, 'reject' => -1, 'pending' => null,];
            if (array_key_exists($filter, $keys)) {

                $value = $keys[$filter];

                $results = Admin_title::where('isAuth', $value)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw new Exception("No $filter data found.");
                $results = $results->toJson(JSON_PRETTY_PRINT);
                return response($results);
            }
            if ($filter != null) {

                $results = Admin_title::where('id',   $filter)
                    ->orwhere('title', 'like', '%' . $filter . '%')
                    ->orwhere('definition', 'like', '%' . $filter . '%')
                    ->orwhere('type', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get();

                $results->count() > 0 ?: throw new Exception("No $filter data found.");
                $results = $results->toJson(JSON_PRETTY_PRINT);
                return response($results);
            }

            $results = Admin_title::latest('modifiedDate')->limit(10)->get();
            $results->count() > 0 ?: throw new Exception("No $filter data found.");
            $results = $results->toJson(JSON_PRETTY_PRINT);
            return response($results) ?: throw new Exception('Somethig went wrong.');

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from handleGetDesignations@AdminController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No data found.', 404);
        }
    }

    public function handlePostDesignations(Request $request)
    {
        try {
            $request->validate([
                'd_id'            => 'nullable|exists:admin_titles,id',
                'd_title'         => 'nullable|string',
                'd_type'          => 'nullable|in:Elected,Permanent,Contractual,Temporary,Other',
                'd_definition'    => 'nullable|exists:select_options,optionValue',
                'd_status'        => 'nullable|boolean',
                'd_authorization' => 'nullable|in:-1,0,1',
                'd_remarks'       => 'nullable|string',
            ]);

            if ($request->d_definition == null) {
                return response('Full Designations is required.', 400);
            }

            $data = [
                'title'      => $request->d_title,
                'definition' => $request->d_definition,
                'type'       => $request->d_type,
                'remarks'    => $request->d_remarks,
                'isActive'   => $request->d_status ?: 0,
                'isAuth'     => $request->d_authorization ?: 0,
                'authBy' => $this->activeUserID(),

            ];

            // for create new designation 
            if (!isset($request->d_id) || $request->d_id == null) {
                $data['insertedBy'] =  $this->activeUserID();

                DB::table('admin_titles')->insert($data) ?: throw new Exception('Data not inserted');

                return  response('Designations successfully created.', 201);
            }

            // for update existing designation 
            if (isset($request->d_id) && $request->d_id != null) {
                $data['modifiedBy'] =  $this->activeUserID();
                $save = DB::table('admin_titles')->where('id', $request->d_id)->update($data);

                return $save ? response('Designations successfully updated.', 201) : response('Designations not updated.', 400);
            }

            throw new Exception('Something went wrong.');

            //end 
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from handlePostDesignations@AdminController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Designations not created.', 400);
        }
    }



    //the end.
}
