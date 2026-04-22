@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Government Oversight Dashboard')

@section('content')

{{-- STAT GRID --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Employees',    'value'=>number_format($stats['total_employees']),   'sub'=>number_format($stats['verified_employees']).' verified',  'icon'=>'people-fill',          'color'=>'#0D2137'],
        ['label'=>'Total Employers',    'value'=>number_format($stats['total_employers']),   'sub'=>number_format($stats['verified_employers']).' verified',  'icon'=>'building-fill',        'color'=>'#1A3A5C'],
        ['label'=>'Pending Approvals',  'value'=>number_format($stats['pending_employers']), 'sub'=>'Awaiting review',                                        'icon'=>'hourglass-split',      'color'=>'#D97706'],
        ['label'=>'Employment Records', 'value'=>number_format($stats['total_records']),     'sub'=>number_format($stats['active_records']).' active',        'icon'=>'file-earmark-person-fill','color'=>'#0D2137'],
        ['label'=>'Open Disputes',      'value'=>number_format($stats['open_disputes']),     'sub'=>number_format($stats['under_review']).' under review',    'icon'=>'shield-exclamation',   'color'=>'#DC2626'],
        ['label'=>'Blacklisted',        'value'=>number_format($stats['blacklisted']),       'sub'=>'Conduct-flagged employees',                              'icon'=>'ban-fill',             'color'=>'#7C3AED'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-md-4 col-lg-2" style="min-width:200px; flex:1">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon"><i class="bi bi-{{ $c['icon'] }}"></i></div>
            </div>
            <div class="stat-value">{{ $c['value'] }}</div>
            <div class="stat-label mt-1">{{ $c['label'] }}</div>
            <div class="small mt-1" style="color:var(--text-muted)">{{ $c['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    {{-- PENDING EMPLOYERS --}}
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-building-check me-2 text-warning"></i>Pending Employer Approvals</h6>
                <a href="{{ route('admin.employers.index') }}" class="btn btn-outline-navy btn-sm">View All</a>
            </div>
            <div class="card-modern-body p-0">
                @forelse($recentEmployers as $emp)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:800;font-size:16px;color:var(--amber);flex-shrink:0">
                        {{ strtoupper(substr($emp->company_name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:13.5px">{{ $emp->company_name }}</div>
                        <div class="small" style="color:var(--text-muted)">
                            {{ ucfirst(str_replace('_',' ',$emp->company_type)) }} &bull; {{ $emp->district }}
                        </div>
                    </div>
                    <div class="small text-end" style="color:var(--text-muted); white-space:nowrap">
                        {{ $emp->created_at->diffForHumans() }}
                    </div>
                    <a href="{{ route('admin.employers.show', $emp) }}" class="btn btn-amber btn-sm ms-2">Review</a>
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-check-circle display-6 text-success d-block mb-2"></i>
                    No pending approvals
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- OPEN DISPUTES --}}
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-shield-exclamation me-2 text-danger"></i>Open Disputes</h6>
                <a href="{{ route('admin.disputes.index') }}" class="btn btn-outline-navy btn-sm">View All</a>
            </div>
            <div class="card-modern-body p-0">
                @forelse($recentDisputes as $d)
                <div class="d-flex align-items-start gap-3 px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div style="width:34px;height:34px;border-radius:50%;background:#FEE2E2;display:flex;align-items:center;justify-content:center;color:#DC2626;flex-shrink:0">
                        <i class="bi bi-flag-fill" style="font-size:13px"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:13px">{{ $d->raisedBy->name }}</div>
                        <div class="small" style="color:var(--text-muted)">
                            {{ ucfirst(str_replace('_',' ',$d->dispute_type)) }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $d->created_at->diffForHumans() }}</div>
                    </div>
                    <a href="{{ route('admin.disputes.show', $d) }}" class="btn btn-outline-navy btn-sm" style="white-space:nowrap">Review</a>
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-shield-check display-6 text-success d-block mb-2"></i>
                    No open disputes
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- PENDING EMPLOYEE VERIFICATIONS --}}
<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-person-check me-2" style="color:var(--amber)"></i>Employees Pending ID Verification</h6>
        <a href="{{ route('admin.employees.index', ['verified'=>'0']) }}" class="btn btn-outline-navy btn-sm">View All</a>
    </div>
    <div class="card-modern-body p-0">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>National ID</th>
                    <th>District</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingEmployees as $emp)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--amber)">
                                {{ strtoupper(substr($emp->first_name,0,1)) }}
                            </div>
                            <span class="fw-semibold" style="font-size:13px">{{ $emp->full_name }}</span>
                        </div>
                    </td>
                    <td style="font-size:12.5px;font-family:monospace">{{ $emp->national_id }}</td>
                    <td style="font-size:12.5px">{{ $emp->district ?? '—' }}</td>
                    <td><span class="badge-nfer badge-pending">Unverified</span></td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $emp->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.employees.show', $emp) }}" class="btn btn-outline-navy btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted)">All employees are verified</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection