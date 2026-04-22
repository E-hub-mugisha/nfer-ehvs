<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'account_type', 'is_verified', 'is_active', 'profile_photo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // Relationships
    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function employer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employer::class);
    }

    public function disputes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dispute::class, 'raised_by');
    }

    // Helper scopes
    public function scopeEmployees($query)
    {
        return $query->where('account_type', 'employee');
    }

    public function scopeEmployers($query)
    {
        return $query->where('account_type', 'employer');
    }

    public function isAdmin(): bool
    {
        return $this->account_type === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->account_type === 'employer';
    }

    public function isEmployee(): bool
    {
        return $this->account_type === 'employee';
    }
}
