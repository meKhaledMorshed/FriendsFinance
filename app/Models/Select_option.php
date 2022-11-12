<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Select_option extends Model
{
    use HasFactory;

    protected $hidden = ['insertedBy', 'authBy'];

    public $timestamps = false;
}
