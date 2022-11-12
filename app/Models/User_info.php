<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_info extends Model
{
    public $timestamps = false;
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
    public function unAuthAddresses()
    {
        return $this->hasMany(User_address::class, 'uid', 'uid')->where('authBy', null);
    }
    public function unAuthPresentAddress()
    {
        return $this->hasOne(User_address::class, 'uid', 'uid')->where('type', 'Present')->where('authBy', null);
    }
    public function unAuthPermanentAddress()
    {
        return $this->hasOne(User_address::class, 'uid', 'uid')->where('type', 'Permanent')->where('authBy', null);
    }
}
