<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\{EmploymentRecord, EmploymentFeedback};
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function create(EmploymentRecord $record)
    {
        abort_if($record->employer_id !== auth()->user()->employer->id, 403);
        abort_if($record->feedback()->exists(), 403, 'Feedback already submitted.');
        abort_if($record->status === 'active', 403, 'Cannot submit feedback for an active record.');

        return view('employer.feedback.create', compact('record'));
    }

    public function store(Request $request, EmploymentRecord $record)
    {
        abort_if($record->employer_id !== auth()->user()->employer->id, 403);

        $data = $request->validate([
            'rating_punctuality'   => 'nullable|integer|between:1,5',
            'rating_teamwork'      => 'nullable|integer|between:1,5',
            'rating_communication' => 'nullable|integer|between:1,5',
            'rating_integrity'     => 'nullable|integer|between:1,5',
            'rating_performance'   => 'nullable|integer|between:1,5',
            'general_remarks'      => 'nullable|string|max:2000',
            'strengths'            => 'nullable|string|max:1000',
            'areas_of_improvement' => 'nullable|string|max:1000',
            'would_rehire'         => 'nullable|boolean',
            'conduct_flag'         => 'required|in:clean,minor_issue,major_issue,blacklisted',
        ]);

        EmploymentFeedback::create([
            ...$data,
            'employment_record_id' => $record->id,
            'employer_id'          => $record->employer_id,
            'employee_id'          => $record->employee_id,
        ]);

        return redirect()->route('employer.records.show', $record)
                         ->with('success', 'Feedback submitted successfully.');
    }

    public function edit(EmploymentFeedback $feedback)
    {
        abort_if($feedback->employer_id !== auth()->user()->employer->id, 403);
        return view('employer.feedback.edit', compact('feedback'));
    }

    public function update(Request $request, EmploymentFeedback $feedback)
    {
        abort_if($feedback->employer_id !== auth()->user()->employer->id, 403);

        $data = $request->validate([
            'rating_punctuality'   => 'nullable|integer|between:1,5',
            'rating_teamwork'      => 'nullable|integer|between:1,5',
            'rating_communication' => 'nullable|integer|between:1,5',
            'rating_integrity'     => 'nullable|integer|between:1,5',
            'rating_performance'   => 'nullable|integer|between:1,5',
            'general_remarks'      => 'nullable|string|max:2000',
            'strengths'            => 'nullable|string|max:1000',
            'areas_of_improvement' => 'nullable|string|max:1000',
            'would_rehire'         => 'nullable|boolean',
            'conduct_flag'         => 'required|in:clean,minor_issue,major_issue,blacklisted',
        ]);

        $feedback->update($data);

        return redirect()->route('employer.records.show', $feedback->employmentRecord)
                         ->with('success', 'Feedback updated successfully.');
    }
}
