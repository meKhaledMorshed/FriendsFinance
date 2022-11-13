<?php

namespace App\Http\Controllers;

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
    public function pullParentCategoryName($id = null)
    {
        try {
            if ($id == null) {
                return response('No parent ID given', 400);
            }

            $parent = Account_category::find($id);
            $parent != null ?: throw  new Exception('No data found.');
            return response($parent->category, 200);

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullParentCategoryName@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
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
}
