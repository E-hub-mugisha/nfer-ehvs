@extends('layouts.app')

@section('title', 'My Disputes')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap');

    :root {
        --ink: #0f1923; --slate: #2c3e50; --muted: #64748b;
        --border: #dde3ec; --surface: #f4f6fa; --card: #ffffff;
        --accent: #1a56db; --accent-lt: #ebf1fd;
        --green: #0e7a50; --green-lt: #e6f5ef;
        --amber: #b45309; --amber-lt: #fef3cd;
        --red: #be123c; --red-lt: #fde8ee;
        --radius: 6px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'IBM Plex Sans', sans-serif; background: var(--surface); color: var(--ink); font-size: 14px; line-height: 1.6; }

    .topbar { background: var(--card); border-bottom: 1px solid var(--border); padding: 0 32px; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
    .topbar-brand { display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 13px; letter-spacing: .04em; color: var(--accent); text-transform: uppercase; }
    .avatar-sm { width: 32px; height: 32px; border-radius: 50%; background: var(--accent-lt); border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; color: var(--accent); overflow: hidden; }
    .avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
    .topbar-right { display: flex; align-items: center; gap: 20px; font-size: 13px; color: var(--slate); }

    .layout { display: flex; min-height: calc(100vh - 56px); }
    .sidebar { width: 220px; background: var(--card); border-right: 1px solid var(--border); padding: 24px 0; flex-shrink: 0; }
    .nav-label { padding: 0 20px 6px; font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
    .nav-section { margin-bottom: 24px; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 20px; color: var(--slate); text-decoration: none; font-size: 13.5px; border-left: 3px solid transparent; transition: all .15s; }
    .nav-item:hover { background: var(--surface); color: var(--accent); }
    .nav-item.active { background: var(--accent-lt); color: var(--accent); border-left-color: var(--accent); font-weight: 500; }

    .main { flex: 1; padding: 28px 32px; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .page-title { font-size: 20px; font-weight: 600; color: var(--ink); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 500; font-family: 'IBM Plex Mono', monospace; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-green  { background: var(--green-lt);  color: var(--green); } .badge-green  .badge-dot { background: var(--green);  }
    .badge-amber  { background: var(--amber-lt);  color: var(--amber); } .badge-amber  .badge-dot { background: var(--amber);  }
    .badge-blue   { background: var(--accent-lt); color: var(--accent); } .badge-blue  .badge-dot { background: var(--accent); }
    .badge-red    { background: var(--red-lt);    color: var(--red); }   .badge-red   .badge-dot { background: var(--red);    }
    .badge-gray   { background: var(--surface); color: var(--muted); border: 1px solid var(--border); }

    .btn { padding: 9px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 7px; transition: opacity .15s, transform .1s; }
    .btn:hover { opacity: .85; transform: translateY(-1px); }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--slate); }
    .btn-sm { padding: 6px 12px; font-size: 12px; }

    /* ── Stats ── */
    .stats-row { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-chip { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px 16px; display: flex; align-items: center; gap: 10px; min-width: 120px; }
    .stat-chip-val { font-size: 20px; font-weight: 600; font-family: 'IBM Plex Mono', monospace; color: var(--ink); }
    .stat-chip-label { font-size: 11.5px; color: var(--muted); }

    /* ── Filter bar ── */
    .filter-bar { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px 16px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
    .filter-label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; margin-right: 4px; }
    .filter-btn { padding: 6px 14px; border-radius: 20px; font-size: 12.5px; border: 1px solid var(--border); background: transparent; color: var(--slate); cursor: pointer; font-family: inherit; transition: all .15s; }
    .filter-btn:hover { background: var(--surface); }
    .filter-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* ── Dispute cards ── */
    .dispute-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 14px; overflow: hidden; transition: box-shadow .2s; }
    .dispute-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    .dispute-card.status-open   { border-left: 4px solid var(--amber); }
    .dispute-card.status-resolved { border-left: 4px solid var(--green); }
    .dispute-card.status-rejected { border-left: 4px solid var(--red); }
    .dispute-card.status-under_review { border-left: 4px solid var(--accent); }

    .dispute-head { padding: 16px 20px; display: flex; align-items: flex-start; gap: 14px; }
    .dispute-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .dispute-icon.open     { background: var(--amber-lt); color: var(--amber); }
    .dispute-icon.resolved { background: var(--green-lt);  color: var(--green);  }
    .dispute-icon.rejected { background: var(--red-lt);    color: var(--red);    }
    .dispute-icon.review   { background: var(--accent-lt); color: var(--accent); }

    .dispute-title { font-size: 14px; font-weight: 600; color: var(--ink); line-height: 1.3; }
    .dispute-company { font-size: 12.5px; color: var(--muted); margin-top: 2px; }
    .dispute-meta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 6px; }
    .dispute-meta-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted); }
    .dispute-right { margin-left: auto; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
    .dispute-date { font-size: 11.5px; font-family: 'IBM Plex Mono', monospace; color: var(--muted); }

    .dispute-body { padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface); }
    .dispute-desc { font-size: 13px; color: var(--slate); line-height: 1.6; }

    /* ── Admin response ── */
    .admin-response { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px 16px; margin-top: 10px; }
    .admin-response-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .admin-response-text { font-size: 13px; color: var(--slate); line-height: 1.6; }

    /* ── Dispute form ── */
    .new-dispute-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 24px; overflow: hidden; }
    .new-dispute-head { padding: 14px 20px; border-bottom: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none; }
    .new-dispute-head-left { display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: var(--ink); }
    .new-dispute-body { display: none; padding: 20px; }
    .new-dispute-body.open { display: block; }

    .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
    .field-label { font-size: 12px; font-weight: 600; color: var(--slate); }
    .field-label .req { color: var(--red); margin-left: 2px; }
    .input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius); font-family: inherit; font-size: 13.5px; color: var(--ink); background: var(--card); outline: none; transition: border-color .15s, box-shadow .15s; }
    .input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(26,86,219,.1); }
    select.input { cursor: pointer; }
    textarea.input { resize: vertical; min-height: 90px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media(max-width:600px) { .form-grid { grid-template-columns: 1fr; } }
    .field-error { font-size: 11.5px; color: var(--red); }

    /* ── Alert ── */
    .alert { display: flex; gap: 10px; padding: 12px 16px; border-radius: var(--radius); font-size: 13px; margin-bottom: 20px; }
    .alert-success { background: var(--green-lt); border: 1px solid #a3d9be; color: var(--green); }
    .alert-info    { background: var(--accent-lt); border: 1px solid #bbd3f8; color: var(--accent); }

    /* ── Empty ── */
    .empty-state { text-align: center; padding: 48px 20px; color: var(--muted); }
    .empty-state svg { margin-bottom: 14px; opacity: .3; }
    .empty-title { font-size: 15px; font-weight: 500; color: var(--slate); margin-bottom: 4px; }

    /* ── Pagination ── */
    .pagination { display: flex; align-items: center; gap: 6px; margin-top: 24px; justify-content: center; }
    .page-btn { padding: 7px 12px; border-radius: var(--radius); border: 1px solid var(--border); background: var(--card); color: var(--slate); font-size: 13px; text-decoration: none; transition: background .15s; }
    .page-btn:hover { background: var(--surface); }
    .page-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
    .page-btn.disabled { opacity: .4; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="topbar">
    <div class="topbar-brand">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><path d="M8 7V5a2 2 0 0 0-4 0v2"/>
        </svg>
        NFER-EHVS
    </div>
    <div class="topbar-right">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="avatar-sm">
                @if($employee->profile_photo)
                    <img src="{{ asset('storage/'.$employee->profile_photo) }}" alt="">
                @else
                    {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                @endif
            </div>
            {{ $employee->first_name }}
        </div>
    </div>
</div>

<div class="layout">
    <nav class="sidebar">
        <div class="nav-section">
            <div class="nav-label">Employee</div>
            <a href="{{ route('employee.dashboard') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('employee.profile.show') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                My Profile
            </a>
            <a href="{{ route('employee.history.index') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                Employment History
            </a>
            <a href="{{ route('employee.disputes.index') }}" class="nav-item active">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Disputes
            </a>
        </div>
    </nav>

    <main class="main">
        <div class="page-header">
            <div>
                <div class="page-title">Disputes</div>
                <div class="page-sub">Challenge inaccurate employment records or employer feedback</div>
            </div>
            <button class="btn btn-primary" onclick="toggleNewDispute()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Dispute
            </button>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Stats ── --}}
        <div class="stats-row">
            @foreach(['total'=>['label'=>'Total','color'=>'var(--ink)'],'open'=>['label'=>'Open','color'=>'var(--amber)'],'under_review'=>['label'=>'In Review','color'=>'var(--accent)'],'resolved'=>['label'=>'Resolved','color'=>'var(--green)'],'rejected'=>['label'=>'Rejected','color'=>'var(--red)']] as $status=>$cfg)
            <div class="stat-chip">
                <div class="stat-chip-val" style="color:{{ $cfg['color'] }};">{{ $disputeCounts[$status] ?? 0 }}</div>
                <div class="stat-chip-label">{{ $cfg['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- New dispute form ── --}}
        <div class="new-dispute-card" id="newDisputeCard">
            <div class="new-dispute-head" onclick="toggleNewDispute()" id="newDisputeToggle">
                <div class="new-dispute-head-left">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    File a New Dispute
                </div>
                <svg id="newDisputeChevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform .2s;"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="new-dispute-body {{ request('record_id') ? 'open' : '' }}" id="newDisputeBody">
                <div class="alert alert-info" style="margin-bottom:16px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Disputes are reviewed by NFER-EHVS administration within 5–7 business days. Provide as much detail as possible.
                </div>

                <form action="{{ route('employee.disputes.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Employment Record <span class="req">*</span></label>
                            <select name="employment_record_id" class="input" required>
                                <option value="">Select a record…</option>
                                @foreach($employmentRecords as $rec)
                                    <option value="{{ $rec->id }}" {{ request('record_id') == $rec->id ? 'selected' : '' }}>
                                        {{ $rec->job_title }} @ {{ $rec->employer->company_name ?? '—' }}
                                        ({{ $rec->start_date->format('M Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employment_record_id') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Dispute Type <span class="req">*</span></label>
                            <select name="type" class="input" required>
                                <option value="">Select type…</option>
                                <option value="incorrect_dates" {{ old('type')=='incorrect_dates'?'selected':'' }}>Incorrect Employment Dates</option>
                                <option value="incorrect_exit_reason" {{ old('type')=='incorrect_exit_reason'?'selected':'' }}>Incorrect Exit Reason</option>
                                <option value="unfair_feedback" {{ old('type')=='unfair_feedback'?'selected':'' }}>Unfair / False Feedback</option>
                                <option value="incorrect_job_title" {{ old('type')=='incorrect_job_title'?'selected':'' }}>Incorrect Job Title</option>
                                <option value="other" {{ old('type')=='other'?'selected':'' }}>Other</option>
                            </select>
                            @error('type') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="grid-column:1/-1;">
                            <label class="field-label">Title <span class="req">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" class="input" placeholder="Brief summary of your dispute" required>
                            @error('title') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="grid-column:1/-1;">
                            <label class="field-label">Description <span class="req">*</span></label>
                            <textarea name="description" class="input" placeholder="Explain the inaccuracy or issue in detail. Include dates, correct information, and any evidence you can reference." required>{{ old('description') }}</textarea>
                            @error('description') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:4px;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="toggleNewDispute()">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Submit Dispute
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filter bar ── --}}
        <div class="filter-bar">
            <span class="filter-label">Filter:</span>
            @foreach(['all'=>'All','open'=>'Open','under_review'=>'In Review','resolved'=>'Resolved','rejected'=>'Rejected'] as $val=>$label)
            <a href="{{ route('employee.disputes.index', array_merge(request()->except('status','page'), ['status'=>$val=='all'?null:$val])) }}"
               class="filter-btn {{ (request('status',$val=='all'?null:$val) == ($val=='all'?null:$val) && (request('status') === null && $val==='all' || request('status') === $val)) ? 'active' : '' }}">
                {{ $label }}
                @if($val !== 'all' && isset($disputeCounts[$val]) && $disputeCounts[$val] > 0)
                    ({{ $disputeCounts[$val] }})
                @endif
            </a>
            @endforeach
        </div>

        {{-- Disputes list ── --}}
        @forelse($disputes as $dispute)
        @php
            $statusMap = ['open'=>'amber','under_review'=>'blue','resolved'=>'green','rejected'=>'red'];
            $iconClass = match($dispute->status) { 'open'=>'open', 'under_review'=>'review', 'resolved'=>'resolved', 'rejected'=>'rejected', default=>'open' };
        @endphp
        <div class="dispute-card status-{{ $dispute->status }}">
            <div class="dispute-head">
                <div class="dispute-icon {{ $iconClass }}">
                    @if($dispute->status === 'resolved')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    @elseif($dispute->status === 'rejected')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @elseif($dispute->status === 'under_review')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
                    @endif
                </div>
                <div style="flex:1;">
                    <div class="dispute-title">{{ $dispute->title }}</div>
                    <div class="dispute-company">
                        Re: {{ $dispute->employmentRecord->job_title ?? '—' }} @ {{ $dispute->employmentRecord->employer->company_name ?? '—' }}
                    </div>
                    <div class="dispute-meta">
                        <span class="dispute-meta-item">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Filed {{ $dispute->created_at->format('d M Y') }}
                        </span>
                        <span class="dispute-meta-item">
                            Type: {{ ucfirst(str_replace('_',' ',$dispute->type)) }}
                        </span>
                        <span class="dispute-meta-item" style="font-family:'IBM Plex Mono',monospace; font-size:11px;">
                            #{{ str_pad($dispute->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
                <div class="dispute-right">
                    <span class="badge badge-{{ $statusMap[$dispute->status] ?? 'gray' }}">
                        <span class="badge-dot"></span>{{ ucfirst(str_replace('_',' ',$dispute->status)) }}
                    </span>
                    @if($dispute->resolved_at)
                        <span class="dispute-date">{{ $dispute->resolved_at->format('d M Y') }}</span>
                    @endif
                </div>
            </div>

            @if($dispute->description || $dispute->admin_response)
            <div class="dispute-body">
                @if($dispute->description)
                <div class="dispute-desc">{{ Str::limit($dispute->description, 300) }}</div>
                @endif

                @if($dispute->admin_response)
                <div class="admin-response">
                    <div class="admin-response-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Administration Response
                        @if($dispute->resolved_at)
                            <span style="font-weight:400; color:var(--muted);">— {{ $dispute->resolved_at->format('d M Y') }}</span>
                        @endif
                    </div>
                    <div class="admin-response-text">{{ $dispute->admin_response }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div class="empty-title">No disputes filed</div>
            <div style="font-size:13px;">If you find any inaccurate records, use the form above to file a dispute.</div>
        </div>
        @endforelse

        {{-- Pagination ── --}}
        @if($disputes->hasPages())
        <div class="pagination">
            @if($disputes->onFirstPage())
                <span class="page-btn disabled">&lsaquo;</span>
            @else
                <a href="{{ $disputes->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
            @endif
            @foreach($disputes->getUrlRange(1, $disputes->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page == $disputes->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($disputes->hasMorePages())
                <a href="{{ $disputes->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
            @else
                <span class="page-btn disabled">&rsaquo;</span>
            @endif
        </div>
        @endif

    </main>
</div>

@push('scripts')
<script>
function toggleNewDispute() {
    const body = document.getElementById('newDisputeBody');
    const chevron = document.getElementById('newDisputeChevron');
    const isOpen = body.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(180deg)' : '';
}
// Auto-open if validation errors for new dispute
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('newDisputeBody').classList.add('open');
    document.getElementById('newDisputeChevron').style.transform = 'rotate(180deg)';
});
@endif
</script>
@endpush
@endsection