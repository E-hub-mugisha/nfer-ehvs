<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\EmployerFeedback;
use App\Models\EmploymentFeedback;
use App\Models\EmploymentRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month');

        // ── Summary Totals ──────────────────────────────────────
        $summary = [
            'employees'         => Employee::count(),
            'verified_employees'=> Employee::where('is_verified', true)->count(),
            'employers'         => Employer::count(),
            'verified_employers'=> Employer::where('verification_status', 'verified')->count(),
            'records'           => EmploymentRecord::count(),
            'active_records'    => EmploymentRecord::where('status', 'active')->count(),
            'disputes'          => Dispute::count(),
            'resolved_disputes' => Dispute::where('status', 'resolved')->count(),
        ];

        // ── Monthly Registrations (year) ──────────────────────
        $monthlyEmployees = Employee::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyRecords = EmploymentRecord::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Pad to 12 months
        $months = collect(range(1, 12))->map(fn($m) => [
            'label'     => date('M', mktime(0, 0, 0, $m, 1)),
            'employees' => $monthlyEmployees->get($m, 0),
            'records'   => $monthlyRecords->get($m, 0),
        ]);

        // ── Exit Reason Breakdown ──────────────────────────────
        $exitBreakdown = EmploymentRecord::whereNotNull('exit_reason')
            ->select('exit_reason', DB::raw('count(*) as total'))
            ->groupBy('exit_reason')
            ->orderByDesc('total')
            ->get();

        // ── Conduct Flag Distribution ──────────────────────────
        $conductBreakdown = EmploymentFeedback::select(
                'conduct_flag',
                DB::raw('count(distinct employee_id) as total')
            )
            ->groupBy('conduct_flag')
            ->get();

        // ── Top Industries by Employer ─────────────────────────
        $topIndustries = Employer::where('verification_status', 'verified')
            ->select('industry', DB::raw('count(*) as total'))
            ->groupBy('industry')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // ── Employment Type Distribution ───────────────────────
        $employmentTypes = EmploymentRecord::select(
                'employment_type',
                DB::raw('count(*) as total')
            )
            ->groupBy('employment_type')
            ->get();

        // ── Employers by Company Type ──────────────────────────
        $companyTypes = Employer::where('verification_status', 'verified')
            ->select('company_type', DB::raw('count(*) as total'))
            ->groupBy('company_type')
            ->get();

        // ── District Distribution (Employees) ─────────────────
        $districts = Employee::whereNotNull('district')
            ->select('district', DB::raw('count(*) as total'))
            ->groupBy('district')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // ── Dispute Types ──────────────────────────────────────
        $disputeTypes = Dispute::select(
                'dispute_type',
                DB::raw('count(*) as total')
            )
            ->groupBy('dispute_type')
            ->get();

        $availableYears = range(now()->year, 2020);

        return view('admin.reports.index', compact(
            'summary',
            'months',
            'exitBreakdown',
            'conductBreakdown',
            'topIndustries',
            'employmentTypes',
            'companyTypes',
            'districts',
            'disputeTypes',
            'year',
            'availableYears'
        ));
    }

    public function export(Request $request): Response
    {
        $request->validate([
            'type' => 'required|in:employees,employers,records,disputes',
            'format' => 'required|in:csv',
        ]);

        $type = $request->type;

        $data    = [];
        $headers = [];

        switch ($type) {
            case 'employees':
                $headers = ['ID', 'National ID', 'Full Name', 'Gender', 'District', 'Status', 'Verified', 'Registered'];
                Employee::with('user')->chunk(500, function ($employees) use (&$data) {
                    foreach ($employees as $e) {
                        $data[] = [
                            $e->id,
                            $e->national_id,
                            $e->full_name,
                            $e->gender,
                            $e->district,
                            $e->employment_status,
                            $e->is_verified ? 'Yes' : 'No',
                            $e->created_at->format('Y-m-d'),
                        ];
                    }
                });
                break;

            case 'employers':
                $headers = ['ID', 'Company Name', 'Type', 'Industry', 'District', 'Status', 'Registered'];
                Employer::chunk(500, function ($employers) use (&$data) {
                    foreach ($employers as $e) {
                        $data[] = [
                            $e->id,
                            $e->company_name,
                            $e->company_type,
                            $e->industry,
                            $e->district,
                            $e->verification_status,
                            $e->created_at->format('Y-m-d'),
                        ];
                    }
                });
                break;

            case 'records':
                $headers = ['ID', 'Employee', 'Employer', 'Job Title', 'Type', 'Start', 'End', 'Exit Reason', 'Status'];
                EmploymentRecord::with(['employee', 'employer'])->chunk(500, function ($records) use (&$data) {
                    foreach ($records as $r) {
                        $data[] = [
                            $r->id,
                            $r->employee->full_name,
                            $r->employer->company_name,
                            $r->job_title,
                            $r->employment_type,
                            $r->start_date->format('Y-m-d'),
                            $r->end_date?->format('Y-m-d') ?? 'Present',
                            $r->exit_reason ?? '-',
                            $r->status,
                        ];
                    }
                });
                break;

            case 'disputes':
                $headers = ['ID', 'Raised By', 'Type', 'Status', 'Date', 'Resolved'];
                Dispute::with(['raisedBy', 'resolvedBy'])->chunk(500, function ($disputes) use (&$data) {
                    foreach ($disputes as $d) {
                        $data[] = [
                            $d->id,
                            $d->raisedBy->name,
                            $d->dispute_type,
                            $d->status,
                            $d->created_at->format('Y-m-d'),
                            $d->resolved_at?->format('Y-m-d') ?? '-',
                        ];
                    }
                });
                break;
        }

        $filename = "nfer-ehvs-{$type}-" . now()->format('Ymd-His') . '.csv';

        $callback = function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}