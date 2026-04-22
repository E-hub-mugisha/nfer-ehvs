@extends('layouts.app')
@section('title', 'Employers')
@section('page-title', 'Employer Registry')

@section('content')

{{-- STATUS TABS --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach(['pending'=>'Pending','verified'=>'Verified','rejected'=>'Rejected','suspended'=>'Suspended'] as $key => $label)
    <a href="{{ route('admin.employers.index', ['status'=>$key]) }}"
       class="btn {{ $status === $key ? 'btn-navy' : 'btn-outline-navy' }} btn-sm d-flex align-items-center gap-2">
        {{ $label }}
        <span style="background:rgba(255,255,255,.25);padding:1px 7px;border-radius:10px;font-size:11px">
            {{ number_format($counts[$key]) }}
        </span>
    </a>
    @endforeach
</div>

{{-- SEARCH & FILTER --}}
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" action="{{ route('admin.employers.index') }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Company name, registration no…" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Company Type</label>
                    <select name="company_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach(['private_company','public_institution','ngo','parastatal','embassy','other'] as $t)
                        <option value="{{ $t }}" {{ request('company_type')===$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.employers.index') }}" class="btn btn-outline-navy w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EMPLOYERS TABLE --}}
<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-building me-2"></i>
            {{ ucfirst($status) }} Employers
            <span class="ms-2" style="color:var(--text-muted);font-weight:400;font-size:13px">({{ $employers->total() }} total)</span>
        </h6>
    </div>
    <div class="card-modern-body p-0">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Industry</th>
                    <th>District</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($employers as $employer)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($employer->logo)
                                <img src="{{ asset($employer->logo) }}" alt="" style="width:34px;height:34px;border-radius:8px;object-fit:cover">
                            @else
                                <div style="width:34px;height:34px;border-radius:8px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--amber);font-size:14px">
                                    {{ strtoupper(substr($employer->company_name,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold" style="font-size:13.5px">{{ $employer->company_name }}</div>
                                <div style="font-size:11px;color:var(--text-muted);font-family:monospace">{{ $employer->registration_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12.5px">{{ ucfirst(str_replace('_',' ',$employer->company_type)) }}</td>
                    <td style="font-size:12.5px">{{ $employer->industry }}</td>
                    <td style="font-size:12.5px">{{ $employer->district }}</td>
                    <td>
                        <div style="font-size:12.5px">{{ $employer->contact_person }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $employer->contact_phone }}</div>
                    </td>
                    <td>
                        <span class="badge-nfer badge-{{ $employer->verification_status }}">
                            {{ ucfirst($employer->verification_status) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $employer->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.employers.show', $employer) }}" class="btn btn-outline-navy btn-sm">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:var(--text-muted)">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        No {{ $status }} employers found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employers->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid var(--border)">
        {{ $employers->links() }}
    </div>
    @endif
</div>

@endsection
