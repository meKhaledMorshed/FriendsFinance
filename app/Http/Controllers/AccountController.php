<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Account_category;
use App\Models\Branch;
use App\Models\Nominee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    //


    private function adminUID()
    {
        return session()->get('userID');
    }

    public function index()
    {
        try {

            $categories = Account_category::where('isActive', 1)->get(['id', 'category']);
            $branches = Branch::where('isActive', 1)->get(['id', 'branchName']);

            return view('backend.account.index', compact('categories', 'branches'));
            //end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from create@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something Error.');
        }
    }
    public function postAccountForm(Request $request)
    {
        try {

            $request->validate([
                'id'            => 'nullable|exists:accounts,id',
                'uid'           => 'required|exists:users,id',
                'accountName'   => 'required|string|max:100',
                'category'      => 'nullable|exists:account_categories,id',
                'branch'        => 'nullable|exists:branches,id',
                'status'        => 'nullable|boolean',
                'authorization' => 'nullable|in:-1,0,1',
                'remarks'       => 'nullable|string|max:255'
            ]);

            if ((!isset($request->id) || $request->id == null) && $request->category == null) {
                return response('Please select a category.', 406);
            }
            if ((!isset($request->id) || $request->id == null) && $request->branch == null) {
                return response('Please select a Branch.', 406);
            }

            $totalAccountExist = Account::where('branchID', $request->branch)->where('catID', $request->category)->count();

            $account = [
                'uid'           => $request->uid,
                'accountName'   => $request->accountName,
                'remarks'       => $request->remarks,
                'isActive'      => $request->status,
                'isAuth'        => $request->authorization,
                'authBy'        => $request->authorization == null ?:  $this->adminUID()
            ];

            DB::beginTransaction();

            if (isset($request->id) && $request->id != null) {
                $account['modifiedBy'] = $this->adminUID();
                DB::table('accounts')->where('id', $request->id)->update($account) ?: throw new Exception('Account not Updated.');
            } else {
                $account['branchID'] = $request->branch;
                $account['catID'] = $request->category;
                $account['accountNumber'] = sprintf('%03d', $request->branch) . sprintf('%03d', $request->category) . sprintf('%04d', $totalAccountExist + 1);
                $account['insertedBy'] = $this->adminUID();
                DB::table('accounts')->insert($account) ?: throw new Exception('Account not Created.');
            }

            DB::commit();
            return response('Request successfully executed.', 201);

            //end
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from postAccountForm@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Request not executed', 406);
        }
    }

    public function pullAccounts($filter = null)
    {
        try {

            if ($filter == null) {
                $accounts = Account::where('isAuth', 1)->latest('modifiedDate')->limit(10)->get();
                $accounts->count() > 0 ?: throw  new Exception('No data found.');
                return response($accounts->toJson(), 200);
            }

            $filter = strtolower($filter);

            if ($filter == 'all') {
                $accounts = Account::latest('modifiedDate')->get();
                $accounts->count() > 0 ?: throw  new Exception('No data found.');
                return response($accounts->toJson(), 200);
            }

            $filters = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $accounts = Account::where('isActive', $filter)->latest('modifiedDate')->limit(10)->get();
                $accounts->count() > 0 ?: throw  new Exception('No data found.');
                return response($accounts->toJson(), 200);
            }

            $filters = ['authorize' => 1, 'unauthorized' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $accounts = Account::where('isAuth', $filter)->latest('modifiedDate')->limit(10)->get();
                $accounts->count() > 0 ?: throw  new Exception('No data found.');
                return response($accounts->toJson(), 200);
            }

            if ($filter != null) {
                $accounts = Account::where('id', $filter)
                    ->orwhere('branchID', 'like', '%' . $filter . '%')
                    ->orwhere('catID', 'like', '%' . $filter . '%')
                    ->orwhere('accountName', 'like', '%' . $filter . '%')
                    ->orwhere('accountNumber', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get();
                $accounts->count() > 0 ?: throw  new Exception('No data found.');
                return response($accounts->toJson(), 200);
            }

            throw  new Exception('No data found.');

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullAccounts@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No Account found', 404);
        }
    }

    public function getAccountName($number = null)
    {
        try {
            if ($number == null) {
                return response('No account number given', 400);
            }

            $name = Account::where('accountNumber', $number)->first('accountName');
            $name != null ?: throw  new Exception('No account found.');
            return response($name->accountName, 200);

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from getAccountName@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No match.', 404);
        }
    }
    /* ================================================ Category ================================================ */

    public function pullCategories($filter = null)
    {
        try {

            if ($filter == null) {
                $categories = Account_category::where('isAuth', 1)->latest('modifiedDate')->limit(10)->get();
                $categories->count() > 0 ?: throw  new Exception('No data found.');
                return response($categories->toJson(), 200);
            }

            $filter = strtolower($filter);

            if ($filter == 'all') {
                $categories = Account_category::orderBy('category')->get();
                $categories->count() > 0 ?: throw  new Exception('No data found.');
                return response($categories->toJson(), 200);
            }

            $filters = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $categories = Account_category::where('isActive', $filter)->latest('modifiedDate')->limit(10)->get();
                $categories->count() > 0 ?: throw  new Exception('No data found.');
                return response($categories->toJson(), 200);
            }

            $filters = ['authorize' => 1, 'unauthorized' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $categories = Account_category::where('isAuth', $filter)->latest('modifiedDate')->limit(10)->get();
                $categories->count() > 0 ?: throw  new Exception('No data found.');
                return response($categories->toJson(), 200);
            }

            if ($filter != null) {
                $categories = Account_category::where('id', $filter)
                    ->orwhere('category', 'like', '%' . $filter . '%')
                    ->orwhere('description', 'like', '%' . $filter . '%')
                    ->orwhere('tags', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get();
                $categories->count() > 0 ?: throw  new Exception('No data found.');
                return response($categories->toJson(), 200);
            }

            throw  new Exception('No data found.');

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullCategories@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No category found', 404);
        }
    }
    public function pullCategoryName($id = null)
    {
        try {
            if ($id == null) {
                return response('No Category ID given', 400);
            }

            $parent = Account_category::find($id);
            $parent != null ?: throw  new Exception('No data found.');
            return response($parent->category, 200);

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullCategoryName@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No Parent category found', 404);
        }
    }

    public function accountCategoryForm()
    {
        try {


            $categories = Account_category::where('isActive', 1)->get(['id', 'category']);

            return view('backend.account.category', compact('categories'));

            throw new Exception();

            //end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from create@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Something Error.');
        }
    }

    public function postAccountCategory(Request $request)
    {
        try {

            $request->validate([
                'id'            => 'nullable|exists:account_categories,id',
                'name'          => 'required|string|min:2|max:50',
                'parent'        => 'nullable|exists:account_categories,id',
                'status'        => 'nullable|boolean',
                'authorization' => 'nullable|boolean'
            ]);

            $data = [
                'category'      => $request->name,
                'description'   => $request->description,
                'parentCatID'   => $request->parent,
                'tags'          => $request->tag,
                'remarks'       => $request->remarks,
                'isActive'      => $request->status,
                'isAuth'        => $request->authorization,
                'authBy'        => $request->authorization == null ?:  $this->adminUID()
            ];

            DB::beginTransaction();

            if (!isset($request->id)) {

                $ck_duplicate = DB::table('account_categories')->where('category', $request->name)->count();
                if ($ck_duplicate > 0) {
                    return response($request->name . ' already exists.', 401);
                }

                $data['insertedBy'] = $this->adminUID();

                DB::table('account_categories')->insert($data) ?: throw new Exception('Account category not inserted.');
                DB::commit();
                return response('Account category inserted.', 201);
            }

            if (isset($request->id)) {

                $ck_duplicate = DB::table('account_categories')->where('id', '!=', $request->id)->where('category', $request->name)->count();
                if ($ck_duplicate > 0) {
                    return response($request->name . ' already exists.', 401);
                }

                $data['modifiedBy'] = $this->adminUID();

                DB::table('account_categories')->where('id', $request->id)->update($data) ?: throw new Exception('Account category not updated.');
                DB::commit();
                return response('Account category updated.', 201);
            }
            // end 
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from create@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Data not inserted', 401);
        }
    }


    /* ================================================ Nominee ================================================ */


    public function postNomineeForm(Request $request)
    {
        try {

            $request->validate([
                'id'             => 'nullable|exists:nominees,id',
                'accountNumber'  => 'nullable|exists:accounts,accountNumber',
                'name'           => 'nullable|string|max:100',
                'dob'            => 'nullable|date|after:01/01/1970|before:tomorrow',
                'gender'         => 'nullable|exists:select_options,optionValue',
                'relation'       => 'nullable|in:Mother,Father,Brother,Sister,Spouse,Other',
                'share'          => 'nullable|numeric|min:1|max:100',
                'email'          => 'nullable|email|max:100',
                'mobile'         => 'nullable|max:20',
                'nid'            => 'nullable|regex:/[A-Za-z0-9]{8,25}/',
                'passport'       => 'nullable|regex:/[A-Za-z0-9]{8,25}/',
                'address'        => 'nullable|string|max:255',
                'remarks'        => 'nullable|string|max:255',
                'photo'          => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG | max:102400',
                'status'         => 'nullable|boolean',
                'authorization'  => 'nullable|in:-1,0,1'
            ]);

            $inputs = [
                'accountNumber' => 'Account Number',
                'name'          => 'Nominee name',
                'dob'           => 'Birthday',
                'gender'        => 'Gender',
                'relation'      => 'Relation',
                'address'       => 'Address',
                'dob'           => 'Birthday'
            ];

            foreach ($inputs as $key => $input) {
                if ($request->$key == null) {
                    return response("$input is required.", 406);
                }
            }

            if ((!isset($request->id) || $request->id == null) && $request->photo == null) {
                return response("Nominee Photo is required.", 406);
            }

            $nominee = [
                'accountNum' => $request->accountNumber,
                'name'          => $request->name,
                'birthday'      => $request->dob,
                'gender'        => $request->gender,
                'relation'      => $request->relation,
                'percentage'    => $request->share != null ? $request->share : 100,
                'nid'           => $request->nid,
                'passport'      => $request->passport,
                'email'         => $request->email,
                'mobile'        => $request->mobile,
                'address'       => $request->address,
                'remarks'       => $request->remarks,
                'isActive'      => $request->status,
                'isAuth'        => $request->authorization,
                'authBy'        => $request->authorization == null ?:  $this->adminUID()
            ];

            if ($request->hasfile('photo')) {
                $nominee['photo'] = uniqid('nominee_' . $request->accountNumber . '_') . time() . '.' . $request->photo->extension();
            }

            DB::beginTransaction();

            if (isset($request->id) && $request->id != null) {

                if ($request->hasfile('photo')) {
                    $result = DB::table('nominees')->find($request->id, 'photo');
                    $oldFile = $result->photo;
                }

                $nominee['modifiedBy'] = $this->adminUID();
                DB::table('nominees')->where('id', $request->id)->update($nominee) ?: throw new Exception('Nominee not Updated.');

                // move photo to server folder
                if ($request->hasfile('photo')) {
                    // move photo to server folder 
                    $request->photo->move(public_path('assets/photos'), $nominee['photo']) ?: throw new Exception('Nominee photo not moved to folder.');
                    $oldFile == null ?: unlink(public_path('assets/photos/' . $oldFile));
                }
            } else {
                $nominee['insertedBy'] = $this->adminUID();
                DB::table('nominees')->insert($nominee) ?: throw new Exception('Nominee not Created.'); // move photo to server folder 
                $request->photo->move(public_path('assets/photos'), $nominee['photo']) ?: throw new Exception('Nominee photo not moved to folder.');
            }

            DB::commit();
            return response('Request successfully executed.', 201);

            //end
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from postNomineeForm@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Request not executed', 406);
        }
    }

    public function pullNominees($filter = null)
    {
        try {

            if ($filter == null) {
                $results = Nominee::where('isAuth', 1)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filter = strtolower($filter);

            if ($filter == 'all') {
                $results = Nominee::latest('modifiedDate')->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filters = ['active' => 1, 'inactive' => 0];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $results = Nominee::where('isActive', $filter)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filters = ['authorize' => 1, 'unauthorized' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $results = Nominee::where('isAuth', $filter)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            if ($filter != null) {
                $results = Nominee::where('id', $filter)
                    ->orwhere('accountNum', 'like', '%' . $filter . '%')
                    ->orwhere('name', 'like', '%' . $filter . '%')
                    ->orwhere('nid', 'like', '%' . $filter . '%')
                    ->orwhere('passport', 'like', '%' . $filter . '%')
                    ->orwhere('email', 'like', '%' . $filter . '%')
                    ->orwhere('mobile', 'like', '%' . $filter . '%')
                    ->orwhere('address', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            throw  new Exception('No data found.');

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullNominees@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No data found', 404);
        }
    }



    // end class
}
