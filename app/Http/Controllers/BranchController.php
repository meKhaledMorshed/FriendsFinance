<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public $name = null;
    public $type = null;
    public $details = null;

    function __construct($id=null)
    {
        $result = DB::table('branches')->where('id', '=', $id)->first();
        if($result){
            $this->name = $result->name;
            $this->type = $result->type;
            $this->details = $result->details;
        }
    }
    


}
