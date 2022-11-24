<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Account_category;
use App\Models\Branch;
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
                'accountNumber'  => 'required|exists:accounts,accountNumber',
                'name'           => 'required|string|max:100',
                'dob'            => 'required|date|after:01/01/1970|before:tomorrow',
                'gender'         => 'required|exists:select_options,optionValue',
                'relation'       => 'required|in:Mother,Father,Brother,Sister,Spouse,Other',
                'share'          => 'required|numiric|min:1|max:100',
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

            $ck_inputs = [
                'name'      => 'Nominee name is required',
                'dob'       => 'Nominee birthday is required',
                'gender'    => 'Nominee gender is required',
                'dob'       => 'Nominee birthday is required',
                'dob'       => 'Nominee birthday is required',
                'dob'       => 'Nominee birthday is required',
            ];

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


    // end class
}
