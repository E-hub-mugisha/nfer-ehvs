@extends('layouts.app')
@section('title', $record->job_title . ' — ' . $record->employee->full_name)
@section('page-title', 'Employment Record')

@section('content')

<div class="mb-3 d-flex align-items-center justify-content-between">
    <a href="{{ route('employer.records.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Records
    </a>
    <div class="d-flex gap-2">
        @if($record->status === 'active')
        <a href="{{ route('employer.records.edit', $record) }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <button class="btn btn-amber btn-sm" data-bs-toggle="modal" data-bs-target="#closeModal">
            <i class="bi bi-door-open me-1"></i>Close Record
        </button>
        @elseif(!$record->feedback)
        <a href="{{ route('employer.feedback.create', $record) }}" class="btn btn-amber btn-sm">
            <i class="bi bi-chat-right-text me-1"></i>Add Feedback
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- LEFT --}}
    <div class="col-lg-4">

        {{-- Employee Card --}}
        <div class="card-modern mb-3">
            <div class="card-modern-body text-center" style="padding:28px 24px">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:24px;font-weight:800;color:var(--amber);margin:0 auto 12px">
                    {{ strtoupper(substr($record->employee->first_name,0,1)) }}
                </div>
                <h6 class="mb-0" style="font-family:var(--font-head);color:var(--navy)">{{ $record->employee->full_name }}</h6>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px">{{ $record->employee->national_id }}</div>
                @if($record->employee->is_verified)
                <div class="mt-2"><span class="verified-shield" style="font-size:10.5px"><i class="bi bi-patch-check-fill me-1"></i>Verified</span></div>
                @endif
            </div>
        </div>

        {{-- Record Status Card --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-info-circle me-2"></i>Record Info</h6></div>
            <div class="card-modern-body">
                @php
                $info = [
                    'Status'      => '<span class="badge-nfer badge-'.$record->status.'">'.ucfirst($record->status).'</span>',
                    'Duration'    => $record->duration,
                    'Type'        => ucfirst(str_replace('_',' ',$record->employment_type)),
                    'Confirmed'   => $record->employee_confirmation === 'confirmed' ? '<span class="badge-nfer badge-active">Yes</span>' : ($record->employee_confirmation === 'disputed' ? '<span class="badge-nfer badge-rejected">Disputed</span>' : '<span class="badge-nfer badge-pending">Pending</span>'),
                    'Feedback'    => $record->feedback ? '<span class="badge-nfer badge-active">Submitted</span>' : '<span class="badge-nfer badge-pending">Not yet</span>',
                ];
                @endphp
                @foreach($info as $label => $value)
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span>{!! $value !!}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Disputes --}}
        @if($record->disputes->count())
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-shield-exclamation me-2 text-danger"></i>Disputes ({{ $record->disputes->count() }})</h6></div>
            <div class="card-modern-body p-0">
                @foreach($record->disputes as $d)
                <div class="px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div class="d-flex justify-content-between">
                        <span style="font-size:12.5px;font-weight:600">{{ ucfirst(str_replace('_',' ',$d->dispute_type)) }}</span>
                        <span class="badge-nfer badge-{{ in_array($d->status,['open','under_review']) ? 'rejected' : 'active' }}" style="font-size:10.5px">{{ ucfirst(str_replace('_',' ',$d->status)) }}</span>
                    </div>
                    <div style="font-size:11.5px;color:var(--text-muted)">{{ $d->created_at->format('d M Y') }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT --}}
    <div class="col-lg-8">

        {{-- Main Record Details --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header">
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-head);color:var(--navy)">{{ $record->job_title }}</h5>
                    @if($record->department)<div style="font-size:13px;color:var(--text-muted)">{{ $record->department }}</div>@endif
                </div>
            </div>
            <div class="card-modern-body">
                <div class="row g-3">
                    @php
                    $fields = [
                        'Start Date'  => $record->start_date->format('d M Y'),
                        'End Date'    => $record->end_date ? $record->end_date->format('d M Y') : 'Present',
                        'Duration'    => $record->duration,
                        'Type'        => ucfirst(str_replace('_',' ',$record->employment_type)),
                    ];
                    @endphp
                    @foreach($fields as $label => $val)
                    <div class="col-md-3">
                        <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">{{ $label }}</div>
                        <div class="fw-semibold mt-1" style="font-size:13.5px">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>

                @if($record->exit_reason)
                <div class="mt-4 p-3 rounded-2" style="background:var(--surface);border:1px solid var(--border)">
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:6px">Exit Information</div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge-nfer badge-pending">{{ ucfirst(str_replace('_',' ',$record->exit_reason)) }}</span>
                    </div>
                    @if($record->exit_details)
                    <p style="font-size:13.5px;margin-top:8px;margin-bottom:0;color:var(--text-main)">{{ $record->exit_details }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Feedback --}}
        @if($record->feedback)
        <div class="card-modern mb-3">
            <div class="card-modern-header">
                <h6><i class="bi bi-chat-quote me-2"></i>Employer Feedback</h6>
                <a href="{{ route('employer.feedback.edit', $record->feedback) }}" class="btn btn-outline-navy btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </div>
            <div class="card-modern-body">
                @php $fb = $record->feedback; @endphp

                {{-- Ratings Grid --}}
                <div class="row g-3 mb-4">
                    @foreach(['punctuality','teamwork','communication','integrity','performance'] as $attr)
                    @php $val = $fb->{"rating_{$attr}"} @endphp
                    @if($val)
                    <div class="col-md-4">
                        <div class="p-3 rounded-2" style="background:var(--surface);border:1px solid var(--border)">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:6px">{{ ucfirst($attr) }}</div>
                            <div class="stars" style="font-size:13px">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$val?'':'empty' }}"></i>@endfor
                                <span style="font-size:12px;color:var(--text-muted);margin-left:4px">{{ $val }}/5</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    <div class="col-md-4">
                        <div class="p-3 rounded-2" style="background:var(--navy);border:1px solid var(--navy)">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:rgba(255,255,255,.5);margin-bottom:4px">Overall</div>
                            <div style="font-family:var(--font-head);font-size:22px;font-weight:800;color:var(--amber)">{{ $fb->overall_rating }}/5</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    @if($fb->general_remarks)
                    <div class="col-12">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:4px">General Remarks</div>
                        <p style="font-size:13.5px">{{ $fb->general_remarks }}</p>
                    </div>
                    @endif
                    @if($fb->strengths)
                    <div class="col-md-6">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:4px">Strengths</div>
                        <p style="font-size:13px">{{ $fb->strengths }}</p>
                    </div>
                    @endif
                    @if($fb->areas_of_improvement)
                    <div class="col-md-6">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);margin-bottom:4px">Areas of Improvement</div>
                        <p style="font-size:13px">{{ $fb->areas_of_improvement }}</p>
                    </div>
                    @endif
                    <div class="col-12 d-flex align-items-center gap-3">
                        <x-conduct-badge :flag="$fb->conduct_flag" />
                        @if(!is_null($fb->would_rehire))
                        <span class="{{ $fb->would_rehire ? 'text-success' : 'text-danger' }}" style="font-size:13px;font-weight:600">
                            <i class="bi bi-{{ $fb->would_rehire ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                            {{ $fb->would_rehire ? 'Would Rehire' : 'Would Not Rehire' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @elseif($record->status === 'closed')
        <div class="p-4 rounded-3 d-flex align-items-center gap-4" style="background:#FEF9C3;border:1px solid #FDE68A">
            <i class="bi bi-chat-right-text fs-3" style="color:#D97706"></i>
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#92400E">Feedback Not Yet Submitted</div>
                <div style="font-size:13px;color:#92400E">Provide feedback on this employee's professional conduct and performance.</div>
            </div>
            <a href="{{ route('employer.feedback.create', $record) }}" class="btn btn-amber btn-sm" style="white-space:nowrap">Add Feedback</a>
        </div>
        @endif

    </div>
</div>

{{-- CLOSE RECORD MODAL --}}
@if($record->status === 'active')
<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;border:none">
            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                <h5 class="modal-title" style="font-family:var(--font-head);color:var(--navy)">Close Employment Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employer.records.close', $record) }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:24px">
                    <div class="mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exit Reason <span class="text-danger">*</span></label>
                        <select name="exit_reason" class="form-select" required>
                            <option value="">Select reason…</option>
                            @foreach(['resigned','terminated','contract_ended','redundancy','retirement','mutual_agreement','misconduct','absconded','deceased','other'] as $r)
                            <option value="{{ $r }}">{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exit Details <small class="text-muted">(optional)</small></label>
                        <textarea name="exit_details" class="form-control" rows="3" placeholder="Additional notes…"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;gap:10px">
                    <button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-amber"><i class="bi bi-door-open me-2"></i>Close Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
