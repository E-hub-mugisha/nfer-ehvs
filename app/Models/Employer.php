<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'registration_number', 'tin_number',
        'company_type', 'industry', 'district', 'city', 'address',
        'website', 'logo', 'contact_person', 'contact_email', 'contact_phone',
        'verification_status', 'rejection_reason', 'verified_at', 'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employmentRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmploymentRecord::class);
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmploymentFeedback::class);
    }

    public function verifiedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}
