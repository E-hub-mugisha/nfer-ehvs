@extends('layouts.app')
@section('title', $employer->company_name)
@section('page-title', 'Employer Review')

@section('content')

<div class="mb-3">
    <a href="{{ route('admin.employers.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Employers
    </a>
</div>

<div class="row g-4">

    {{-- LEFT: COMPANY PROFILE --}}
    <div class="col-lg-4">

        {{-- Profile Card --}}
        <div class="card-modern mb-3">
            <div class="card-modern-body text-center" style="padding:32px 24px">
                @if($employer->logo)
                    <img src="{{ asset($employer->logo) }}" alt="" style="width:80px;height:80px;border-radius:16px;object-fit:cover;margin-bottom:12px">
                @else
                    <div style="width:72px;height:72px;border-radius:16px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:28px;font-weight:800;color:var(--amber);margin:0 auto 12px">
                        {{ strtoupper(substr($employer->company_name,0,1)) }}
                    </div>
                @endif
                <h5 class="mb-1" style="font-family:var(--font-head);color:var(--navy)">{{ $employer->company_name }}</h5>
                <div style="font-size:13px;color:var(--text-muted)">{{ $employer->industry }}</div>
                <div class="mt-3">
                    <span class="badge-nfer badge-{{ $employer->verification_status }} d-inline-flex">
                        <i class="bi bi-{{ $employer->verification_status==='verified' ? 'patch-check-fill' : ($employer->verification_status==='pending' ? 'hourglass-split' : 'x-circle-fill') }} me-1"></i>
                        {{ ucfirst($employer->verification_status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-2 mb-3">
            @foreach([['Employment Records',$recordCount,'file-earmark-person'],['Active Employees',$activeCount,'person-check'],['Feedback Given',$feedbackCount,'chat-quote']] as [$label,$val,$icon])
            <div class="col-4">
                <div class="text-center p-3 rounded-3" style="background:#fff;border:1px solid var(--border)">
                    <div style="font-family:var(--font-head);font-size:20px;font-weight:800;color:var(--navy)">{{ $val }}</div>
                    <div style="font-size:10px;color:var(--text-muted)">{{ $label }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="card-modern">
            <div class="card-modern-body">
                <div class="fw-bold mb-3" style="font-size:12.5px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted)">Actions</div>

                @if($employer->verification_status === 'pending')
                    {{-- VERIFY --}}
                    <form action="{{ route('admin.employers.verify', $employer) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-navy w-100" onclick="return confirm('Verify {{ addslashes($employer->company_name) }}?')">
                            <i class="bi bi-patch-check me-2"></i>Verify Employer
                        </button>
                    </form>
                    {{-- REJECT --}}
                    <button class="btn btn-outline-navy w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                        <i class="bi bi-x-circle me-2"></i>Reject Application
                    </button>
                    <div class="collapse mb-2" id="rejectForm">
                        <form action="{{ route('admin.employers.reject', $employer) }}" method="POST">
                            @csrf
                            <textarea name="rejection_reason" class="form-control mb-2" rows="3" placeholder="Reason for rejection (required)..." required minlength="10"></textarea>
                            <button type="submit" class="btn w-100" style="background:#DC2626;color:#fff">Confirm Rejection</button>
                        </form>
                    </div>
                @elseif($employer->verification_status === 'verified')
                    <button class="btn w-100 mb-2" style="background:#F59E0B;color:#fff;border:none" data-bs-toggle="collapse" data-bs-target="#suspendForm">
                        <i class="bi bi-slash-circle me-2"></i>Suspend Employer
                    </button>
                    <div class="collapse mb-2" id="suspendForm">
                        <form action="{{ route('admin.employers.suspend', $employer) }}" method="POST">
                            @csrf
                            <textarea name="suspension_reason" class="form-control mb-2" rows="3" placeholder="Reason for suspension (optional)..."></textarea>
                            <button type="submit" class="btn w-100" style="background:#DC2626;color:#fff">Confirm Suspension</button>
                        </form>
                    </div>
                @elseif(in_array($employer->verification_status, ['rejected','suspended']))
                    <form action="{{ route('admin.employers.reinstate', $employer) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-navy w-100" onclick="return confirm('Reinstate this employer?')">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reinstate Employer
                        </button>
                    </form>
                @endif

                @if($employer->rejection_reason)
                <div class="mt-2 p-3 rounded-2" style="background:#FEF2F2;border:1px solid #FECACA;font-size:12.5px;color:#991B1B">
                    <strong>Rejection/Suspension note:</strong><br>{{ $employer->rejection_reason }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: DETAILS --}}
    <div class="col-lg-8">

        {{-- Company Info --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-info-circle me-2"></i>Company Information</h6></div>
            <div class="card-modern-body">
                <div class="row g-3">
                    @php
                    $fields = [
                        'Registration Number' => $employer->registration_number,
                        'TIN Number'          => $employer->tin_number ?? '—',
                        'Company Type'        => ucfirst(str_replace('_',' ',$employer->company_type)),
                        'Industry'            => $employer->industry,
                        'District'            => $employer->district,
                        'City'                => $employer->city ?? '—',
                        'Address'             => $employer->address,
                        'Website'             => $employer->website ?? '—',
                    ];
                    @endphp
                    @foreach($fields as $label => $value)
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">{{ $label }}</div>
                        <div style="font-size:13.5px;color:var(--text-main);margin-top:2px">
                            @if($label === 'Website' && $value !== '—')
                                <a href="{{ $value }}" target="_blank" style="color:var(--navy)">{{ $value }}</a>
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Contact Person --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-person-lines-fill me-2"></i>Contact Person</h6></div>
            <div class="card-modern-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Name</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $employer->contact_person }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Email</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $employer->contact_email }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Phone</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $employer->contact_phone }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account User --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-person-badge me-2"></i>System Account</h6></div>
            <div class="card-modern-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--amber);font-size:16px">
                        {{ strtoupper(substr($employer->user->name,0,1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $employer->user->name }}</div>
                        <div style="font-size:12.5px;color:var(--text-muted)">{{ $employer->user->email }} &bull; {{ $employer->user->phone }}</div>
                    </div>
                    <div class="ms-auto">
                        <span class="badge-nfer {{ $employer->user->is_active ? 'badge-active' : 'badge-suspended' }}">
                            {{ $employer->user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verification History --}}
        @if($employer->verified_at || $employer->rejection_reason)
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-clock-history me-2"></i>Verification History</h6></div>
            <div class="card-modern-body">
                @if($employer->verified_at)
                <div class="small text-success"><i class="bi bi-check-circle-fill me-2"></i>Verified on {{ $employer->verified_at->format('d M Y H:i') }}
                    @if($employer->verifiedBy) by {{ $employer->verifiedBy->name }}@endif
                </div>
                @endif
                @if($employer->rejection_reason)
                <div class="small text-danger mt-2"><i class="bi bi-x-circle-fill me-2"></i>{{ $employer->rejection_reason }}</div>
                @endif
            </div>
        </div>
        @endif

        {{-- Recent Employment Records --}}
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-file-earmark-person me-2"></i>Recent Employment Records</h6></div>
            <div class="card-modern-body p-0">
                <table class="table-modern">
                    <thead>
                        <tr><th>Employee</th><th>Job Title</th><th>Start Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($employer->employmentRecords->take(5) as $rec)
                        <tr>
                            <td style="font-size:13px">{{ $rec->employee->full_name ?? '—' }}</td>
                            <td style="font-size:13px">{{ $rec->job_title }}</td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $rec->start_date->format('d M Y') }}</td>
                            <td><span class="badge-nfer badge-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3" style="color:var(--text-muted)">No records yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
