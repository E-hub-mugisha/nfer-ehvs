@extends('layouts.app')
@section('title', $employee->full_name)
@section('page-title', 'Employee Profile')

@section('content')

<div class="mb-3">
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Employees
    </a>
</div>

<div class="row g-4">
    {{-- LEFT SIDEBAR --}}
    <div class="col-lg-4">
        <div class="card-modern mb-3">
            <div class="card-modern-body text-center" style="padding:32px 24px">
                @if($employee->profile_photo)
                    <img src="{{ asset($employee->profile_photo) }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:12px">
                @else
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:28px;font-weight:800;color:var(--amber);margin:0 auto 12px">
                        {{ strtoupper(substr($employee->first_name,0,1)) }}
                    </div>
                @endif
                <h5 class="mb-1" style="font-family:var(--font-head);color:var(--navy)">{{ $employee->full_name }}</h5>
                <div style="font-size:13px;color:var(--text-muted)">{{ $employee->current_title ?? 'No title set' }}</div>
                <div class="mt-2">
                    @if($employee->is_verified)
                        <span class="verified-shield"><i class="bi bi-patch-check-fill me-1"></i>ID Verified</span>
                    @else
                        <span class="badge-nfer badge-pending">Unverified</span>
                    @endif
                </div>
                @if($employee->average_rating > 0)
                <div class="mt-3">
                    <div style="font-family:var(--font-head);font-size:26px;font-weight:800;color:var(--navy)">{{ $employee->average_rating }}</div>
                    <div class="stars">
                        @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$employee->average_rating?'':'empty' }}"></i>@endfor
                    </div>
                    <div style="font-size:11px;color:var(--text-muted)">Overall Rating</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Admin Action --}}
        @if(!$employee->is_verified)
        <div class="card-modern mb-3">
            <div class="card-modern-body">
                <form action="{{ route('admin.employees.verify', $employee) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-navy w-100" onclick="return confirm('Verify this employee?')">
                        <i class="bi bi-patch-check me-2"></i>Verify Employee
                    </button>
                </form>
                <div class="small text-muted mt-2 text-center">Verifies the National ID and activates public profile visibility</div>
            </div>
        </div>
        @endif

        {{-- Personal Info --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-person me-2"></i>Personal Details</h6></div>
            <div class="card-modern-body">
                @php
                $details = [
                    'National ID'  => $employee->national_id,
                    'Date of Birth'=> $employee->date_of_birth->format('d M Y'),
                    'Gender'       => ucfirst($employee->gender),
                    'Nationality'  => $employee->nationality,
                    'District'     => $employee->district ?? '—',
                    'Sector'       => $employee->sector ?? '—',
                ];
                @endphp
                @foreach($details as $label => $value)
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span class="fw-semibold">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Skills --}}
        @if($employee->skills->count())
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-tags me-2"></i>Skills</h6></div>
            <div class="card-modern-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($employee->skills as $skill)
                    <span class="badge-nfer" style="background:var(--amber-light);color:var(--navy)">
                        {{ $skill->name }}
                        <span style="opacity:.6;font-size:10px">({{ $skill->pivot->proficiency }})</span>
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: RECORDS & FEEDBACK --}}
    <div class="col-lg-8">

        {{-- Employment History --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header">
                <h6><i class="bi bi-briefcase me-2"></i>Employment History</h6>
                <span class="badge bg-primary rounded-pill">{{ $employee->employmentRecords->count() }}</span>
            </div>
            <div class="card-modern-body p-0">
                <table class="table-modern">
                    <thead>
                        <tr><th>Employer</th><th>Job Title</th><th>Period</th><th>Exit</th><th>Status</th><th>Confirmed</th></tr>
                    </thead>
                    <tbody>
                        @forelse($employee->employmentRecords as $rec)
                        <tr>
                            <td style="font-size:12.5px;font-weight:600">{{ $rec->employer->company_name }}</td>
                            <td style="font-size:12.5px">{{ $rec->job_title }}</td>
                            <td style="font-size:11.5px;color:var(--text-muted)">
                                {{ $rec->start_date->format('M Y') }} —
                                {{ $rec->end_date ? $rec->end_date->format('M Y') : 'Present' }}
                            </td>
                            <td style="font-size:12px">{{ $rec->exit_reason ? ucfirst(str_replace('_',' ',$rec->exit_reason)) : '—' }}</td>
                            <td><span class="badge-nfer badge-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span></td>
                            <td>
                                @if($rec->employee_confirmation === 'confirmed')
                                    <span class="badge-nfer badge-active">Confirmed</span>
                                @elseif($rec->employee_confirmation === 'disputed')
                                    <span class="badge-nfer badge-rejected">Disputed</span>
                                @else
                                    <span class="badge-nfer badge-pending">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted)">No employment records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Feedback Summary --}}
        @if($employee->feedbacks->count())
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-chat-quote me-2"></i>Employer Feedback</h6>
                <span class="badge bg-secondary rounded-pill">{{ $employee->feedbacks->count() }}</span>
            </div>
            <div class="card-modern-body p-0">
                <table class="table-modern">
                    <thead>
                        <tr><th>Employer</th><th>Rating</th><th>Conduct</th><th>Rehire</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($employee->feedbacks as $fb)
                        <tr>
                            <td style="font-size:12.5px;font-weight:600">{{ $fb->employer->company_name }}</td>
                            <td>
                                <span class="stars" style="font-size:12px">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$fb->overall_rating?'':'empty' }}"></i>@endfor
                                </span>
                                <span style="font-size:11px;color:var(--text-muted)"> {{ $fb->overall_rating }}</span>
                            </td>
                            <td><span class="badge-nfer badge-{{ $fb->conduct_flag === 'clean' ? 'active' : 'rejected' }}">{{ ucfirst(str_replace('_',' ',$fb->conduct_flag)) }}</span></td>
                            <td style="font-size:12.5px">
                                @if(!is_null($fb->would_rehire))
                                    <span class="{{ $fb->would_rehire ? 'text-success' : 'text-danger' }}">
                                        <i class="bi bi-{{ $fb->would_rehire ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td style="font-size:11.5px;color:var(--text-muted)">{{ $fb->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
