@extends('layouts.app')
@section('title', 'Dispute #' . $dispute->id)
@section('page-title', 'Dispute Review')

@section('content')

<div class="mb-3">
    <a href="{{ route('admin.disputes.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Disputes
    </a>
</div>

<div class="row g-4">
    {{-- LEFT: DISPUTE DETAILS --}}
    <div class="col-lg-7">

        {{-- Header Card --}}
        <div class="card-modern mb-3">
            <div class="card-modern-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px">Dispute #{{ $dispute->id }}</div>
                        <h5 class="mt-1 mb-2" style="font-family:var(--font-head);color:var(--navy)">
                            {{ ucfirst(str_replace('_',' ',$dispute->dispute_type)) }}
                        </h5>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @php $dMap = ['open'=>'badge-rejected','under_review'=>'badge-pending','resolved'=>'badge-active','dismissed'=>'badge-suspended']; @endphp
                            <span class="badge-nfer {{ $dMap[$dispute->status] ?? 'badge-pending' }}">
                                {{ ucfirst(str_replace('_',' ',$dispute->status)) }}
                            </span>
                            <span style="font-size:12px;color:var(--text-muted)">Submitted {{ $dispute->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-chat-text me-2"></i>Dispute Description</h6></div>
            <div class="card-modern-body">
                <p style="font-size:14px;line-height:1.7;color:var(--text-main)">{{ $dispute->description }}</p>
                @if($dispute->supporting_document)
                <div class="mt-3">
                    <a href="{{ asset($dispute->supporting_document) }}" target="_blank" class="btn btn-outline-navy btn-sm">
                        <i class="bi bi-paperclip me-2"></i>View Supporting Document
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Related Employment Record --}}
        @if($dispute->employmentRecord)
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-file-earmark-person me-2"></i>Disputed Employment Record</h6></div>
            <div class="card-modern-body">
                @php $rec = $dispute->employmentRecord; @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Employee</div>
                        <div class="fw-semibold mt-1">{{ $rec->employee->full_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Employer</div>
                        <div class="fw-semibold mt-1">{{ $rec->employer->company_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Job Title</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $rec->job_title }}</div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Period</div>
                        <div style="font-size:13.5px;margin-top:2px">
                            {{ $rec->start_date->format('M Y') }} — {{ $rec->end_date ? $rec->end_date->format('M Y') : 'Present' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Exit Reason</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $rec->exit_reason ? ucfirst(str_replace('_',' ',$rec->exit_reason)) : '—' }}</div>
                    </div>
                    @if($rec->exit_details)
                    <div class="col-12">
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted)">Exit Details</div>
                        <div style="font-size:13px;margin-top:4px;color:var(--text-main)">{{ $rec->exit_details }}</div>
                    </div>
                    @endif
                </div>

                @if($rec->feedback)
                <div class="mt-3 p-3 rounded-2" style="background:var(--surface);border:1px solid var(--border)">
                    <div class="fw-semibold mb-2" style="font-size:12.5px">Employer Feedback on Record</div>
                    <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size:12.5px">
                        <span>Rating: <span class="stars" style="font-size:12px">
                            @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$rec->feedback->overall_rating?'':'empty' }}"></i>@endfor
                        </span></span>
                        <span class="badge-nfer badge-{{ $rec->feedback->conduct_flag==='clean'?'active':'rejected' }}">{{ ucfirst(str_replace('_',' ',$rec->feedback->conduct_flag)) }}</span>
                        @if($rec->feedback->general_remarks)
                        <span style="font-style:italic;color:var(--text-muted)">"{{ Str::limit($rec->feedback->general_remarks, 80) }}"</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Resolution (if closed) --}}
        @if($dispute->admin_resolution)
        <div class="card-modern" style="border-left:3px solid var(--amber)">
            <div class="card-modern-header"><h6><i class="bi bi-check-circle me-2 text-success"></i>Admin Resolution</h6></div>
            <div class="card-modern-body">
                <p style="font-size:14px;line-height:1.7">{{ $dispute->admin_resolution }}</p>
                @if($dispute->resolvedBy)
                <div class="small text-muted mt-2">
                    Resolved by <strong>{{ $dispute->resolvedBy->name }}</strong>
                    on {{ $dispute->resolved_at->format('d M Y H:i') }}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: ACTIONS & CLAIMANT --}}
    <div class="col-lg-5">

        {{-- Claimant --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-person me-2"></i>Submitted By</h6></div>
            <div class="card-modern-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--amber);font-size:16px">
                        {{ strtoupper(substr($dispute->raisedBy->name,0,1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $dispute->raisedBy->name }}</div>
                        <div style="font-size:12.5px;color:var(--text-muted)">{{ $dispute->raisedBy->email }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ ucfirst($dispute->raisedBy->account_type) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Actions --}}
        @if(in_array($dispute->status, ['open','under_review']))
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-shield-fill me-2 text-warning"></i>Admin Actions</h6></div>
            <div class="card-modern-body">

                @if($dispute->status === 'open')
                <form action="{{ route('admin.disputes.mark-review', $dispute) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-navy w-100">
                        <i class="bi bi-eye me-2"></i>Mark as Under Review
                    </button>
                </form>
                @endif

                {{-- RESOLVE --}}
                <div class="mb-2 fw-semibold" style="font-size:12.5px">Resolve Dispute</div>
                <form action="{{ route('admin.disputes.resolve', $dispute) }}" method="POST" class="mb-3">
                    @csrf
                    <textarea name="admin_resolution" class="form-control mb-2" rows="4"
                        placeholder="Describe the resolution and any actions taken (min 20 chars)…" required minlength="20"></textarea>
                    <button type="submit" class="btn btn-navy w-100" onclick="return confirm('Resolve this dispute?')">
                        <i class="bi bi-check-circle me-2"></i>Resolve Dispute
                    </button>
                </form>

                <hr style="border-color:var(--border)">

                {{-- DISMISS --}}
                <div class="mb-2 fw-semibold" style="font-size:12.5px">Dismiss Dispute</div>
                <form action="{{ route('admin.disputes.dismiss', $dispute) }}" method="POST">
                    @csrf
                    <textarea name="admin_resolution" class="form-control mb-2" rows="3"
                        placeholder="Reason for dismissal…" required minlength="10"></textarea>
                    <button type="submit" class="btn w-100" style="background:#DC2626;color:#fff;border:none" onclick="return confirm('Dismiss this dispute?')">
                        <i class="bi bi-x-circle me-2"></i>Dismiss Dispute
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-clock-history me-2"></i>Timeline</h6></div>
            <div class="card-modern-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div style="font-size:13px"><span class="fw-semibold">Dispute submitted</span></div>
                        <div style="font-size:11.5px;color:var(--text-muted)">{{ $dispute->created_at->format('d M Y H:i') }}</div>
                    </div>
                    @if($dispute->status === 'under_review')
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div style="font-size:13px"><span class="fw-semibold">Marked under review</span></div>
                    </div>
                    @endif
                    @if($dispute->resolved_at)
                    <div class="timeline-item">
                        <div class="timeline-dot {{ in_array($dispute->status,['dismissed']) ? 'closed' : '' }}"></div>
                        <div style="font-size:13px"><span class="fw-semibold">{{ ucfirst($dispute->status) }}</span></div>
                        <div style="font-size:11.5px;color:var(--text-muted)">{{ $dispute->resolved_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
