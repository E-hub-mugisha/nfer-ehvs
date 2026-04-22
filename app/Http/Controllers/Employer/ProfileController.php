<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private function getEmployer(): Employer
    {
        return auth()->user()->employer;
    }

    public function show(): View
    {
        $employer = $this->getEmployer();

        abort_unless($employer, 404, 'Please complete your company profile first.');

        $employer->load(['user', 'verifiedBy', 'employmentRecords.employee.user']);

        $stats = [
            'total_records'   => $employer->employmentRecords()->count(),
            'active_employees'=> $employer->employmentRecords()->where('status', 'active')->count(),
            'feedback_given'  => $employer->feedbacks()->count(),
        ];

        return view('employer.profile.show', compact('employer', 'stats'));
    }

    public function edit(): View
    {
        $employer = $this->getEmployer();

        // If no employer profile yet, go to create
        if (!$employer) {
            return view('employer.profile.create');
        }

        return view('employer.profile.edit', compact('employer'));
    }

    public function update(Request $request): RedirectResponse
    {
        $employer = $this->getEmployer();

        $data = $request->validate([
            'company_name'      => 'required|string|max:200',
            'registration_number'=> 'required|string|max:50|unique:employers,registration_number,' . ($employer?->id ?? 0),
            'tin_number'        => 'nullable|string|max:20|unique:employers,tin_number,' . ($employer?->id ?? 0),
            'company_type'      => 'required|in:private_company,public_institution,ngo,parastatal,embassy,other',
            'industry'          => 'required|string|max:100',
            'district'          => 'required|string|max:80',
            'city'              => 'nullable|string|max:80',
            'address'           => 'required|string|max:500',
            'website'           => 'nullable|url|max:200',
            'contact_person'    => 'required|string|max:150',
            'contact_email'     => 'required|email|max:150',
            'contact_phone'     => 'required|string|max:20',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'employer_' . auth()->id() . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images/employers'), $filename);
            $data['logo'] = 'images/employers/' . $filename;
        }

        if ($employer) {
            // Update existing
            // Reset to pending if key registration fields changed
            $resetFields = ['company_name', 'registration_number', 'tin_number'];
            $needsReverification = collect($resetFields)->contains(
                fn($field) => isset($data[$field]) && $data[$field] !== $employer->$field
            );

            if ($needsReverification && $employer->verification_status === 'verified') {
                $data['verification_status'] = 'pending';
                $data['verified_at']          = null;
                $data['verified_by']          = null;
                auth()->user()->update(['is_verified' => false]);
            }

            $employer->update($data);
            $message = 'Company profile updated.';
        } else {
            // Create new profile
            Employer::create([
                ...$data,
                'user_id'             => auth()->id(),
                'verification_status' => 'pending',
            ]);
            $message = 'Company profile created. Awaiting admin verification.';
        }

        return redirect()->route('employer.profile.show')
                         ->with('success', $message);
    }
}