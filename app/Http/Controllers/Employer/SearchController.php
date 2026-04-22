<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Employer};
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private function checkVerified(): void
    {
        abort_unless(auth()->user()->employer?->isVerified(), 403,
            'Your organisation must be verified to use the verification portal.');
    }

    public function index()
    {
        $this->checkVerified();
        return view('employer.search.index');
    }

    public function results(Request $request)
    {
        $this->checkVerified();

        $request->validate([
            'q' => 'required|string|min:3',
        ]);

        $employees = Employee::verified()
            ->search($request->q)
            ->with('user')
            ->paginate(10)
            ->appends($request->only('q'));

        return view('employer.search.results', compact('employees'));
    }

    public function show(Employee $employee)
    {
        $this->checkVerified();
        abort_unless($employee->is_verified, 404);

        $records = $employee->employmentRecords()
            ->visible()
            ->with(['employer', 'feedback'])
            ->orderByDesc('start_date')
            ->get();

        return view('employer.search.profile', compact('employee', 'records'));
    }
}
