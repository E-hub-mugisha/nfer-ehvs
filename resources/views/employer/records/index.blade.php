@extends('layouts.app')
@section('title', 'Employment Records')
@section('page-title', 'Employment Records')

@section('content')

{{-- STATS BAR --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#fff;border:1px solid var(--border)">
            <div style="width:38px;height:38px;border-radius:9px;background:#DCFCE7;display:flex;align-items:center;justify-content:center;color:#16A34A;font-size:18px;flex-shrink:0"><i class="bi bi-person-check-fill"></i></div>
            <div><div style="font-family:var(--font-head);font-size:22px;font-weight:800;color:var(--navy)">{{ $stats['active'] }}</div><div style="font-size:11.5px;color:var(--text-muted)">Active</div></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#fff;border:1px solid var(--border)">
            <div style="width:38px;height:38px;border-radius:9px;background:#F1F5F9;display:flex;align-items:center;justify-content:center;color:#475569;font-size:18px;flex-shrink:0"><i class="bi bi-person-dash"></i></div>
            <div><div style="font-family:var(--font-head);font-size:22px;font-weight:800;color:var(--navy)">{{ $stats['closed'] }}</div><div style="font-size:11.5px;color:var(--text-muted)">Closed</div></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#fff;border:1px solid var(--border)">
            <div style="width:38px;height:38px;border-radius:9px;background:#FEF9C3;display:flex;align-items:center;justify-content:center;color:#D97706;font-size:18px;flex-shrink:0"><i class="bi bi-chat-right-text"></i></div>
            <div><div style="font-family:var(--font-head);font-size:22px;font-weight:800;color:var(--navy)">{{ $stats['pending_feedback'] }}</div><div style="font-size:11.5px;color:var(--text-muted)">Need Feedback</div></div>
        </div>
    </div>
    <div class="col-md-3 d-flex align-items-center justify-content-end">
        <a href="{{ route('employer.records.create') }}" class="btn btn-navy">
            <i class="bi bi-plus-circle me-2"></i>Add Employment Record
        </a>
    </div>
</div>

{{-- FILTERS --}}
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search Employee</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or National ID…" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                        <option value="closed" {{ request('status')==='closed'?'selected':'' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Exit Reason</label>
                    <select name="exit_reason" class="form-select">
                        <option value="">All</option>
                        @foreach(['resigned','terminated','contract_ended','redundancy','retirement','mutual_agreement','misconduct','absconded','other'] as $r)
                        <option value="{{ $r }}" {{ request('exit_reason')===$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1"><button type="submit" class="btn btn-navy w-100"><i class="bi bi-search"></i></button></div>
                <div class="col-md-2"><a href="{{ route('employer.records.index') }}" class="btn btn-outline-navy w-100">Reset</a></div>
            </div>
        </form>
    </div>
</div>

{{-- TABLE --}}
<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-file-earmark-person me-2"></i>All Records
            <span class="ms-2" style="font-weight:400;font-size:13px;color:var(--text-muted)">({{ $records->total() }})</span>
        </h6>
    </div>
    <div class="card-modern-body p-0">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Exit Reason</th>
                    <th>Status</th>
                    <th>Confirmed</th>
                    <th>Feedback</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--amber);flex-shrink:0">
                                {{ strtoupper(substr($rec->employee->first_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:13px">{{ $rec->employee->full_name }}</div>
                                <div style="font-size:10.5px;color:var(--text-muted);font-family:monospace">{{ $rec->employee->national_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px">{{ $rec->job_title }}</td>
                    <td style="font-size:12px">{{ ucfirst(str_replace('_',' ',$rec->employment_type)) }}</td>
                    <td style="font-size:12.5px">{{ $rec->start_date->format('d M Y') }}</td>
                    <td style="font-size:12.5px">{{ $rec->end_date ? $rec->end_date->format('d M Y') : '<span style="color:var(--text-muted)">Present</span>' }}</td>
                    <td style="font-size:12px">{{ $rec->exit_reason ? ucfirst(str_replace('_',' ',$rec->exit_reason)) : '—' }}</td>
                    <td><span class="badge-nfer badge-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span></td>
                    <td>
                        @if($rec->employee_confirmation === 'confirmed')
                            <span class="badge-nfer badge-active" style="font-size:10.5px">✓ Yes</span>
                        @elseif($rec->employee_confirmation === 'disputed')
                            <span class="badge-nfer badge-rejected" style="font-size:10.5px">Disputed</span>
                        @else
                            <span class="badge-nfer badge-pending" style="font-size:10.5px">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($rec->feedback)
                            <span class="verified-shield" style="font-size:10.5px"><i class="bi bi-check me-1"></i>Done</span>
                        @elseif($rec->status === 'closed')
                            <a href="{{ route('employer.feedback.create', $rec) }}" class="badge-nfer badge-pending" style="text-decoration:none;font-size:10.5px">Add</a>
                        @else
                            <span style="font-size:12px;color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('employer.records.show', $rec) }}" class="btn btn-outline-navy btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5" style="color:var(--text-muted)">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        No records found.
                        <a href="{{ route('employer.records.create') }}" class="d-block mt-2">Add your first employment record</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid var(--border)">{{ $records->links() }}</div>
    @endif
</div>

@endsection
