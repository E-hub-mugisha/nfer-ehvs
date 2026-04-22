<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // USERS (all account types)
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = User::query()->with(['roles']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'employee.skills', 'employee.employmentRecords.employer', 'employer']);
        return view('admin.users.show', compact('user'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'account_type' => 'required|in:employee,employer,admin',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            ...$data,
            'password'    => Hash::make($data['password']),
            'is_verified' => true,
        ]);

        $user->assignRole($data['account_type']);

        return redirect()->route('admin.users.index')
                         ->with('success', "User '{$user->name}' created successfully.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot deactivate your own account.');

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User account {$status} successfully.");
    }

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEES (admin view)
    // ─────────────────────────────────────────────────────────────

    public function employees(Request $request): View
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('national_id', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === '1');
        }

        $employees = $query->latest()->paginate(20)->appends($request->query());

        return view('admin.employees.index', compact('employees'));
    }

    public function showEmployee(Employee $employee): View
    {
        $employee->load([
            'user',
            'skills',
            'employmentRecords.employer',
            'employmentRecords.feedback',
            'feedbacks.employer',
        ]);

        return view('admin.employees.show', compact('employee'));
    }

    public function verifyEmployee(Employee $employee): RedirectResponse
    {
        $employee->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $employee->user->update(['is_verified' => true]);

        return back()->with('success', "Employee '{$employee->full_name}' has been verified.");
    }
}