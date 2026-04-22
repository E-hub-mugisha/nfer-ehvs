<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Employer;
use App\Models\EmploymentRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function getEmployer(): Employer
    {
        return auth()->user()->employer;
    }

    public function index(): View
    {
        $employer = $this->getEmployer();

        abort_unless($employer, 404, 'Employer profile not found. Please complete your company registration.');

        // ── Stats ──────────────────────────────────────────────
        $stats = [
            'total_records'      => $employer->employmentRecords()->count(),
            'active_employees'   => $employer->employmentRecords()->where('status', 'active')->count(),
            'past_employees'     => $employer->employmentRecords()->where('status', 'closed')->count(),
            'pending_confirmation' => $employer->employmentRecords()
                                        ->where('employee_confirmation', 'pending')
                                        ->count(),
            'feedback_given'     => $employer->feedbacks()->count(),
            'open_disputes'      => Dispute::whereHas(
                                        'employmentRecord',
                                        fn($q) => $q->where('employer_id', $employer->id)
                                    )->where('status', 'open')->count(),
            'is_verified'        => $employer->isVerified(),
        ];

        // ── Recent Active Records ──────────────────────────────
        $recentRecords = $employer->employmentRecords()
            ->with('employee.user')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        // ── Exit Reason Summary ────────────────────────────────
        $exitSummary = $employer->employmentRecords()
            ->whereNotNull('exit_reason')
            ->select('exit_reason', DB::raw('count(*) as total'))
            ->groupBy('exit_reason')
            ->orderByDesc('total')
            ->get();

        // ── Pending Feedback (closed records without feedback) ─
        $pendingFeedback = $employer->employmentRecords()
            ->with('employee.user')
            ->where('status', 'closed')
            ->whereDoesntHave('feedback')
            ->latest('end_date')
            ->take(5)
            ->get();

        // ── Pending Employee Confirmations ─────────────────────
        $pendingConfirmations = $employer->employmentRecords()
            ->with('employee.user')
            ->where('employee_confirmation', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // ── Disputed Records ───────────────────────────────────
        $disputedRecords = $employer->employmentRecords()
            ->whereHas('disputes', fn($q) => $q->whereIn('status', ['open', 'under_review']))
            ->with(['employee.user', 'disputes' => fn($q) => $q->whereIn('status', ['open', 'under_review'])])
            ->latest()
            ->take(5)
            ->get();

        return view('employer.dashboard', compact(
            'employer',
            'stats',
            'recentRecords',
            'exitSummary',
            'pendingFeedback',
            'pendingConfirmations',
            'disputedRecords'
        ));
    }
}