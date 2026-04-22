@extends('layouts.app')
@section('title', 'Submit Feedback')
@section('page-title', 'Submit Employee Feedback')

@section('content')

<div class="mb-3">
    <a href="{{ route('employer.records.show', $record) }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Record
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">

        {{-- EMPLOYEE SUMMARY --}}
        <div class="card-modern mb-4" style="border-left:4px solid var(--amber)">
            <div class="card-modern-body">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div style="width:56px;height:56px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:22px;font-weight:800;color:var(--amber)">
                        {{ strtoupper(substr($record->employee->first_name,0,1)) }}
                    </div>
                    <div>
                        <h6 class="mb-0" style="font-family:var(--font-head);color:var(--navy)">{{ $record->employee->full_name }}</h6>
                        <div style="font-size:12.5px;color:var(--text-muted)">{{ $record->job_title }}
                            &bull; {{ $record->start_date->format('M Y') }} — {{ $record->end_date->format('M Y') }}
                            &bull; {{ $record->duration }}
                        </div>
                    </div>
                    <div class="ms-auto text-end">
                        <div style="font-size:11px;color:var(--text-muted)">Exit Reason</div>
                        <span class="badge-nfer badge-pending">{{ ucfirst(str_replace('_',' ',$record->exit_reason)) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-chat-right-text me-2"></i>Professional Conduct Feedback</h6>
            </div>
            <div class="card-modern-body">
                <form action="{{ route('employer.feedback.store', $record) }}" method="POST">
                    @csrf

                    {{-- STAR RATINGS --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Performance Ratings <small class="fw-normal" style="text-transform:none;letter-spacing:0">(1 = Poor, 5 = Excellent)</small>
                        </div>
                        <div class="row g-3">
                            @foreach(['punctuality'=>'Punctuality & Attendance','teamwork'=>'Teamwork & Collaboration','communication'=>'Communication','integrity'=>'Integrity & Ethics','performance'=>'Work Performance'] as $key => $label)
                            <div class="col-md-4">
                                <label class="form-label">{{ $label }}</label>
                                <div class="d-flex gap-2">
                                    @for($i=1;$i<=5;$i++)
                                    <label class="star-label" style="cursor:pointer">
                                        <input type="radio" name="rating_{{ $key }}" value="{{ $i }}" style="display:none"
                                            {{ old("rating_{$key}") == $i ? 'checked' : '' }}>
                                        <i class="bi bi-star-fill star-icon" data-val="{{ $i }}" data-group="{{ $key }}"
                                           style="font-size:22px;color:var(--border);cursor:pointer;transition:color .1s"></i>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- CONDUCT FLAG --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Conduct Classification <span class="text-danger">*</span>
                        </div>
                        <div class="row g-3">
                            @foreach([
                                'clean'       => ['✓ Clean Record',   '#DCFCE7','#16A34A','No issues. Meets professional standards.'],
                                'minor_issue' => ['⚠ Minor Issue',    '#FEF9C3','#D97706','Small infractions that were addressed.'],
                                'major_issue' => ['✗ Major Issue',    '#FEE2E2','#DC2626','Serious conduct or performance problems.'],
                                'blacklisted' => ['⛔ Blacklisted',   '#F1F5F9','#7C3AED','Dismissal for gross misconduct.'],
                            ] as $val => [$label, $bg, $color, $desc])
                            <div class="col-md-3">
                                <label class="d-block" style="cursor:pointer">
                                    <input type="radio" name="conduct_flag" value="{{ $val }}" style="display:none"
                                        {{ old('conduct_flag') === $val ? 'checked' : '' }}
                                        class="conduct-radio" id="cf_{{ $val }}">
                                    <div class="conduct-card p-3 rounded-3 text-center h-100" data-val="{{ $val }}"
                                         style="border:2px solid {{ $bg === '#F1F5F9' ? '#E2E8F0' : $bg }};background:{{ $bg }};transition:all .15s">
                                        <div class="fw-bold mb-1" style="font-size:13px;color:{{ $color }}">{{ $label }}</div>
                                        <div style="font-size:11px;color:{{ $color }};opacity:.75">{{ $desc }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('conduct_flag')<div class="text-danger mt-2" style="font-size:13px">{{ $message }}</div>@enderror
                    </div>

                    {{-- REMARKS --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Written Remarks
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">General Remarks</label>
                                <textarea name="general_remarks" class="form-control" rows="4"
                                    placeholder="Overall assessment of the employee's time at your organisation…">{{ old('general_remarks') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Key Strengths</label>
                                <textarea name="strengths" class="form-control" rows="3" placeholder="What the employee did well…">{{ old('strengths') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Areas for Improvement</label>
                                <textarea name="areas_of_improvement" class="form-control" rows="3" placeholder="Areas the employee could improve…">{{ old('areas_of_improvement') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- REHIRE --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Rehire Recommendation
                        </div>
                        <div class="d-flex gap-3">
                            <label style="cursor:pointer">
                                <input type="radio" name="would_rehire" value="1" {{ old('would_rehire')==='1'?'checked':'' }}>
                                <span class="ms-1" style="font-size:13.5px">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>Yes, would rehire
                                </span>
                            </label>
                            <label style="cursor:pointer">
                                <input type="radio" name="would_rehire" value="0" {{ old('would_rehire')==='0'?'checked':'' }}>
                                <span class="ms-1" style="font-size:13.5px">
                                    <i class="bi bi-x-circle-fill text-danger me-1"></i>No, would not rehire
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('employer.records.show', $record) }}" class="btn btn-outline-navy">Cancel</a>
                        <button type="submit" class="btn btn-navy"><i class="bi bi-check-circle me-2"></i>Submit Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.star-icon:hover, .star-icon.active { color: var(--amber) !important; }
.conduct-card.selected { box-shadow: 0 0 0 2px var(--navy); }
</style>
@endpush

@push('scripts')
<script>
// Star ratings
document.querySelectorAll('.star-icon').forEach(star => {
    star.addEventListener('click', function () {
        const group = this.dataset.group;
        const val = parseInt(this.dataset.val);
        document.querySelectorAll(`[data-group="${group}"]`).forEach((s, i) => {
            s.style.color = (i < val) ? 'var(--amber)' : 'var(--border)';
        });
        document.querySelector(`input[name="rating_${group}"][value="${val}"]`).checked = true;
    });
    star.addEventListener('mouseover', function () {
        const group = this.dataset.group;
        const val = parseInt(this.dataset.val);
        document.querySelectorAll(`[data-group="${group}"]`).forEach((s, i) => {
            s.style.color = (i < val) ? 'var(--amber)' : 'var(--border)';
        });
    });
});

// Conduct cards
document.querySelectorAll('.conduct-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.conduct-card').forEach(c => c.classList.remove('selected'));
        document.querySelector(`.conduct-card[data-val="${this.value}"]`).classList.add('selected');
    });
});

// Pre-fill stars from old() values
@foreach(['punctuality','teamwork','communication','integrity','performance'] as $key)
@if(old("rating_{$key}"))
document.querySelectorAll(`[data-group="{{ $key }}"]`).forEach((s, i) => {
    s.style.color = (i < {{ old("rating_{$key}") }}) ? 'var(--amber)' : 'var(--border)';
});
@endif
@endforeach
</script>
@endpush

@endsection
