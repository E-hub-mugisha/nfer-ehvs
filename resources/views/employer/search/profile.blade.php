@extends('layouts.app')
@section('title', $employee->full_name . ' — Verification Profile')
@section('page-title', 'Employee Verification Profile')

@section('content')

<div class="mb-3">
    <a href="javascript:history.back()" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Results
    </a>
</div>

{{-- PROFILE HEADER --}}
<div class="card-modern mb-4" style="border-top:4px solid var(--amber)">
    <div class="card-modern-body">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:32px;font-weight:800;color:var(--amber)">
                    {{ strtoupper(substr($employee->first_name,0,1)) }}
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-3 flex-wrap mb-1">
                    <h4 class="mb-0" style="font-family:var(--font-head);color:var(--navy)">{{ $employee->full_name }}</h4>
                    <span class="verified-shield"><i class="bi bi-patch-check-fill me-1"></i>Government Verified</span>
                </div>
                <div style="font-size:14px;color:var(--text-muted)">{{ $employee->current_title ?? 'Formal Employee' }}</div>
                <div class="d-flex gap-3 mt-2 flex-wrap" style="font-size:12.5px;color:var(--text-muted)">
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $employee->district ?? 'Rwanda' }}</span>
                    <span>
                        <i class="bi bi-circle-fill me-1 {{ $employee->employment_status==='employed'?'text-success':'text-secondary' }}" style="font-size:8px"></i>
                        {{ ucfirst(str_replace('_',' ',$employee->employment_status)) }}
                    </span>
                    <span><i class="bi bi-briefcase me-1"></i>{{ $stats['total_employers'] }} Employer{{ $stats['total_employers']!=1?'s':'' }}</span>
                    <span><i class="bi bi-clock me-1"></i>{{ $stats['years_experience'] }} Experience</span>
                </div>
            </div>
            {{-- RATING SUMMARY --}}
            @if($stats['average_rating'] > 0)
            <div class="col-auto text-center" style="min-width:100px">
                <div style="font-family:var(--font-head);font-size:40px;font-weight:800;color:var(--navy);line-height:1">{{ number_format($stats['average_rating'],1) }}</div>
                <div class="stars" style="font-size:14px">
                    @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$stats['average_rating']?'':'empty' }}"></i>@endfor
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:3px">Overall Rating</div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- MAIN: EMPLOYMENT HISTORY --}}
    <div class="col-lg-8">
        <div class="card-modern mb-4">
            <div class="card-modern-header">
                <h6><i class="bi bi-briefcase me-2"></i>Verified Employment History</h6>
                <span class="badge bg-primary rounded-pill">{{ $records->count() }} Records</span>
            </div>
            <div class="card-modern-body p-0">
                @forelse($records as $index => $rec)
                <div style="padding:20px 24px;{{ $index > 0 ? 'border-top:1px solid var(--border)' : '' }}">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-flex gap-3">
                            {{-- Company logo/initial --}}
                            <div style="width:42px;height:42px;border-radius:10px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:17px;color:var(--amber);flex-shrink:0;margin-top:2px">
                                {{ strtoupper(substr($rec->employer->company_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size:15px;font-family:var(--font-head);color:var(--navy)">{{ $rec->job_title }}</div>
                                <div style="font-size:13px;color:var(--text-muted)">{{ $rec->employer->company_name }}</div>
                                <div class="d-flex gap-3 mt-1 flex-wrap" style="font-size:12px;color:var(--text-muted)">
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $rec->start_date->format('M Y') }} — {{ $rec->end_date ? $rec->end_date->format('M Y') : 'Present' }}</span>
                                    <span><i class="bi bi-hourglass-split me-1"></i>{{ $rec->duration }}</span>
                                    <span><i class="bi bi-briefcase me-1"></i>{{ ucfirst(str_replace('_',' ',$rec->employment_type)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge-nfer badge-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span>
                            @if($rec->exit_reason)
                            <span style="font-size:11.5px;color:var(--text-muted)">
                                Left: {{ ucfirst(str_replace('_',' ',$rec->exit_reason)) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Feedback block --}}
                    @if($rec->feedback)
                    <div class="p-4 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
                        <div class="row g-3 mb-3">
                            @foreach(['punctuality','teamwork','communication','integrity','performance'] as $attr)
                            @php $val = $rec->feedback->{"rating_{$attr}"} @endphp
                            @if($val)
                            <div class="col-auto">
                                <span style="font-size:11.5px;font-weight:600;text-transform:capitalize;color:var(--text-muted)">{{ $attr }}:</span>
                                <span class="stars ms-1" style="font-size:12px">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$val?'':'empty' }}"></i>@endfor
                                </span>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        @if($rec->feedback->general_remarks)
                        <p style="font-size:13.5px;font-style:italic;color:var(--text-main);margin-bottom:12px;line-height:1.7">
                            "{{ $rec->feedback->general_remarks }}"
                        </p>
                        @endif

                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <x-conduct-badge :flag="$rec->feedback->conduct_flag" />
                            @if(!is_null($rec->feedback->would_rehire))
                            <span class="{{ $rec->feedback->would_rehire ? 'text-success' : 'text-danger' }}" style="font-size:12.5px;font-weight:600">
                                <i class="bi bi-{{ $rec->feedback->would_rehire ? 'check-circle-fill' : 'x-circle-fill' }} me-1"></i>
                                {{ $rec->feedback->would_rehire ? 'Would Rehire' : 'Would Not Rehire' }}
                            </span>
                            @endif
                            <span style="font-family:var(--font-head);font-size:16px;font-weight:800;color:var(--navy);margin-left:auto">
                                {{ $rec->feedback->overall_rating }}/5
                                <span class="stars ms-1" style="font-size:12px">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star-fill {{ $i<=$rec->feedback->overall_rating?'':'empty' }}"></i>@endfor
                                </span>
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-briefcase display-6 d-block mb-2 opacity-25"></i>
                    No visible employment records
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-lg-4">

        {{-- Conduct Summary --}}
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-shield-fill me-2"></i>Conduct Summary</h6></div>
            <div class="card-modern-body">
                @php
                $conductCounts = $stats['conduct_flags']->countBy()->sortByDesc(fn($c) => $c);
                @endphp
                @forelse($conductCounts as $flag => $count)
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <x-conduct-badge :flag="$flag" />
                    <span style="font-family:var(--font-head);font-size:16px;font-weight:700;color:var(--navy)">{{ $count }}</span>
                </div>
                @empty
                <div style="font-size:13px;color:var(--text-muted)">No conduct records yet</div>
                @endforelse

                @if($stats['would_rehire_count'] > 0 || $stats['would_not_rehire'] > 0)
                <hr style="border-color:var(--border)">
                <div class="d-flex justify-content-between" style="font-size:13px">
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Would Rehire</span>
                    <strong>{{ $stats['would_rehire_count'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mt-1" style="font-size:13px">
                    <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Would Not Rehire</span>
                    <strong>{{ $stats['would_not_rehire'] }}</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Skills --}}
        @if($skills->count())
        <div class="card-modern mb-3">
            <div class="card-modern-header"><h6><i class="bi bi-tags me-2"></i>Skills</h6></div>
            <div class="card-modern-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                    <span class="badge-nfer" style="background:var(--amber-light);color:var(--navy)">
                        {{ $skill->name }}
                        <span style="opacity:.6;font-size:10px;margin-left:3px">({{ $skill->pivot->proficiency }})</span>
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Stats Quick View --}}
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-bar-chart me-2"></i>Career Overview</h6></div>
            <div class="card-modern-body p-0">
                @php
                $overviewItems = [
                    ['Employers',        $stats['total_employers'],  'building'],
                    ['Total Experience', $stats['years_experience'], 'clock'],
                    ['Average Rating',   $stats['average_rating'] > 0 ? $stats['average_rating'].'/5' : 'N/A', 'star-fill'],
                ];
                @endphp
                @foreach($overviewItems as [$label, $val, $icon])
                <div class="d-flex align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid var(--border)">
                    <div style="width:32px;height:32px;border-radius:8px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;color:var(--amber);flex-shrink:0">
                        <i class="bi bi-{{ $icon }}" style="font-size:14px"></i>
                    </div>
                    <div class="flex-grow-1" style="font-size:12.5px;color:var(--text-muted)">{{ $label }}</div>
                    <div class="fw-bold" style="font-family:var(--font-head);font-size:15px;color:var(--navy)">{{ $val }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
