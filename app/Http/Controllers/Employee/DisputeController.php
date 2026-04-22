<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Employee;
use App\Models\EmploymentRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisputeController extends Controller
{
    private function getEmployee(): Employee
    {
        return auth()->user()->employee;
    }

    public function index(): View
    {
        $disputes = Dispute::where('raised_by', auth()->id())
            ->with([
                'employmentRecord.employer',
                'resolvedBy',
            ])
            ->latest()
            ->paginate(15);

        $counts = [
            'open'         => Dispute::where('raised_by', auth()->id())->where('status', 'open')->count(),
            'under_review' => Dispute::where('raised_by', auth()->id())->where('status', 'under_review')->count(),
            'resolved'     => Dispute::where('raised_by', auth()->id())->where('status', 'resolved')->count(),
            'dismissed'    => Dispute::where('raised_by', auth()->id())->where('status', 'dismissed')->count(),
        ];

        return view('employee.disputes.index', compact('disputes', 'counts'));
    }

    public function store(Request $request, EmploymentRecord $record): RedirectResponse
    {
        $employee = $this->getEmployee();

        // Verify the record belongs to this employee
        abort_unless($record->employee_id === $employee->id, 403, 'Access denied.');

        // Only allow disputes on confirmed or closed records
        abort_unless(
            in_array($record->employee_confirmation, ['confirmed', 'pending']),
            422,
            'You cannot raise a dispute on this record.'
        );

        // Prevent duplicate open dispute for the same record
        $existingOpen = Dispute::where('employment_record_id', $record->id)
            ->where('raised_by', auth()->id())
            ->whereIn('status', ['open', 'under_review'])
            ->exists();

        if ($existingOpen) {
            return back()->withErrors(['dispute' => 'You already have an open dispute for this record.']);
        }

        $data = $request->validate([
            'dispute_type'        => 'required|in:incorrect_dates,wrong_exit_reason,unfair_feedback,false_record,other',
            'description'         => 'required|string|min:30|max:2000',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle supporting document upload
        if ($request->hasFile('supporting_document')) {
            $file     = $request->file('supporting_document');
            $filename = 'dispute_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/disputes'), $filename);
            $data['supporting_document'] = 'images/disputes/' . $filename;
        }

        Dispute::create([
            ...$data,
            'employment_record_id' => $record->id,
            'raised_by'            => auth()->id(),
            'status'               => 'open',
        ]);

        // Mark the record as disputed
        $record->update(['employee_confirmation' => 'disputed']);

        return redirect()->route('employee.dispute.index')
                         ->with('success', 'Dispute submitted successfully. The administration team will review it.');
    }

    public function show(Dispute $dispute): View
    {
        abort_unless($dispute->raised_by === auth()->id(), 403, 'Access denied.');

        $dispute->load([
            'employmentRecord.employer',
            'employmentRecord.feedback',
            'resolvedBy',
        ]);

        return view('employee.disputes.show', compact('dispute'));
    }
}