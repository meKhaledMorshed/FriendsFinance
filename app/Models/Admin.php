<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    public function permission()
    {
        return $this->hasOne(Permission::class, 'adminID', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
    public function userinfo()
    {
        return $this->belongsTo(User_info::class, 'uid', 'uid');
    }
    public function title()
    {
        return $this->belongsTo(Admin_title::class, 'titleID', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchID', 'id');
    }
}
