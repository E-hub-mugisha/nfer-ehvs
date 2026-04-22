<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'national_id', 'first_name', 'last_name', 'middle_name',
        'date_of_birth', 'gender', 'nationality', 'district', 'sector', 'address',
        'profile_photo', 'current_title', 'bio', 'linkedin_url',
        'employment_status', 'is_verified', 'verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'verified_at'   => 'datetime',
        'is_verified'   => 'boolean',
    ];

    // Relationships
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employmentRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmploymentRecord::class)->orderByDesc('start_date');
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmploymentFeedback::class);
    }

    public function skills(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'employee_skill')
                    ->withPivot('proficiency')
                    ->withTimestamps();
    }

    public function disputes(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Dispute::class, EmploymentRecord::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->feedbacks()->avg('overall_rating') ?? 0, 1);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('national_id', 'like', "%{$term}%")
              ->orWhere('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%");
        });
    }
}
