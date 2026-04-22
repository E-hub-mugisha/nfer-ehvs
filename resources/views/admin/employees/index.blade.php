@extends('layouts.app')
@section('title', 'Employees')
@section('page-title', 'Employee Registry')

@section('content')

{{-- SEARCH & FILTERS --}}
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" action="{{ route('admin.employees.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or National ID…" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Employment Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="employed" {{ request('status')==='employed'?'selected':'' }}>Employed</option>
                        <option value="unemployed" {{ request('status')==='unemployed'?'selected':'' }}>Unemployed</option>
                        <option value="self_employed" {{ request('status')==='self_employed'?'selected':'' }}>Self Employed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Verification</label>
                    <select name="verified" class="form-select">
                        <option value="">All</option>
                        <option value="1" {{ request('verified')==='1'?'selected':'' }}>Verified</option>
                        <option value="0" {{ request('verified')==='0'?'selected':'' }}>Unverified</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-navy w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLE --}}
<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-people me-2"></i>Registered Employees
            <span class="ms-2" style="color:var(--text-muted);font-weight:400;font-size:13px">({{ $employees->total() }} total)</span>
        </h6>
    </div>
    <div class="card-modern-body p-0">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>National ID</th>
                    <th>Gender</th>
                    <th>District</th>
                    <th>Current Title</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Registered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--amber);flex-shrink:0">
                                {{ strtoupper(substr($emp->first_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:13px">{{ $emp->full_name }}</div>
                                <div style="font-size:11px;color:var(--text-muted)">{{ $emp->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;font-family:monospace">{{ $emp->national_id }}</td>
                    <td style="font-size:12.5px">{{ ucfirst($emp->gender) }}</td>
                    <td style="font-size:12.5px">{{ $emp->district ?? '—' }}</td>
                    <td style="font-size:12.5px">{{ $emp->current_title ?? '—' }}</td>
                    <td>
                        @php $empStatus = $emp->employment_status; @endphp
                        <span class="badge-nfer {{ $empStatus==='employed' ? 'badge-active' : ($empStatus==='unemployed' ? 'badge-suspended' : 'badge-pending') }}">
                            {{ ucfirst(str_replace('_',' ',$empStatus)) }}
                        </span>
                    </td>
                    <td>
                        @if($emp->is_verified)
                            <span class="verified-shield" style="font-size:10.5px"><i class="bi bi-patch-check-fill me-1"></i>Yes</span>
                        @else
                            <span class="badge-nfer badge-pending">No</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $emp->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.employees.show', $emp) }}" class="btn btn-outline-navy btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5" style="color:var(--text-muted)">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>No employees found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid var(--border)">{{ $employees->links() }}</div>
    @endif
</div>

@endsection
