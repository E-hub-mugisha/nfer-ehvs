<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\EmploymentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmploymentRecordController extends Controller
{
    private function getEmployer(): Employer
    {
        return auth()->user()->employer;
    }

    public function index()
    {
        $records = $this->getEmployer()->employmentRecords()
            ->with('employee.user')
            ->latest()
            ->paginate(15);

        return view('employer.records.index', compact('records'));
    }

    public function create()
    {
        return view('employer.records.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'national_id'       => 'required|string|exists:employees,national_id',
            'job_title'         => 'required|string|max:150',
            'department'        => 'nullable|string|max:150',
            'start_date'        => 'required|date|before_or_equal:today',
            'end_date'          => 'nullable|date|after:start_date',
            'employment_type'   => 'required|in:full_time,part_time,contract,internship',
            'starting_salary'   => 'nullable|numeric|min:0',
            'ending_salary'     => 'nullable|numeric|min:0',
            'exit_reason'       => 'nullable|in:resigned,terminated,contract_ended,redundancy,retirement,mutual_agreement,misconduct,absconded,deceased,other',
            'exit_details'      => 'nullable|string|max:1000',
        ]);

        $employee = Employee::where('national_id', $data['national_id'])->firstOrFail();
        $employer = $this->getEmployer();

        // Prevent duplicate active record
        $existing = EmploymentRecord::where('employee_id', $employee->id)
            ->where('employer_id', $employer->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->withErrors(['national_id' => 'This employee already has an active record at your organisation.']);
        }

        DB::transaction(function () use ($data, $employee, $employer) {
            $record = EmploymentRecord::create([
                ...$data,
                'employee_id' => $employee->id,
                'employer_id' => $employer->id,
                'status'      => isset($data['end_date']) ? 'closed' : 'active',
            ]);

            // Update employee employment status if active
            if ($record->status === 'active') {
                $employee->update(['employment_status' => 'employed']);
            }
        });

        return redirect()->route('employer.records.index')
                         ->with('success', 'Employment record created successfully.');
    }

    public function show(EmploymentRecord $record)
    {
        $this->authorize('view', $record);
        $record->load(['employee.user', 'feedback', 'disputes']);
        return view('employer.records.show', compact('record'));
    }

    public function edit(EmploymentRecord $record)
    {
        $this->authorize('update', $record);
        return view('employer.records.edit', compact('record'));
    }

    public function update(Request $request, EmploymentRecord $record)
    {
        $this->authorize('update', $record);

        $data = $request->validate([
            'job_title'       => 'required|string|max:150',
            'department'      => 'nullable|string|max:150',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after:start_date',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'starting_salary' => 'nullable|numeric|min:0',
            'ending_salary'   => 'nullable|numeric|min:0',
            'exit_reason'     => 'nullable|required_with:end_date|in:resigned,terminated,contract_ended,redundancy,retirement,mutual_agreement,misconduct,absconded,deceased,other',
            'exit_details'    => 'nullable|string|max:1000',
        ]);

        $record->update($data);

        return redirect()->route('employer.records.show', $record)
                         ->with('success', 'Record updated successfully.');
    }

    public function close(Request $request, EmploymentRecord $record)
    {
        $this->authorize('update', $record);

        $data = $request->validate([
            'end_date'    => 'required|date|after:' . $record->start_date,
            'exit_reason' => 'required|in:resigned,terminated,contract_ended,redundancy,retirement,mutual_agreement,misconduct,absconded,deceased,other',
            'exit_details'=> 'nullable|string|max:1000',
        ]);

        $record->update([...$data, 'status' => 'closed']);

        return redirect()->route('employer.records.show', $record)
                         ->with('success', 'Employment record closed.');
    }
}
