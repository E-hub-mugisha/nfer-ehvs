<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'employment_record_id', 'employer_id', 'employee_id',
        'rating_punctuality', 'rating_teamwork', 'rating_communication',
        'rating_integrity', 'rating_performance', 'overall_rating',
        'general_remarks', 'strengths', 'areas_of_improvement',
        'would_rehire', 'conduct_flag', 'is_public',
    ];

    protected $casts = [
        'would_rehire'   => 'boolean',
        'is_public'      => 'boolean',
        'overall_rating' => 'decimal:2',
    ];

    public function employmentRecord(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EmploymentRecord::class);
    }

    public function employer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Auto-compute overall_rating before saving
    protected static function booted(): void
    {
        static::saving(function (self $feedback) {
            $ratings = array_filter([
                $feedback->rating_punctuality,
                $feedback->rating_teamwork,
                $feedback->rating_communication,
                $feedback->rating_integrity,
                $feedback->rating_performance,
            ]);
            if (count($ratings)) {
                $feedback->overall_rating = round(array_sum($ratings) / count($ratings), 2);
            }
        });
    }
}
