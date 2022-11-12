<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    public $title = null; 
    public $titleDescription = null;

    function __construct($id=null)
    {
        $result = DB::table('designations')->where('id', '=', $id)->first();
        if($result){ 
            $this->title = $result->title;
            $this->titleDescription = $result->titleDescription;
        }
    }
}
