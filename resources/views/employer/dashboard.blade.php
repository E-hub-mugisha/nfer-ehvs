@extends('layouts.app')
@section('title', 'Employer Dashboard')
@section('page-title', 'Company Dashboard')

@section('content')

{{-- UNVERIFIED WARNING BANNER --}}
@unless($employer->isVerified())
<div class="mb-4 p-4 rounded-3 d-flex align-items-start gap-3" style="background:#FEF9C3;border:1px solid #FDE68A">
    <i class="bi bi-hourglass-split fs-4" style="color:#D97706;flex-shrink:0;margin-top:2px"></i>
    <div>
        <div class="fw-bold" style="color:#92400E;font-size:14px">Company Verification Pending</div>
        <div style="color:#92400E;font-size:13px;margin-top:2px">
            Your organisation is awaiting admin verification. You cannot add employment records or access the verification portal until approved.
            @if($employer->verification_status === 'rejected')
                <br><strong>Rejection reason:</strong> {{ $employer->rejection_reason }}
            @endif
        </div>
    </div>
</div>
@endunless

{{-- STATS GRID --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['Total Records',     $stats['total_records'],        'file-earmark-person-fill', 'All employment records'],
        ['Active Employees',  $stats['active_employees'],     'person-check-fill',        'Currently employed'],
        ['Past Employees',    $stats['past_employees'],       'person-dash',              'Closed records'],
        ['Pending Feedback',  $stats['feedback_given'] === 0 ? $stats['past_employees'] : 0, 'chat-right-text', 'Closed without feedback'],
    ];
    @endphp
    @foreach($cards as [$label, $val, $icon, $sub])
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon mb-3"><i class="bi bi-{{ $icon }}"></i></div>
            <div class="stat-value">{{ number_format($val) }}</div>
            <div class="stat-label mt-1">{{ $label }}</div>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px">{{ $sub }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ALERTS ROW --}}
<div class="row g-3 mb-4">
    @if($stats['pending_confirmation'] > 0)
    <div class="col-md-4">
        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#EFF6FF;border:1px solid #BFDBFE">
            <i class="bi bi-clock-history fs-4" style="color:#1D4ED8"></i>
            <div>
                <div class="fw-bold" style="font-size:13.5px;color:#1E3A8A">{{ $stats['pending_confirmation'] }} Pending Confirmations</div>
                <div style="font-size:12px;color:#1D4ED8">Employees have not yet confirmed</div>
            </div>
        </div>
    </div>
    @endif
    @if($stats['open_disputes'] > 0)
    <div class="col-md-4">
        <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:#FEF2F2;border:1px solid #FECACA">
            <i class="bi bi-shield-exclamation fs-4" style="color:#DC2626"></i>
            <div>
                <div class="fw-bold" style="font-size:13.5px;color:#991B1B">{{ $stats['open_disputes'] }} Open Disputes</div>
                <div style="font-size:12px;color:#DC2626">Under admin review</div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-4">
    {{-- ACTIVE EMPLOYEES --}}
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-people me-2"></i>Active Employees</h6>
                <a href="{{ route('employer.records.index', ['status'=>'active']) }}" class="btn btn-outline-navy btn-sm">View All</a>
            </div>
            <div class="card-modern-body p-0">
                @forelse($recentRecords as $rec)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--amber);flex-shrink:0">
                        {{ strtoupper(substr($rec->employee->first_name,0,1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:13.5px">{{ $rec->employee->full_name }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $rec->job_title }} &bull; Since {{ $rec->start_date->format('M Y') }}</div>
                    </div>
                    <span class="badge-nfer badge-{{ $rec->employee_confirmation === 'confirmed' ? 'active' : 'pending' }}" style="font-size:10.5px">
                        {{ $rec->employee_confirmation === 'confirmed' ? '✓ Confirmed' : '⏳ Pending' }}
                    </span>
                    <a href="{{ route('employer.records.show', $rec) }}" class="btn btn-outline-navy btn-sm ms-2">View</a>
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-people display-6 d-block mb-2 opacity-25"></i>
                    No active employees yet.
                    @if($employer->isVerified())
                    <a href="{{ route('employer.records.create') }}" class="d-block mt-2 btn btn-amber btn-sm">Add First Record</a>
                    @endif
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-5">

        {{-- PENDING FEEDBACK --}}
        @if($pendingFeedback->count())
        <div class="card-modern mb-3">
            <div class="card-modern-header">
                <h6><i class="bi bi-chat-right-text me-2" style="color:var(--amber)"></i>Feedback Due</h6>
                <span class="badge bg-warning text-dark rounded-pill">{{ $pendingFeedback->count() }}</span>
            </div>
            <div class="card-modern-body p-0">
                @foreach($pendingFeedback as $rec)
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:13px">{{ $rec->employee->full_name }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted)">Left {{ $rec->end_date?->format('d M Y') }}</div>
                    </div>
                    <a href="{{ route('employer.feedback.create', $rec) }}" class="btn btn-amber btn-sm">Add Feedback</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- EXIT SUMMARY --}}
        @if($exitSummary->count())
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-pie-chart me-2"></i>Exit Summary</h6></div>
            <div class="card-modern-body p-0">
                @php $exitTotal = $exitSummary->sum('total') ?: 1; @endphp
                @foreach($exitSummary as $e)
                <div class="px-4 py-2 d-flex align-items-center gap-3" style="border-bottom:1px solid var(--border)">
                    <div style="flex:1;font-size:12.5px">{{ ucfirst(str_replace('_',' ',$e->exit_reason)) }}</div>
                    <div style="width:60px;height:6px;background:var(--border);border-radius:3px">
                        <div style="height:6px;background:var(--amber);border-radius:3px;width:{{ round($e->total/$exitTotal*100) }}%"></div>
                    </div>
                    <span style="font-size:12px;font-weight:700;color:var(--navy);min-width:20px;text-align:right">{{ $e->total }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- QUICK ACTIONS --}}
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-lightning me-2"></i>Quick Actions</h6></div>
            <div class="card-modern-body d-flex flex-column gap-2">
                @if($employer->isVerified())
                <a href="{{ route('employer.records.create') }}" class="btn btn-navy w-100"><i class="bi bi-plus-circle me-2"></i>Add Employment Record</a>
                <a href="{{ route('employer.search.index') }}" class="btn btn-amber w-100"><i class="bi bi-search-heart me-2"></i>Verify an Employee</a>
                @endif
                <a href="{{ route('employer.profile.edit') }}" class="btn btn-outline-navy w-100"><i class="bi bi-building me-2"></i>Edit Company Profile</a>
            </div>
        </div>
    </div>
</div>

@endsection
