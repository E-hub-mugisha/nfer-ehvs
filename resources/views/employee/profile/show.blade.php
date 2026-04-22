@extends('layouts.app')

@section('title', 'My Profile')

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
    .nav-item svg { flex-shrink: 0; opacity: .75; }
    .nav-item.active svg { opacity: 1; }

    .main { flex: 1; padding: 28px 32px; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .page-title { font-size: 20px; font-weight: 600; color: var(--ink); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

    .btn-sm { padding: 7px 14px; border-radius: var(--radius); font-size: 12.5px; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 6px; transition: opacity .15s, transform .1s; white-space: nowrap; }
    .btn-sm:hover { opacity: .85; transform: translateY(-1px); }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--slate); }
    .btn-danger  { background: transparent; border: 1px solid var(--red); color: var(--red); }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 500; font-family: 'IBM Plex Mono', monospace; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-green  { background: var(--green-lt);  color: var(--green); } .badge-green  .badge-dot { background: var(--green);  }
    .badge-amber  { background: var(--amber-lt);  color: var(--amber); } .badge-amber  .badge-dot { background: var(--amber);  }
    .badge-blue   { background: var(--accent-lt); color: var(--accent); } .badge-blue  .badge-dot { background: var(--accent); }

    /* ── Profile hero ── */
    .hero-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; margin-bottom: 24px; }
    .hero-cover { height: 100px; background: linear-gradient(120deg, #1a56db 0%, #1e3a8a 100%); position: relative; }
    .hero-cover-pattern { position: absolute; inset: 0; opacity: .07; background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%); background-size: 10px 10px; }
    .hero-body { padding: 0 28px 24px; position: relative; }
    .avatar-xl {
        width: 90px; height: 90px; border-radius: 50%;
        border: 4px solid var(--card);
        background: var(--accent-lt);
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; font-weight: 600; color: var(--accent);
        overflow: hidden;
        position: relative; top: -45px; margin-bottom: -30px;
    }
    .avatar-xl img { width: 100%; height: 100%; object-fit: cover; }
    .hero-name { font-size: 20px; font-weight: 600; color: var(--ink); }
    .hero-title { font-size: 13.5px; color: var(--muted); margin: 3px 0 12px; }
    .hero-actions { position: absolute; top: 16px; right: 28px; display: flex; gap: 8px; }

    /* ── Grid ── */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
    @media(max-width:900px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }

    /* ── Card ── */
    .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 20px; }
    .card-head { padding: 14px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-size: 13.5px; font-weight: 600; color: var(--ink); }
    .card-body { padding: 20px; }

    /* ── Field rows ── */
    .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media(max-width:600px) { .field-grid { grid-template-columns: 1fr; } }
    .field { }
    .field-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); margin-bottom: 4px; }
    .field-value { font-size: 13.5px; color: var(--ink); }
    .field-value.mono { font-family: 'IBM Plex Mono', monospace; }
    .field-value.empty { color: var(--muted); font-style: italic; }

    .divider { height: 1px; background: var(--border); margin: 16px 0; }

    /* ── Verification status card ── */
    .verify-card { border-radius: var(--radius); padding: 16px 20px; display: flex; gap: 14px; align-items: flex-start; }
    .verify-card.pending { background: var(--amber-lt); border: 1px solid #f5d77a; }
    .verify-card.verified { background: var(--green-lt); border: 1px solid #a3d9be; }
    .verify-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .verify-card.pending .verify-icon { background: #fde68a; color: var(--amber); }
    .verify-card.verified .verify-icon { background: #bbf0d5; color: var(--green); }
    .verify-card-title { font-size: 14px; font-weight: 600; }
    .verify-card.pending .verify-card-title { color: var(--amber); }
    .verify-card.verified .verify-card-title { color: var(--green); }
    .verify-card-sub { font-size: 12.5px; color: var(--muted); margin-top: 2px; }

    /* ── Skills ── */
    .skill-tag { display: inline-flex; align-items: center; gap: 8px; padding: 5px 12px; border-radius: 4px; background: var(--surface); border: 1px solid var(--border); font-size: 12.5px; color: var(--slate); margin: 3px; }
    .proficiency-pip { display: inline-flex; gap: 3px; }
    .pip { width: 6px; height: 6px; border-radius: 50%; background: var(--border); }
    .pip.on { background: var(--accent); }

    /* ── Rating visual ── */
    .stars { display: flex; gap: 3px; }
    .star { width: 16px; height: 16px; color: var(--border); }
    .star.on { color: #f59e0b; }
    .big-rating { font-size: 36px; font-weight: 300; font-family: 'IBM Plex Mono', monospace; color: var(--ink); line-height: 1; }

    /* ── Bio ── */
    .bio-text { font-size: 13.5px; color: var(--slate); line-height: 1.7; }

    /* ── Alert info ── */
    .alert-info { background: var(--accent-lt); border: 1px solid #bbd3f8; border-radius: var(--radius); padding: 12px 16px; font-size: 13px; color: var(--accent); display: flex; gap: 10px; margin-bottom: 20px; }
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
            <a href="{{ route('employee.profile.show') }}" class="nav-item active">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                My Profile
            </a>
            <a href="{{ route('employee.history.index') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                Employment History
            </a>
            <a href="{{ route('employee.disputes.index') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Disputes
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Account</div>
            <a href="{{ route('employee.profile.edit') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Profile
            </a>
        </div>
    </nav>

    <main class="main">
        <div class="page-header">
            <div>
                <div class="page-title">My Profile</div>
                <div class="page-sub">View your registered information on the NFER-EHVS system</div>
            </div>
            <a href="{{ route('employee.profile.edit') }}" class="btn-sm btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Profile
            </a>
        </div>

        {{-- Hero --}}
        <div class="hero-card">
            <div class="hero-cover"><div class="hero-cover-pattern"></div></div>
            <div class="hero-body">
                <div style="display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:flex-end; gap:16px;">
                        <div class="avatar-xl">
                            @if($employee->profile_photo)
                                <img src="{{ asset('storage/'.$employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                            @else
                                {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                            @endif
                        </div>
                        <div>
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <div class="hero-name">{{ $employee->full_name }}</div>
                                @if($employee->is_verified)
                                    <span class="badge badge-green"><span class="badge-dot"></span>Verified</span>
                                @else
                                    <span class="badge badge-amber"><span class="badge-dot"></span>Pending</span>
                                @endif
                            </div>
                            <div class="hero-title">{{ $employee->current_title ?? 'No title set' }}</div>
                            @if($employee->linkedin_url)
                            <a href="{{ $employee->linkedin_url }}" target="_blank" rel="noopener" style="font-size:12.5px; color:var(--accent); text-decoration:none; display:flex; align-items:center; gap:5px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                                LinkedIn Profile
                            </a>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; padding-bottom:4px;">
                        <div style="text-align:center;">
                            <div class="big-rating">{{ number_format($employee->average_rating, 1) }}</div>
                            <div class="stars" style="justify-content:center; margin-top:4px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="star {{ $i <= round($employee->average_rating) ? 'on' : '' }}" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                @endfor
                            </div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">{{ $employee->feedbacks->count() }} reviews</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verification status --}}
        <div class="verify-card {{ $employee->is_verified ? 'verified' : 'pending' }}" style="margin-bottom:20px;">
            <div class="verify-icon">
                @if($employee->is_verified)
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @endif
            </div>
            <div>
                <div class="verify-card-title">
                    @if($employee->is_verified) Identity Verified @else Verification Pending @endif
                </div>
                <div class="verify-card-sub">
                    @if($employee->is_verified)
                        Verified on {{ $employee->verified_at->format('d F Y') }}. Your profile is visible to registered employers.
                    @else
                        Your National ID is being reviewed by the NFER-EHVS administration. This usually takes 1–3 business days.
                    @endif
                </div>
            </div>
        </div>

        {{-- Personal Information --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">Personal Information</span>
            </div>
            <div class="card-body">
                <div class="field-grid">
                    <div class="field">
                        <div class="field-label">National ID</div>
                        <div class="field-value mono">{{ $employee->national_id }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Full Name</div>
                        <div class="field-value">{{ $employee->full_name }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Date of Birth</div>
                        <div class="field-value">{{ $employee->date_of_birth->format('d F Y') }} <span style="color:var(--muted); font-size:12px;">({{ $employee->date_of_birth->age }} yrs)</span></div>
                    </div>
                    <div class="field">
                        <div class="field-label">Gender</div>
                        <div class="field-value">{{ ucfirst($employee->gender ?? '—') }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Nationality</div>
                        <div class="field-value">{{ $employee->nationality ?? '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Employment Status</div>
                        <div class="field-value">
                            <span class="badge {{ match($employee->employment_status) { 'employed'=>'badge-green', 'unemployed'=>'badge-amber', 'seeking'=>'badge-blue', default=>'badge-blue' } }}">
                                <span class="badge-dot"></span>{{ ucfirst(str_replace('_',' ',$employee->employment_status ?? 'unknown')) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="card">
            <div class="card-head"><span class="card-title">Location</span></div>
            <div class="card-body">
                <div class="field-grid">
                    <div class="field">
                        <div class="field-label">District</div>
                        <div class="field-value">{{ $employee->district ?? '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Sector</div>
                        <div class="field-value">{{ $employee->sector ?? '—' }}</div>
                    </div>
                    <div class="field" style="grid-column:1/-1;">
                        <div class="field-label">Address</div>
                        <div class="field-value">{{ $employee->address ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Professional --}}
        <div class="card">
            <div class="card-head"><span class="card-title">Professional Details</span></div>
            <div class="card-body">
                @if($employee->bio)
                    <div class="field" style="margin-bottom:16px;">
                        <div class="field-label">Bio</div>
                        <p class="bio-text">{{ $employee->bio }}</p>
                    </div>
                    <div class="divider"></div>
                @endif
                <div class="field">
                    <div class="field-label">Skills</div>
                    <div style="margin-top:6px;">
                        @forelse($employee->skills as $skill)
                            <div class="skill-tag" style="display:inline-flex;">
                                {{ $skill->name }}
                                <div class="proficiency-pip">
                                    @php $lvl = match($skill->pivot->proficiency ?? 'beginner') { 'beginner'=>1, 'intermediate'=>2, 'advanced'=>3, default=>1 }; @endphp
                                    @for($p=1; $p<=3; $p++) <div class="pip {{ $p<=$lvl ? 'on' : '' }}"></div> @endfor
                                </div>
                            </div>
                        @empty
                            <span class="field-value empty">No skills added. <a href="{{ route('employee.profile.edit') }}" style="color:var(--accent);">Add skills</a></span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div style="font-size:12px; color:var(--muted); text-align:right;">
            Profile last updated {{ $employee->updated_at->diffForHumans() }}
        </div>
    </main>
</div>
@endsection