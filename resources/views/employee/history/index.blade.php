@extends('layouts.app')
@section('title', 'My Employment History')
@section('page-title', 'My Employment History')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <p class="text-muted mb-0">Your verified formal employment history. Confirm records added by employers.</p>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-clock-history me-2"></i>Employment Timeline</h6>
        @if(auth()->user()->employee?->is_verified)
            <span class="verified-shield"><i class="bi bi-patch-check-fill"></i> ID Verified</span>
        @endif
    </div>
    <div class="card-modern-body">
        @forelse($records as $record)
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot {{ $record->status === 'closed' ? 'closed' : '' }}"></div>
                <div class="border rounded-3 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-family: var(--font-head)">{{ $record->job_title }}</h6>
                            <div class="text-muted">{{ $record->employer->company_name }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge-nfer badge-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                            @if($record->employee_confirmation === 'pending')
                                <form action="{{ route('employee.history.confirm', $record) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-amber btn-sm">
                                        <i class="bi bi-check2"></i> Confirm
                                    </button>
                                </form>
                            @elseif($record->employee_confirmation === 'confirmed')
                                <div class="small text-success mt-1"><i class="bi bi-check-circle-fill"></i> Confirmed</div>
                            @endif
                        </div>
                    </div>
                    <div class="row g-3 small text-muted">
                        <div class="col-auto">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $record->start_date->format('M Y') }} —
                            {{ $record->end_date ? $record->end_date->format('M Y') : 'Present' }}
                        </div>
                        <div class="col-auto"><i class="bi bi-clock me-1"></i>{{ $record->duration }}</div>
                        <div class="col-auto"><i class="bi bi-briefcase me-1"></i>{{ ucfirst(str_replace('_',' ',$record->employment_type)) }}</div>
                        @if($record->exit_reason)
                        <div class="col-auto">
                            <i class="bi bi-door-open me-1"></i>Exit: {{ ucfirst(str_replace('_',' ',$record->exit_reason)) }}
                        </div>
                        @endif
                    </div>

                    @if($record->feedback)
                    <div class="mt-3 p-3 rounded-2" style="background: var(--surface); border: 1px solid var(--border)">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-bold">Employer Feedback</span>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $record->feedback->overall_rating ? '' : 'empty' }}"></i>
                                @endfor
                                <span class="text-muted ms-1">{{ $record->feedback->overall_rating }}/5</span>
                            </div>
                        </div>
                        @if($record->feedback->general_remarks)
                            <p class="small text-muted mt-2 mb-0">{{ $record->feedback->general_remarks }}</p>
                        @endif
                    </div>
                    @endif

                    @if($record->employee_confirmation === 'confirmed' && $record->status === 'closed')
                    <div class="mt-2">
                        <a href="#" class="small text-danger" data-bs-toggle="modal" data-bs-target="#disputeModal{{ $record->id }}">
                            <i class="bi bi-flag me-1"></i>Raise a dispute
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4"></i>
            <p class="mt-3">No employment records yet. Records will appear here once an employer registers your employment.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection