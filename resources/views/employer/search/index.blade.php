@extends('layouts.app')
@section('title', 'Employee Verification Portal')
@section('page-title', 'Employee Verification Portal')

@section('content')

{{-- HERO SEARCH --}}
<div class="search-hero mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(232,160,32,.15);border:1px solid rgba(232,160,32,.3);border-radius:20px;padding:6px 16px;margin-bottom:16px">
                <i class="bi bi-patch-check-fill" style="color:var(--amber)"></i>
                <span style="font-size:12.5px;color:var(--amber);font-weight:600">NFER-EHVS Verified Portal</span>
            </div>
            <h3 class="mb-2" style="font-family:var(--font-head);font-weight:800;color:#fff">
                Verify Formal Employment History
            </h3>
            <p style="color:rgba(255,255,255,.6);font-size:14px;margin-bottom:28px">
                Search by National ID or full name. Only verified employee profiles with formal employment records are shown.
            </p>
            <form action="{{ route('employer.search.results') }}" method="GET">
                <div class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-lg"
                        placeholder="Enter National ID (e.g. 1199880012345678) or employee name…"
                        value="{{ request('q') }}"
                        style="background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.2);color:#fff;border-radius:10px;font-size:14px"
                        required minlength="3">
                    <button type="submit" class="btn btn-amber btn-lg" style="border-radius:10px;white-space:nowrap;font-weight:700;padding:0 24px">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>
                <div class="d-flex gap-3 justify-content-center mt-3 flex-wrap">
                    <label style="cursor:pointer;font-size:13px;color:rgba(255,255,255,.6)">
                        <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }}> All
                    </label>
                    <label style="cursor:pointer;font-size:13px;color:rgba(255,255,255,.6)">
                        <input type="radio" name="status" value="employed" {{ request('status')==='employed' ? 'checked' : '' }}> Currently Employed
                    </label>
                    <label style="cursor:pointer;font-size:13px;color:rgba(255,255,255,.6)">
                        <input type="radio" name="status" value="unemployed" {{ request('status')==='unemployed' ? 'checked' : '' }}> Available
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- HOW IT WORKS --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div style="text-align:center;margin-bottom:24px">
            <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:var(--text-muted);font-weight:600">How It Works</div>
        </div>
    </div>
    @foreach([
        ['bi-search-heart',      'Search by ID or Name',      'Enter the candidate\'s National ID number or full name to find their verified profile.'],
        ['bi-clock-history',     'View Employment Timeline',   'See verified formal employment history, job titles, durations, and reasons for leaving.'],
        ['bi-shield-fill-check', 'Read Employer Feedback',     'Access ratings and professional conduct feedback from previous employers.'],
        ['bi-check-circle-fill', 'Make Informed Decisions',    'Hire with confidence using verified, government-backed employment data.'],
    ] as $i => [$icon, $title, $desc])
    <div class="col-md-3">
        <div class="card-modern text-center" style="padding:24px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px;color:var(--amber)">
                <i class="bi bi-{{ $icon }}"></i>
            </div>
            <div class="fw-bold mb-1" style="font-size:14px;font-family:var(--font-head);color:var(--navy)">{{ $title }}</div>
            <div style="font-size:12.5px;color:var(--text-muted);line-height:1.6">{{ $desc }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- NOTICE --}}
<div class="p-4 rounded-3 d-flex gap-3" style="background:#EFF6FF;border:1px solid #BFDBFE">
    <i class="bi bi-info-circle-fill fs-4 flex-shrink-0" style="color:#1D4ED8"></i>
    <div style="font-size:13.5px;color:#1E3A8A">
        <strong>Data Privacy Notice:</strong> Employee profiles are only visible to verified employers.
        Records are government-regulated and employees are notified when their profile is searched.
        Misuse of this system may result in suspension of your employer account.
    </div>
</div>

@endsection
