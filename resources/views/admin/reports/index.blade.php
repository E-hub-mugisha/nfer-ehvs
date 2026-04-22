@extends('layouts.app')
@section('title', 'National Labour Reports')
@section('page-title', 'National Labour Reports')

@section('content')

{{-- YEAR FILTER --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <form method="GET" class="d-flex gap-2 align-items-center">
        <label class="form-label mb-0 fw-semibold" style="white-space:nowrap">Year:</label>
        <select name="year" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
            @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        @foreach(['employees','employers','records','disputes'] as $type)
        <a href="{{ route('admin.reports.export', ['type'=>$type,'format'=>'csv']) }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-download me-1"></i>Export {{ ucfirst($type) }}
        </a>
        @endforeach
    </div>
</div>

{{-- SUMMARY STATS --}}
<div class="row g-3 mb-4">
    @php
    $summaryCards = [
        ['Employees',          $summary['employees'],          $summary['verified_employees'].' verified',   'people-fill'],
        ['Employers',          $summary['employers'],          $summary['verified_employers'].' verified',   'building-fill'],
        ['Employment Records', $summary['records'],            $summary['active_records'].' active',         'file-earmark-person-fill'],
        ['Disputes',           $summary['disputes'],           $summary['resolved_disputes'].' resolved',    'shield-exclamation'],
    ];
    @endphp
    @foreach($summaryCards as [$label, $val, $sub, $icon])
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon mb-3"><i class="bi bi-{{ $icon }}"></i></div>
            <div class="stat-value">{{ number_format($val) }}</div>
            <div class="stat-label mt-1">{{ $label }}</div>
            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px">{{ $sub }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    {{-- MONTHLY CHART --}}
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-bar-chart me-2"></i>Monthly Registrations — {{ $year }}</h6>
            </div>
            <div class="card-modern-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- EXIT REASONS --}}
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-door-open me-2"></i>Exit Reasons</h6></div>
            <div class="card-modern-body p-0">
                @php $total = $exitBreakdown->sum('total') ?: 1; @endphp
                @foreach($exitBreakdown as $e)
                <div class="px-4 py-2 d-flex align-items-center gap-3" style="border-bottom:1px solid var(--border)">
                    <div style="flex:1;font-size:12.5px">{{ ucfirst(str_replace('_',' ',$e->exit_reason)) }}</div>
                    <div style="width:80px">
                        <div style="height:6px;border-radius:3px;background:var(--border)">
                            <div style="height:6px;border-radius:3px;background:var(--amber);width:{{ round($e->total/$total*100) }}%"></div>
                        </div>
                    </div>
                    <div style="font-size:12px;font-weight:600;color:var(--navy);min-width:35px;text-align:right">{{ $e->total }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- CONDUCT FLAGS --}}
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-shield-fill me-2"></i>Conduct Flag Distribution</h6></div>
            <div class="card-modern-body">
                <canvas id="conductChart" height="180"></canvas>
            </div>
        </div>
    </div>

    {{-- EMPLOYMENT TYPES --}}
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-briefcase me-2"></i>Employment Types</h6></div>
            <div class="card-modern-body">
                <canvas id="typeChart" height="180"></canvas>
            </div>
        </div>
    </div>

    {{-- COMPANY TYPES --}}
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-building me-2"></i>Employer Categories</h6></div>
            <div class="card-modern-body">
                <canvas id="companyChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP INDUSTRIES --}}
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-graph-up me-2"></i>Top Industries (Verified Employers)</h6></div>
            <div class="card-modern-body p-0">
                @php $maxInd = $topIndustries->max('total') ?: 1; @endphp
                @foreach($topIndustries as $ind)
                <div class="px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid var(--border)">
                    <div style="flex:1;font-size:13px;font-weight:500">{{ $ind->industry }}</div>
                    <div style="width:120px">
                        <div style="height:7px;border-radius:4px;background:var(--border)">
                            <div style="height:7px;border-radius:4px;background:var(--navy);width:{{ round($ind->total/$maxInd*100) }}%"></div>
                        </div>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:var(--navy);min-width:28px;text-align:right">{{ $ind->total }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TOP DISTRICTS --}}
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-geo-alt me-2"></i>Employee Distribution by District</h6></div>
            <div class="card-modern-body p-0">
                @php $maxDist = $districts->max('total') ?: 1; @endphp
                @foreach($districts as $dist)
                <div class="px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid var(--border)">
                    <div style="flex:1;font-size:13px;font-weight:500">{{ $dist->district }}</div>
                    <div style="width:120px">
                        <div style="height:7px;border-radius:4px;background:var(--border)">
                            <div style="height:7px;border-radius:4px;background:var(--amber);width:{{ round($dist->total/$maxDist*100) }}%"></div>
                        </div>
                    </div>
                    <div style="font-size:13px;font-weight:700;color:var(--navy);min-width:28px;text-align:right">{{ $dist->total }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const navy  = '#0D2137';
const amber = '#E8A020';
const muted = '#64748B';

// Monthly Chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: @json($months->pluck('label')),
        datasets: [
            { label:'Employees', data: @json($months->pluck('employees')), backgroundColor: navy, borderRadius:5 },
            { label:'Records',   data: @json($months->pluck('records')),   backgroundColor: amber, borderRadius:5 },
        ]
    },
    options: {
        plugins:{ legend:{ position:'top', labels:{ font:{ size:12 }, boxWidth:12 } } },
        scales:{ y:{ beginAtZero:true, ticks:{ font:{size:11} } }, x:{ ticks:{ font:{size:11} } } },
        responsive: true
    }
});

// Conduct Chart
new Chart(document.getElementById('conductChart'), {
    type: 'doughnut',
    data: {
        labels: @json($conductBreakdown->pluck('conduct_flag')->map(fn($f)=>ucwords(str_replace('_',' ',$f)))),
        datasets:[{ data: @json($conductBreakdown->pluck('total')), backgroundColor:['#16A34A','#CA8A04','#EA580C','#DC2626'], borderWidth:0 }]
    },
    options: { plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, boxWidth:12 } } }, cutout:'65%' }
});

// Employment Type Chart
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: @json($employmentTypes->pluck('employment_type')->map(fn($t)=>ucwords(str_replace('_',' ',$t)))),
        datasets:[{ data: @json($employmentTypes->pluck('total')), backgroundColor:['#0D2137','#1A3A5C','#E8A020','#94A3B8'], borderWidth:0 }]
    },
    options: { plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, boxWidth:12 } } }, cutout:'65%' }
});

// Company Types Chart
new Chart(document.getElementById('companyChart'), {
    type: 'doughnut',
    data: {
        labels: @json($companyTypes->pluck('company_type')->map(fn($t)=>ucwords(str_replace('_',' ',$t)))),
        datasets:[{ data: @json($companyTypes->pluck('total')), backgroundColor:['#0D2137','#1A3A5C','#2D5F8A','#E8A020','#F59E0B','#94A3B8'], borderWidth:0 }]
    },
    options: { plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, boxWidth:12 } } }, cutout:'65%' }
});
</script>
@endpush

@endsection
