@extends('layouts.app')
@section('title', 'Edit Employment Record')
@section('page-title', 'Edit Employment Record')

@section('content')

<div class="mb-3">
    <a href="{{ route('employer.records.show', $record) }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Record
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- EMPLOYEE HEADER --}}
        <div class="card-modern mb-4">
            <div class="card-modern-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;color:var(--amber)">
                        {{ strtoupper(substr($record->employee->first_name,0,1)) }}
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:14.5px;font-family:var(--font-head)">{{ $record->employee->full_name }}</div>
                        <div style="font-size:12.5px;color:var(--text-muted)">NID: {{ $record->employee->national_id }}</div>
                    </div>
                    <div class="ms-auto"><span class="badge-nfer badge-{{ $record->status }}">{{ ucfirst($record->status) }}</span></div>
                </div>
            </div>
        </div>

        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-pencil me-2"></i>Edit Record Details</h6>
            </div>
            <div class="card-modern-body">
                <form action="{{ route('employer.records.update', $record) }}" method="POST">
                    @csrf @method('PUT')

                    {{-- POSITION --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Position Details
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Job Title <span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror"
                                    value="{{ old('job_title', $record->job_title) }}" required>
                                @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control" value="{{ old('department', $record->department) }}">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type" class="form-select" required>
                                    @foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','internship'=>'Internship'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('employment_type',$record->employment_type)===$v?'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
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
                                    value="{{ old('start_date', $record->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="endDate" class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', $record->end_date?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- EXIT --}}
                    <div id="exitSection" class="mb-4" style="{{ (old('end_date', $record->end_date)) ? '' : 'display:none' }}">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Exit Information
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Exit Reason</label>
                                <select name="exit_reason" id="exitReason" class="form-select @error('exit_reason') is-invalid @enderror">
                                    <option value="">Select reason…</option>
                                    @foreach(['resigned','terminated','contract_ended','redundancy','retirement','mutual_agreement','misconduct','absconded','deceased','other'] as $r)
                                    <option value="{{ $r }}" {{ old('exit_reason',$record->exit_reason)===$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                                    @endforeach
                                </select>
                                @error('exit_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Exit Details</label>
                                <textarea name="exit_details" class="form-control" rows="3">{{ old('exit_details', $record->exit_details) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SALARY --}}
                    <div class="mb-4">
                        <div class="fw-bold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);padding-bottom:8px;border-bottom:1px solid var(--border)">
                            Salary (Internal Only)
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Starting Salary (RWF)</label>
                                <input type="number" name="starting_salary" class="form-control" min="0" value="{{ old('starting_salary', $record->starting_salary) }}">
                            </div>
                            <div class="col-md-4" id="endingSalaryField" style="{{ old('end_date', $record->end_date) ? '' : 'display:none' }}">
                                <label class="form-label">Ending Salary (RWF)</label>
                                <input type="number" name="ending_salary" class="form-control" min="0" value="{{ old('ending_salary', $record->ending_salary) }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('employer.records.show', $record) }}" class="btn btn-outline-navy">Cancel</a>
                        <button type="submit" class="btn btn-navy"><i class="bi bi-check-circle me-2"></i>Save Changes</button>
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
endDate.addEventListener('change', function () {
    const has = this.value !== '';
    exitSection.style.display = has ? '' : 'none';
    endingSalaryField.style.display = has ? '' : 'none';
});
</script>
@endpush

@endsection
