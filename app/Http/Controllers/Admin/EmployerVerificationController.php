<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = Employer::with('user');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('company_name', 'like', "%{$q}%")
                    ->orWhere('registration_number', 'like', "%{$q}%")
                    ->orWhere('tin_number', 'like', "%{$q}%");
            });
        }

        if (in_array($status, ['pending', 'verified', 'rejected', 'suspended'])) {
            $query->where('verification_status', $status);
        }

        if ($request->filled('company_type')) {
            $query->where('company_type', $request->company_type);
        }

        $employers = $query->latest()->paginate(20)->appends($request->query());

        $counts = [
            'pending'   => Employer::where('verification_status', 'pending')->count(),
            'verified'  => Employer::where('verification_status', 'verified')->count(),
            'rejected'  => Employer::where('verification_status', 'rejected')->count(),
            'suspended' => Employer::where('verification_status', 'suspended')->count(),
        ];

        return view('admin.employers.index', compact('employers', 'status', 'counts'));
    }

    public function show(Employer $employer): View
    {
        $employer->load([
            'user',
            'verifiedBy',
            'employmentRecords.employee.user',
            'feedbacks.employee',
        ]);

        $recordCount   = $employer->employmentRecords()->count();
        $activeCount   = $employer->employmentRecords()->where('status', 'active')->count();
        $feedbackCount = $employer->feedbacks()->count();

        return view('admin.employers.show', compact(
            'employer',
            'recordCount',
            'activeCount',
            'feedbackCount'
        ));
    }

    public function verify(Employer $employer): RedirectResponse
    {
        $employer->update([
            'verification_status' => 'verified',
            'verified_at'         => now(),
            'verified_by'         => auth()->id(),
            'rejection_reason'    => null,
        ]);

        $employer->user->update(['is_verified' => true]);

        return back()->with('success', "Employer '{$employer->company_name}' has been verified successfully.");
    }

    public function reject(Request $request, Employer $employer): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $employer->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
            'verified_by'         => auth()->id(),
        ]);

        return back()->with('success', "Employer application for '{$employer->company_name}' has been rejected.");
    }

    public function suspend(Request $request, Employer $employer): RedirectResponse
    {
        $request->validate([
            'suspension_reason' => 'nullable|string|max:500',
        ]);

        $employer->update([
            'verification_status' => 'suspended',
            'rejection_reason'    => $request->suspension_reason,
        ]);

        $employer->user->update(['is_active' => false]);

        return back()->with('success', "Employer '{$employer->company_name}' has been suspended.");
    }

    public function reinstate(Employer $employer): RedirectResponse
    {
        $employer->update([
            'verification_status' => 'verified',
            'rejection_reason'    => null,
            'verified_by'         => auth()->id(),
        ]);

        $employer->user->update(['is_active' => true]);

        return back()->with('success', "Employer '{$employer->company_name}' has been reinstated.");
    }
}