@extends('layouts.app')
@section('title', 'Search Results')
@section('page-title', 'Search Results')

@section('content')

{{-- SEARCH FORM (compact) --}}
<div class="card-modern mb-4" style="border-left:4px solid var(--amber)">
    <div class="card-modern-body">
        <form action="{{ route('employer.search.results') }}" method="GET">
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <div style="flex:1;min-width:240px">
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                        placeholder="National ID or name…" required minlength="3">
                </div>
                <div class="d-flex gap-3">
                    @foreach([''  =>'All', 'employed'  =>'Employed', 'unemployed'=>'Available'] as $v => $l)
                    <label style="cursor:pointer;font-size:13px;white-space:nowrap">
                        <input type="radio" name="status" value="{{ $v }}" {{ request('status')===$v?'checked':'' }}> {{ $l }}
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-navy"><i class="bi bi-search me-2"></i>Search</button>
                <a href="{{ route('employer.search.index') }}" class="btn btn-outline-navy">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- RESULTS HEADER --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <span style="font-size:14px;font-weight:600;color:var(--navy)">{{ $employees->total() }} result{{ $employees->total() != 1 ? 's' : '' }}</span>
        <span style="font-size:13px;color:var(--text-muted)"> for "<strong>{{ request('q') }}</strong>"</span>
    </div>
</div>

{{-- RESULTS GRID --}}
@forelse($employees as $emp)
<div class="card-modern mb-3">
    <div class="card-modern-body">
        <div class="row align-items-center">
            <div class="col-md-1 text-center">
                <div style="width:52px;height:52px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:20px;font-weight:800;color:var(--amber)">
                    {{ strtoupper(substr($emp->first_name,0,1)) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-bold" style="font-size:15px;font-family:var(--font-head);color:var(--navy)">{{ $emp->full_name }}</span>
                    @if($emp->is_verified)
                    <span class="verified-shield" style="font-size:10.5px"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                    @endif
                </div>
                <div style="font-size:13px;color:var(--text-muted)">{{ $emp->current_title ?? 'No title set' }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                    <i class="bi bi-geo-alt me-1"></i>{{ $emp->district ?? 'Not specified' }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $emp->employment_status==='employed'?'#16A34A':'#94A3B8' }}"></div>
                    <span style="font-size:13px">{{ ucfirst(str_replace('_',' ',$emp->employment_status)) }}</span>
                </div>
                @if($emp->average_rating > 0)
                <div class="d-flex align-items-center gap-2">
                    <span class="stars" style="font-size:12px">
                        @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$emp->average_rating?'':'empty' }}"></i>@endfor
                    </span>
                    <span style="font-size:12px;color:var(--text-muted)">{{ $emp->average_rating }}/5</span>
                </div>
                @else
                <div style="font-size:12px;color:var(--text-muted)">No ratings yet</div>
                @endif
            </div>
            <div class="col-md-2 text-center">
                <div style="font-family:var(--font-head);font-size:20px;font-weight:800;color:var(--navy)">
                    {{ $emp->employmentRecords()->visible()->count() }}
                </div>
                <div style="font-size:11px;color:var(--text-muted)">Employment Records</div>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('employer.search.show', $emp) }}" class="btn btn-amber">
                    <i class="bi bi-person-lines-fill me-2"></i>View Profile
                </a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <i class="bi bi-search display-4 text-muted d-block mb-3"></i>
    <h5 style="font-family:var(--font-head);color:var(--navy)">No verified employees found</h5>
    <p style="color:var(--text-muted);font-size:14px">
        No results for "<strong>{{ request('q') }}</strong>".
        The employee may not be registered or their profile may not be verified yet.
    </p>
    <a href="{{ route('employer.search.index') }}" class="btn btn-outline-navy mt-2">Try another search</a>
</div>
@endforelse

@if($employees->hasPages())
<div class="mt-4">{{ $employees->appends(request()->query())->links() }}</div>
@endif

@endsection
