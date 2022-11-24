<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    //
    private function adminUID()
    {
        return session()->get('userID');
    }


    public function create(Request $request)
    {
        try {
            $request->validate([
                'id'        => 'nullable|exists:transactions,id',
                'debit'     => 'required|exists:accounts,accountNumber',
                'credit'    => 'required|exists:accounts,accountNumber',
                'amount'    => 'required|numeric',
                'narration' => 'required|string|max:255'
            ]);

            $admin = Admin::where('uid', $this->adminUID())->first('branchID');

            $data = [
                'branchID'      => $admin->branchID,
                'debitAccount'  => $request->debit,
                'creditAccount' => $request->debit,
                'amount'        => $request->amount,
                'narration'     => $request->narration,
                'isAuth'        => null,
                'authBy'        => null,
            ];

            if ($request->amount <= 1000) {
                $data['isAuth'] = 1;
                $data['authBy'] = $this->adminUID();
            }

            if (isset($request->id) && $request->id != null) {

                $check = DB::table('transactions')->find($request->id, 'isAuth');
                if ($check->isAuth == 1) {
                    return response('This transaction already approved. Contact with administrator.', 403);
                }
                if ($check->isAuth == -1) {
                    return response('This transaction already rejected. Contact with administrator.', 403);
                }

                $data['modifiedBy'] = $this->adminUID();

                $save = DB::table('transactions')->where('id', $request->id)->update($data);

                //
            } else {

                $data['insertedBy'] = $this->adminUID();

                $save = DB::table('transactions')->insert($data);
            }

            DB::beginTransaction();
            $save ?: throw new Exception('Data not saved to DB.');
            DB::commit();
            return response('Request successfully executed', 201);

            //try end here
        } catch (\Exception $e) {
            DB::rollBack();
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from create@TransactionController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Request not executed', 406);
        }
    }

    public function pullTransactions($filter = null)
    {
        try {

            if ($filter == null) {
                $results = Transaction::latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filter = strtolower($filter);

            if ($filter == 'all') {
                $results = Transaction::latest('modifiedDate')->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filters = ['authorize' => 1, 'unauthorize' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $results = Transaction::where('isAuth', $filter)->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            if ($filter != null) {
                $results = Transaction::where('id', $filter)
                    ->orwhere('branchID', 'like', '%' . $filter . '%')
                    ->orwhere('catID', 'like', '%' . $filter . '%')
                    ->orwhere('accountName', 'like', '%' . $filter . '%')
                    ->orwhere('accountNumber', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(10)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            throw  new Exception('No data found.');

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullAccounts@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No Transaction found', 404);
        }
    }
    public function pullTxnByAdmin($filter = null)
    {
        try {

            if ($filter == null) {
                $results = Transaction::where('insertedBy', $this->adminUID())->latest('modifiedDate')->limit(8)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filter = strtolower($filter);

            if ($filter == 'all') {
                $results = Transaction::where('insertedBy', $this->adminUID())->latest('modifiedDate')->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            $filters = ['authorize' => 1, 'unauthorized' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($filter, $filters)) {
                $filter = $filters[$filter];

                $results = Transaction::where('insertedBy', $this->adminUID())->where('isAuth', $filter)->latest('modifiedDate')->limit(8)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            if ($filter != null) {
                $results = Transaction::where('insertedBy', $this->adminUID())->where('id', $filter)
                    ->orwhere('branchID', 'like', '%' . $filter . '%')
                    ->orwhere('catID', 'like', '%' . $filter . '%')
                    ->orwhere('accountName', 'like', '%' . $filter . '%')
                    ->orwhere('accountNumber', 'like', '%' . $filter . '%')
                    ->orwhere('remarks', 'like', '%' . $filter . '%')
                    ->latest('modifiedDate')->limit(8)->get();
                $results->count() > 0 ?: throw  new Exception('No data found.');
                return response($results->toJson(), 200);
            }

            throw  new Exception('No data found.');

            //end

        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from pullAccounts@AccountController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No Data found', 404);
        }
    }


    // change user's address status  
    public function txnAuthorization($change = null, $id = null)
    {
        try {

            $changeArray = ['unauthorize' => 0, 'reject' => -1, 'pending' => null];

            if (array_key_exists($change, $changeArray) && $id != null) {
                $value = $changeArray[$change];
                $update = DB::table('transactions')->where('id', $id)->update(['isAuth' => $value, 'authBy' => null, 'modifiedBy' => $this->adminUID()]);
            }

            if ($change == 'authorize' && $id != null) {
                $update = DB::table('transactions')->where('id', $id)->update(['isAuth' => 1, 'authBy' => $this->adminUID()]);
            }

            $update ?: throw new Exception($change . ' request is not successfull');
            return response($change . ' request is successfull', 201);

            //try end
        } catch (Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from txnAuthorization@AuthorizationController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            //send response to Requester
            return response('Request failed.', 304);
        }
    }


    //
}
