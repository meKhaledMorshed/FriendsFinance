<?php

namespace App\Http\Controllers;


class ApiController extends Controller
{
    //

    public function selectOption($key = null, $parent = null, $group = null)
    {
        $selectOption =  new SelectOptionController();
        return $selectOption->datalist($key, $parent, $group);
    }
}
