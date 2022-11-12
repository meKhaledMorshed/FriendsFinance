<?php

namespace App\Http\Controllers;

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

    public function viewBranch()
    {
        try {





            return view('backend.branch.index');





            //end
        } catch (\Exception $e) {
            date_default_timezone_set('Asia/Dhaka');
            error_log("Error from viewBranch@BranchController | " . date('d M Y H:i:s', time()) . " | " . $e->getMessage());
            return view('backend.dashboard')->with('error', 'Requested url failed.');
        }
    }
}
