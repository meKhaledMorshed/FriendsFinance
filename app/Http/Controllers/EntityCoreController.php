<?php

namespace App\Http\Controllers;

use function PHPUnit\Framework\fileExists;

class EntityCoreController extends Controller
{
    //
    public $entity;

    function __construct()
    {
        $file = file_exists('assets/json/entity_data.json');
        if ($file) {
            $data = file_get_contents('assets/json/entity_data.json');
            $data = json_decode($data);
            $this->entity = $data;
        }
    }
}
