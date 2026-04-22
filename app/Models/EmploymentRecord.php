<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'employer_id', 'job_title', 'department',
        'start_date', 'end_date', 'employment_type',
        'starting_salary', 'ending_salary', 'exit_reason', 'exit_details',
        'status', 'employee_confirmation', 'employee_confirmed_at',
        'is_visible', 'admin_note',
    ];

    protected $casts = [
        'start_date'            => 'date',
        'end_date'              => 'date',
        'employee_confirmed_at' => 'datetime',
        'is_visible'            => 'boolean',
        'starting_salary'       => 'decimal:2',
        'ending_salary'         => 'decimal:2',
    ];

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function employer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function feedback(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmploymentFeedback::class);
    }

    public function disputes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    // Computed attributes
    public function getDurationAttribute(): string
    {
        $start = $this->start_date;
        $end   = $this->end_date ?? now();
        $diff  = $start->diff($end);
        $parts = [];
        if ($diff->y) $parts[] = $diff->y . ' yr' . ($diff->y > 1 ? 's' : '');
        if ($diff->m) $parts[] = $diff->m . ' mo';
        return implode(', ', $parts) ?: 'Less than a month';
    }

    public function getIsCurrentAttribute(): bool
    {
        return is_null($this->end_date) && $this->status === 'active';
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
