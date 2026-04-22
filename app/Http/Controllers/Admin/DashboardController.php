<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\EmployerFeedback;
use App\Models\EmploymentFeedback;
use App\Models\EmploymentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_employees'    => Employee::count(),
            'verified_employees' => Employee::where('is_verified', true)->count(),
            'total_employers'    => Employer::count(),
            'verified_employers' => Employer::where('verification_status', 'verified')->count(),
            'pending_employers'  => Employer::where('verification_status', 'pending')->count(),
            'suspended_employers'=> Employer::where('verification_status', 'suspended')->count(),
            'total_records'      => EmploymentRecord::count(),
            'active_records'     => EmploymentRecord::where('status', 'active')->count(),
            'closed_records'     => EmploymentRecord::where('status', 'closed')->count(),
            'open_disputes'      => Dispute::where('status', 'open')->count(),
            'under_review'       => Dispute::where('status', 'under_review')->count(),
            'blacklisted'        => EmploymentFeedback::where('conduct_flag', 'blacklisted')
                                        ->distinct('employee_id')->count('employee_id'),
            'total_users'        => User::count(),
        ];

        // Exit reason breakdown for chart
        $exitReasons = EmploymentRecord::whereNotNull('exit_reason')
            ->select('exit_reason', DB::raw('count(*) as total'))
            ->groupBy('exit_reason')
            ->orderByDesc('total')
            ->get();

        // Monthly record registrations (last 6 months)
        $monthlyRecords = EmploymentRecord::select(
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month', DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        // Recent pending employers
        $recentEmployers = Employer::where('verification_status', 'pending')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Open disputes
        $recentDisputes = Dispute::where('status', 'open')
            ->with(['raisedBy', 'employmentRecord.employee', 'employmentRecord.employer'])
            ->latest()
            ->take(5)
            ->get();

        // Recently registered employees pending verification
        $pendingEmployees = Employee::where('is_verified', false)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'exitReasons',
            'monthlyRecords',
            'recentEmployers',
            'recentDisputes',
            'pendingEmployees'
        ));
    }
}