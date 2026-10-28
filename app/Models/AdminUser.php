<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'admin_users';

    public $timestamps = false;

    protected $fillable = [
        'username', 'password_hash', 'full_name', 'email', 'role', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /** Laravel's auth system looks for getAuthPassword() - point it at our password_hash column. */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }
}
