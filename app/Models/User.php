<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['password'];

    public $timestamps = false;

    use HasFactory;

    public function userinfo()
    {
        return $this->hasOne(User_info::class, 'uid', 'id');
    }

    public function presentAddress()
    {
        return $this->hasOne(User_address::class, 'uid', 'id')->where('type', 'Present');
    }

    public function permanentAddress()
    {
        return $this->hasOne(User_address::class, 'uid', 'id')->where('type', 'Permanent');
    }
    public function usernid()
    {
        return $this->hasOne(User_document::class, 'uid', 'id')->where('type', 'NID');
    }

    public function addresses()
    {
        return $this->hasMany(User_address::class, 'uid', 'id');
    }

    public function documents()
    {
        return $this->hasMany(User_document::class, 'uid', 'id');
    }

    public function contacts()
    {
        return $this->hasMany(Alternate_contact::class, 'uid', 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'uid', 'id');
    }
}
