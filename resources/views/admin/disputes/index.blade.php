@extends('layouts.app')
@section('title', 'Disputes')
@section('page-title', 'Dispute Management')

@section('content')

{{-- STATUS TABS --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach(['open'=>'Open','under_review'=>'Under Review','resolved'=>'Resolved','dismissed'=>'Dismissed'] as $key => $label)
    <a href="{{ route('admin.disputes.index', ['status'=>$key]) }}"
       class="btn {{ request('status',$key==='open'?'open':null)===$key ? 'btn-navy' : 'btn-outline-navy' }} btn-sm d-flex align-items-center gap-2">
        @if($key==='open')<span style="width:7px;height:7px;border-radius:50%;background:#DC2626;display:inline-block"></span>@endif
        {{ $label }}
        <span style="background:rgba(255,255,255,.25);padding:1px 7px;border-radius:10px;font-size:11px">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

{{-- FILTERS --}}
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET">
            <input type="hidden" name="status" value="{{ request('status','open') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search by Claimant</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or email…" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dispute Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach(['incorrect_dates','wrong_exit_reason','unfair_feedback','false_record','other'] as $t)
                        <option value="{{ $t }}" {{ request('type')===$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-navy w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
                <div class="col-md-2"><a href="{{ route('admin.disputes.index') }}" class="btn btn-outline-navy w-100">Reset</a></div>
            </div>
        </form>
    </div>
</div>

{{-- DISPUTES TABLE --}}
<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-shield-exclamation me-2"></i>Disputes
            <span class="ms-2" style="font-weight:400;font-size:13px;color:var(--text-muted)">({{ $disputes->total() }} total)</span>
        </h6>
    </div>
    <div class="card-modern-body p-0">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Raised By</th>
                    <th>Type</th>
                    <th>Employment Record</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Resolved By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $d)
                <tr>
                    <td style="font-size:12px;color:var(--text-muted)">#{{ $d->id }}</td>
                    <td>
                        <div class="fw-semibold" style="font-size:13px">{{ $d->raisedBy->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $d->raisedBy->email }}</div>
                    </td>
                    <td style="font-size:12.5px">{{ ucfirst(str_replace('_',' ',$d->dispute_type)) }}</td>
                    <td>
                        @if($d->employmentRecord)
                        <div style="font-size:12.5px;font-weight:600">{{ $d->employmentRecord->employee->full_name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">@ {{ $d->employmentRecord->employer->company_name ?? '—' }}</div>
                        @else —
                        @endif
                    </td>
                    <td>
                        @php
                        $dMap = ['open'=>'badge-rejected','under_review'=>'badge-pending','resolved'=>'badge-active','dismissed'=>'badge-suspended'];
                        @endphp
                        <span class="badge-nfer {{ $dMap[$d->status] ?? 'badge-pending' }}">
                            {{ ucfirst(str_replace('_',' ',$d->status)) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $d->created_at->format('d M Y') }}</td>
                    <td style="font-size:12.5px">{{ $d->resolvedBy?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.disputes.show', $d) }}" class="btn btn-outline-navy btn-sm">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:var(--text-muted)">
                        <i class="bi bi-shield-check display-6 text-success d-block mb-2"></i>
                        No disputes found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($disputes->hasPages())
    <div class="px-4 py-3" style="border-top:1px solid var(--border)">{{ $disputes->links() }}</div>
    @endif
</div>

@endsection
