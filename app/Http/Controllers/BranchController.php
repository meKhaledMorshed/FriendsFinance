<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public $name = null;
    public $type = null;
    public $details = null;

    function __construct($id = null)
    {
        $result = DB::table('branches')->where('id', '=', $id)->first();
        if ($result) {
            $this->name = $result->name;
            $this->type = $result->type;
            $this->details = $result->details;
        }
    }

    public function pullBranch()
    {
        try {

            $branches = Branch::all()->toJson();

            return response($branches);
            //end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from viewBranch@BranchController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('No data found', 401);
        }
    }

    public function addOrUpdateBranch(Request $request)
    {
        try {

            $request->validate([
                'branchName' => 'required|string|min:3|max:50',
                'type' => 'required',
                'address' => 'string'
            ]);

            $data = [
                'branchName' => $request->branchName,
                'type' => $request->type,
                'address' => $request->address,
                'isActive' => $request->status,
                'remarks' => $request->remarks,
                'isAuth' => $request->authorization,
                'AuthBy' => session()->get('userID'),

            ];

            if (!isset($request->id) || $request->id == null) {
                $data['insertedBy'] = session()->get('userID');

                DB::table('branches')->insert($data) ?: throw new Exception('Branch not Added.');
                return response('Branch Successfully added', 201);
            } else if (isset($request->id) && $request->id != null) {
                $data['modifiedBy'] = session()->get('userID');

                DB::table('branches')->where('id', $request->id)->update($data) ?: throw new Exception('Branch not updated.');
                return response('Branch Successfully updated', 201);
            }
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from addOrUpdateBranch@BranchController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return response('Data not inserted', 401);
        }
    }
}
