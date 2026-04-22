<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private function getEmployee(): Employee
    {
        return auth()->user()->employee;
    }

    public function show(): View
    {
        $employee = $this->getEmployee();

        abort_unless($employee, 404, 'Please complete your profile first.');

        $employee->load([
            'user',
            'skills',
            'employmentRecords.employer',
            'feedbacks.employer',
        ]);

        return view('employee.profile.show', compact('employee'));
    }

    public function edit(): View
    {
        $employee = $this->getEmployee();
        $skills   = Skill::orderBy('category')->orderBy('name')->get()->groupBy('category');

        if (!$employee) {
            return view('employee.profile.create', compact('skills'));
        }

        $mySkillIds = $employee->skills()->pluck('skills.id')->toArray();

        return view('employee.profile.edit', compact('employee', 'skills', 'mySkillIds'));
    }

    public function update(Request $request): RedirectResponse
    {
        $employee = $this->getEmployee();

        $data = $request->validate([
            'national_id'       => 'required|string|max:20|unique:employees,national_id,' . ($employee?->id ?? 0),
            'first_name'        => 'required|string|max:80',
            'last_name'         => 'required|string|max:80',
            'middle_name'       => 'nullable|string|max:80',
            'date_of_birth'     => 'required|date|before:today',
            'gender'            => 'required|in:male,female,other',
            'nationality'       => 'required|string|max:80',
            'district'          => 'nullable|string|max:80',
            'sector'            => 'nullable|string|max:80',
            'address'           => 'nullable|string|max:500',
            'current_title'     => 'nullable|string|max:150',
            'bio'               => 'nullable|string|max:1000',
            'linkedin_url'      => 'nullable|url|max:200',
            'profile_photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'skills'            => 'nullable|array',
            'skills.*'          => 'exists:skills,id',
            'proficiency'       => 'nullable|array',
            'proficiency.*'     => 'in:beginner,intermediate,advanced,expert',
        ]);

        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            $photo    = $request->file('profile_photo');
            $filename = 'employee_' . auth()->id() . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/employees'), $filename);
            $data['profile_photo'] = 'images/employees/' . $filename;
        }

        $skillData = [];
        if (!empty($data['skills'])) {
            foreach ($data['skills'] as $skillId) {
                $skillData[$skillId] = [
                    'proficiency' => $data['proficiency'][$skillId] ?? 'intermediate',
                ];
            }
        }

        unset($data['skills'], $data['proficiency']);

        if ($employee) {
            $employee->update($data);
            // Sync skills
            $employee->skills()->sync($skillData);

            // Update user name from profile
            auth()->user()->update([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            ]);
        } else {
            $employee = Employee::create([
                ...$data,
                'user_id' => auth()->id(),
            ]);
            $employee->skills()->sync($skillData);

            auth()->user()->update([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            ]);
        }

        return redirect()->route('employee.profile.show')
                         ->with('success', 'Profile updated successfully.');
    }
}