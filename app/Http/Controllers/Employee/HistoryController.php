<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmploymentRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    private function getEmployee(): Employee
    {
        return auth()->user()->employee;
    }

    public function index(Request $request): View
    {
        $employee = $this->getEmployee();

        abort_unless($employee, 404, 'Please complete your profile first.');

        $query = $employee->employmentRecords()->with(['employer', 'feedback', 'disputes']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('confirmation')) {
            $query->where('employee_confirmation', $request->confirmation);
        }

        $records = $query->orderByDesc('start_date')->get();

        // Summary stats
        $stats = [
            'total'        => $records->count(),
            'active'       => $records->where('status', 'active')->count(),
            'pending'      => $records->where('employee_confirmation', 'pending')->count(),
            'avg_rating'   => $employee->average_rating,
            'total_years'  => $this->calcTotalYears($records),
        ];

        return view('employee.history.index', compact('employee', 'records', 'stats'));
    }

    public function confirm(EmploymentRecord $record): RedirectResponse
    {
        $employee = $this->getEmployee();

        abort_unless($record->employee_id === $employee->id, 403, 'Access denied.');
        abort_unless($record->employee_confirmation === 'pending', 422, 'Record already confirmed or disputed.');

        $record->update([
            'employee_confirmation'  => 'confirmed',
            'employee_confirmed_at'  => now(),
        ]);

        return back()->with('success', 'Employment record confirmed successfully.');
    }

    private function calcTotalYears($records): string
    {
        $totalMonths = 0;
        foreach ($records as $r) {
            $start = $r->start_date;
            $end   = $r->end_date ?? now();
            $totalMonths += $start->diffInMonths($end);
        }
        $years  = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;
        $parts  = [];
        if ($years)  $parts[] = $years . ' yr' . ($years > 1 ? 's' : '');
        if ($months) $parts[] = $months . ' mo';
        return implode(', ', $parts) ?: '0';
    }
}