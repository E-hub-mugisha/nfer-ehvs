@extends('layouts.app')
@section('title', 'Add Employment Record')
@section('page-title', 'Add Employment Record')

@section('content')

<div class="mb-3">
    <a href="{{ route('employer.records.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Records
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- INFO BOX --}}
        <div class="p-4 rounded-3 mb-4 d-flex gap-3" style="background:#EFF6FF;border:1px solid #BFDBFE">
            <i class="bi bi-info-circle-fill fs-4 flex-shrink-0" style="color:#1D4ED8;margin-top:2px"></i>
            <div style="font-size:13.5px;color:#1E3A8A">
                <strong>Important:</strong> Only add records for formal employment at your organisation.
                The employee's National ID must be registered in the system. They will be notified and asked to confirm the record.
            </div>
        </div>

        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-plus-circle me-2"></i>New Employment Record</h6>
            </div>
            <div class="card-modern-body">
                <form action="{{ route('employer.records.store') }}" method="POST">
                    @csrf

                    {{-- EMPLOYEE LOOKUP --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Employee Identification
                        </div>
                        <div class="mb-3">
                            <label class="form-label">National ID <span class="text-danger">*</span></label>
                            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                placeholder="Enter employee's National ID number" value="{{ old('national_id') }}" required>
                            <div class="form-text">The employee must be registered in NFER-EHVS for their National ID to be found.</div>
                            @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- JOB DETAILS --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Position Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title <span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror"
                                    placeholder="e.g. Senior Software Engineer" value="{{ old('job_title') }}" required>
                                @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control" placeholder="e.g. Engineering, Finance…" value="{{ old('department') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                                    <option value="">Select type…</option>
                                    @foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','internship'=>'Internship'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('employment_type')===$v?'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- DATES --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Employment Period
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" max="{{ date('Y-m-d') }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date <small class="text-muted">(leave blank if current)</small></label>
                                <input type="date" name="end_date" id="endDate" class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" max="{{ date('Y-m-d') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- EXIT (shown when end_date is filled) --}}
                    <div id="exitSection" class="mb-4" style="{{ old('end_date') ? '' : 'display:none' }}">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Exit Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Exit Reason <span class="text-danger">*</span></label>
                                <select name="exit_reason" id="exitReason" class="form-select @error('exit_reason') is-invalid @enderror">
                                    <option value="">Select reason…</option>
                                    @foreach(['resigned','terminated','contract_ended','redundancy','retirement','mutual_agreement','misconduct','absconded','deceased','other'] as $r)
                                    <option value="{{ $r }}" {{ old('exit_reason')===$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                                    @endforeach
                                </select>
                                @error('exit_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Exit Details <small class="text-muted">(optional but recommended)</small></label>
                                <textarea name="exit_details" class="form-control" rows="3"
                                    placeholder="Additional context about the departure…">{{ old('exit_details') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SALARY (optional) --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Salary Information <small class="fw-normal" style="text-transform:none;letter-spacing:0">(optional — not shown publicly)</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Starting Salary (RWF)</label>
                                <input type="number" name="starting_salary" class="form-control" placeholder="0" min="0" value="{{ old('starting_salary') }}">
                            </div>
                            <div class="col-md-4" id="endingSalaryField" style="{{ old('end_date') ? '' : 'display:none' }}">
                                <label class="form-label">Ending Salary (RWF)</label>
                                <input type="number" name="ending_salary" class="form-control" placeholder="0" min="0" value="{{ old('ending_salary') }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('employer.records.index') }}" class="btn btn-outline-navy">Cancel</a>
                        <button type="submit" class="btn btn-navy"><i class="bi bi-check-circle me-2"></i>Create Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const endDate = document.getElementById('endDate');
const exitSection = document.getElementById('exitSection');
const endingSalaryField = document.getElementById('endingSalaryField');
const exitReason = document.getElementById('exitReason');

endDate.addEventListener('change', function () {
    const hasEnd = this.value !== '';
    exitSection.style.display = hasEnd ? '' : 'none';
    endingSalaryField.style.display = hasEnd ? '' : 'none';
    exitReason.required = hasEnd;
});
</script>
@endpush

@endsection
