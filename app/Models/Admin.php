<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Allowed roles
    const ROLES = ['master', 'super_admin', 'authorizer', 'accountant', 'teller', 'auditor'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isAuthorizer(): bool
    {
        return in_array($this->role, ['master', 'super_admin', 'authorizer']);
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['master', 'super_admin', 'editor']);
    }

    public function isMaster(): bool
    {
        return in_array($this->role, ['master', 'super_admin']);
    }
}
