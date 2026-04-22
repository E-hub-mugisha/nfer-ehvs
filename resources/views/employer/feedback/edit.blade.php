@extends('layouts.app')
@section('title', 'Edit Feedback')
@section('page-title', 'Edit Employee Feedback')

@section('content')

<div class="mb-3">
    <a href="{{ route('employer.records.show', $feedback->employmentRecord) }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Record
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="p-3 rounded-3 mb-4 d-flex gap-3" style="background:#FEF9C3;border:1px solid #FDE68A">
            <i class="bi bi-exclamation-triangle-fill" style="color:#D97706;flex-shrink:0;font-size:18px;margin-top:2px"></i>
            <div style="font-size:13.5px;color:#92400E">
                You are editing existing feedback for <strong>{{ $feedback->employee->full_name ?? 'this employee' }}</strong>.
                Changes will be saved and visible to future employers who view this profile.
            </div>
        </div>

        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-pencil me-2"></i>Edit Feedback</h6>
            </div>
            <div class="card-modern-body">
                <form action="{{ route('employer.feedback.update', $feedback) }}" method="POST">
                    @csrf @method('PUT')

                    {{-- RATINGS --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Performance Ratings
                        </div>
                        <div class="row g-3">
                            @foreach(['punctuality'=>'Punctuality','teamwork'=>'Teamwork','communication'=>'Communication','integrity'=>'Integrity','performance'=>'Performance'] as $key => $label)
                            @php $current = old("rating_{$key}", $feedback->{"rating_{$key}"}) @endphp
                            <div class="col-md-4">
                                <label class="form-label">{{ $label }}</label>
                                <div class="d-flex gap-2">
                                    @for($i=1;$i<=5;$i++)
                                    <label>
                                        <input type="radio" name="rating_{{ $key }}" value="{{ $i }}" style="display:none"
                                            {{ $current == $i ? 'checked' : '' }}>
                                        <i class="bi bi-star-fill star-icon" data-val="{{ $i }}" data-group="{{ $key }}"
                                           style="font-size:22px;cursor:pointer;color:{{ $i <= $current ? 'var(--amber)' : 'var(--border)' }}"></i>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- CONDUCT --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Conduct Classification <span class="text-danger">*</span>
                        </div>
                        <div class="row g-3">
                            @foreach([
                                'clean'       => ['✓ Clean Record','#DCFCE7','#16A34A'],
                                'minor_issue' => ['⚠ Minor Issue','#FEF9C3','#D97706'],
                                'major_issue' => ['✗ Major Issue','#FEE2E2','#DC2626'],
                                'blacklisted' => ['⛔ Blacklisted','#F1F5F9','#7C3AED'],
                            ] as $val => [$label, $bg, $color])
                            @php $checked = old('conduct_flag', $feedback->conduct_flag) === $val @endphp
                            <div class="col-md-3">
                                <label style="cursor:pointer;display:block">
                                    <input type="radio" name="conduct_flag" value="{{ $val }}" style="display:none"
                                        {{ $checked ? 'checked' : '' }} class="conduct-radio">
                                    <div class="conduct-card p-3 rounded-3 text-center {{ $checked ? 'selected' : '' }}" data-val="{{ $val }}"
                                         style="border:2px solid {{ $bg === '#F1F5F9' ? '#E2E8F0' : $bg }};background:{{ $bg }};{{ $checked ? 'box-shadow:0 0 0 2px var(--navy)' : '' }}">
                                        <div class="fw-bold" style="font-size:13px;color:{{ $color }}">{{ $label }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- REMARKS --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Written Remarks
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">General Remarks</label>
                                <textarea name="general_remarks" class="form-control" rows="4">{{ old('general_remarks', $feedback->general_remarks) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Strengths</label>
                                <textarea name="strengths" class="form-control" rows="3">{{ old('strengths', $feedback->strengths) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Areas for Improvement</label>
                                <textarea name="areas_of_improvement" class="form-control" rows="3">{{ old('areas_of_improvement', $feedback->areas_of_improvement) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- REHIRE --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Rehire Recommendation
                        </div>
                        <div class="d-flex gap-4">
                            <label style="cursor:pointer;font-size:13.5px">
                                <input type="radio" name="would_rehire" value="1" {{ old('would_rehire', $feedback->would_rehire ? '1' : null) === '1' ? 'checked' : '' }}>
                                <i class="bi bi-check-circle-fill text-success ms-1 me-1"></i>Yes
                            </label>
                            <label style="cursor:pointer;font-size:13.5px">
                                <input type="radio" name="would_rehire" value="0" {{ old('would_rehire', !is_null($feedback->would_rehire) && !$feedback->would_rehire ? '0' : null) === '0' ? 'checked' : '' }}>
                                <i class="bi bi-x-circle-fill text-danger ms-1 me-1"></i>No
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('employer.records.show', $feedback->employmentRecord) }}" class="btn btn-outline-navy">Cancel</a>
                        <button type="submit" class="btn btn-navy"><i class="bi bi-check-circle me-2"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.star-icon').forEach(star => {
    star.addEventListener('click', function () {
        const group = this.dataset.group, val = parseInt(this.dataset.val);
        document.querySelectorAll(`[data-group="${group}"]`).forEach((s,i) => {
            s.style.color = (i < val) ? 'var(--amber)' : 'var(--border)';
        });
        document.querySelector(`input[name="rating_${group}"][value="${val}"]`).checked = true;
    });
    star.addEventListener('mouseover', function () {
        const group = this.dataset.group, val = parseInt(this.dataset.val);
        document.querySelectorAll(`[data-group="${group}"]`).forEach((s,i) => {
            s.style.color = (i < val) ? 'var(--amber)' : 'var(--border)';
        });
    });
});
document.querySelectorAll('.conduct-radio').forEach(r => {
    r.addEventListener('change', function () {
        document.querySelectorAll('.conduct-card').forEach(c => c.style.boxShadow = 'none');
        document.querySelector(`.conduct-card[data-val="${this.value}"]`).style.boxShadow = '0 0 0 2px var(--navy)';
    });
});
</script>
@endpush

@endsection
