<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Employee;
use App\Models\EmploymentRecord;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function getEmployee(): Employee
    {
        return auth()->user()->employee;
    }

    public function index(): View
    {
        $employee = $this->getEmployee();

        abort_unless($employee, 404, 'Employee profile not found. Please complete your profile.');

        // ── Stats ──────────────────────────────────────────────
        $stats = [
            'total_employers'      => $employee->employmentRecords()->distinct('employer_id')->count('employer_id'),
            'active_record'        => $employee->employmentRecords()->where('status', 'active')->exists(),
            'pending_confirmation' => $employee->employmentRecords()
                                        ->where('employee_confirmation', 'pending')
                                        ->count(),
            'open_disputes'        => Dispute::where('raised_by', auth()->id())
                                        ->whereIn('status', ['open', 'under_review'])
                                        ->count(),
            'average_rating'       => $employee->average_rating,
            'is_verified'          => $employee->is_verified,
            'feedbacks_count'      => $employee->feedbacks()->count(),
        ];

        // ── Current Job ────────────────────────────────────────
        $currentJob = $employee->employmentRecords()
            ->with('employer')
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        // ── Records Pending Confirmation ───────────────────────
        $pendingRecords = $employee->employmentRecords()
            ->with('employer')
            ->where('employee_confirmation', 'pending')
            ->latest()
            ->take(3)
            ->get();

        // ── Recent Feedback ────────────────────────────────────
        $recentFeedback = $employee->feedbacks()
            ->with('employer')
            ->latest()
            ->take(3)
            ->get();

        // ── Employment Timeline (last 3) ───────────────────────
        $recentRecords = $employee->employmentRecords()
            ->with('employer')
            ->orderByDesc('start_date')
            ->take(3)
            ->get();

        // ── Open Disputes ──────────────────────────────────────
        $openDisputes = Dispute::where('raised_by', auth()->id())
            ->with(['employmentRecord.employer'])
            ->whereIn('status', ['open', 'under_review'])
            ->latest()
            ->take(3)
            ->get();

        return view('employee.dashboard', compact(
            'employee',
            'stats',
            'currentJob',
            'pendingRecords',
            'recentFeedback',
            'recentRecords',
            'openDisputes'
        ));
    }
}