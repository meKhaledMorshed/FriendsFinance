<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_address extends Model
{
    use HasFactory;

    public function userinfo()
    {
        return $this->belongsTo(User_info::class, 'uid', 'uid');
    }
}
