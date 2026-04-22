@extends('layouts.app')

@section('title', 'Edit Profile')

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

    .main { flex: 1; padding: 28px 32px; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .page-title { font-size: 20px; font-weight: 600; color: var(--ink); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

    .btn { padding: 9px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 500; font-family: inherit; cursor: pointer; text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 7px; transition: opacity .15s, transform .1s; white-space: nowrap; }
    .btn:hover { opacity: .85; transform: translateY(-1px); }
    .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--slate); }
    .btn-sm { padding: 6px 12px; font-size: 12px; }

    /* ── Form sections ── */
    .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 20px; }
    .card-head { padding: 14px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
    .card-head-icon { width: 30px; height: 30px; border-radius: 6px; background: var(--accent-lt); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .card-title { font-size: 13.5px; font-weight: 600; color: var(--ink); }
    .card-body { padding: 20px; }
    .card-sub { font-size: 12px; color: var(--muted); margin-left: auto; }

    /* ── Form fields ── */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
    @media(max-width:700px) { .form-grid, .form-grid-3 { grid-template-columns: 1fr; } }
    .field { display: flex; flex-direction: column; gap: 5px; }
    .field.full { grid-column: 1/-1; }
    .field-label { font-size: 12px; font-weight: 600; color: var(--slate); }
    .field-label .req { color: var(--red); margin-left: 2px; }
    .field-hint { font-size: 11.5px; color: var(--muted); }
    .field-error { font-size: 11.5px; color: var(--red); }
    .input {
        width: 100%; padding: 9px 12px;
        border: 1px solid var(--border); border-radius: var(--radius);
        font-family: inherit; font-size: 13.5px; color: var(--ink);
        background: var(--card); outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(26,86,219,.1); }
    .input.error { border-color: var(--red); }
    .input:disabled { background: var(--surface); color: var(--muted); cursor: not-allowed; }
    select.input { cursor: pointer; }
    textarea.input { resize: vertical; min-height: 100px; }

    /* ── Photo upload ── */
    .photo-section { display: flex; align-items: flex-start; gap: 24px; }
    .avatar-upload {
        width: 90px; height: 90px; border-radius: 50%;
        border: 3px dashed var(--border);
        background: var(--surface);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        cursor: pointer; position: relative; overflow: hidden; flex-shrink: 0;
        transition: border-color .15s;
    }
    .avatar-upload:hover { border-color: var(--accent); }
    .avatar-upload img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .avatar-upload input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .avatar-upload-label { font-size: 11px; color: var(--muted); text-align: center; line-height: 1.3; pointer-events: none; z-index: 1; }

    /* ── Skills editor ── */
    .skills-editor { display: flex; flex-wrap: wrap; gap: 8px; min-height: 48px; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
    .skill-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 10px; border-radius: 4px;
        background: var(--accent-lt); border: 1px solid #bbd3f8;
        font-size: 12.5px; color: var(--accent);
    }
    .skill-chip button { background: none; border: none; cursor: pointer; color: var(--accent); padding: 0; display: flex; font-size: 14px; line-height: 1; }
    .skill-chip button:hover { color: var(--red); }
    .skill-add { display: flex; gap: 8px; margin-top: 10px; }
    .skill-add .input { flex: 1; }

    /* ── Alert ── */
    .alert { display: flex; gap: 10px; padding: 12px 16px; border-radius: var(--radius); font-size: 13px; margin-bottom: 20px; }
    .alert-success { background: var(--green-lt); border: 1px solid #a3d9be; color: var(--green); }
    .alert-error   { background: var(--red-lt);   border: 1px solid #f4b8c9; color: var(--red);   }

    /* ── Sticky footer ── */
    .form-footer {
        position: sticky; bottom: 0;
        background: var(--card); border-top: 1px solid var(--border);
        padding: 14px 32px; display: flex; align-items: center; justify-content: space-between;
        margin: 0 -32px -28px;
    }
    .form-footer-left { font-size: 12.5px; color: var(--muted); }

    .divider { height: 1px; background: var(--border); margin: 18px 0; }
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
            <a href="{{ route('employee.disputes.index') }}" class="nav-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Disputes
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-label">Account</div>
            <a href="{{ route('employee.profile.edit') }}" class="nav-item active">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Profile
            </a>
        </div>
    </nav>

    <main class="main">
        <div class="page-header">
            <div>
                <div class="page-title">Edit Profile</div>
                <div class="page-sub">Update your personal and professional information</div>
            </div>
            <a href="{{ route('employee.profile.show') }}" class="btn btn-outline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Profile
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>Please fix the following errors before saving.</div>
        </div>
        @endif

        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Photo --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <span class="card-title">Profile Photo</span>
                </div>
                <div class="card-body">
                    <div class="photo-section">
                        <label class="avatar-upload" id="photoPreviewWrapper">
                            @if($employee->profile_photo)
                                <img id="photoPreview" src="{{ asset('storage/'.$employee->profile_photo) }}" alt="">
                            @else
                                <div class="avatar-upload-label" id="photoLabel">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 4px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    Upload Photo
                                </div>
                            @endif
                            <input type="file" name="profile_photo" accept="image/*" id="photoInput">
                        </label>
                        <div>
                            <div style="font-size:13.5px; font-weight:500; color:var(--ink); margin-bottom:4px;">Profile Photo</div>
                            <div style="font-size:12.5px; color:var(--muted); line-height:1.6; margin-bottom:12px;">Upload a clear, professional photo. Accepted: JPG, PNG, GIF. Max size: 2 MB.</div>
                            @if($employee->profile_photo)
                            <label class="btn btn-outline btn-sm" for="photoInput" style="cursor:pointer;">Change Photo</label>
                            @else
                            <label class="btn btn-outline btn-sm" for="photoInput" style="cursor:pointer;">Choose File</label>
                            @endif
                        </div>
                    </div>
                    @error('profile_photo') <div class="field-error" style="margin-top:8px;">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Personal Information --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    </div>
                    <span class="card-title">Personal Information</span>
                    <span class="card-sub">National ID cannot be changed after registration</span>
                </div>
                <div class="card-body">
                    <div class="form-grid-3" style="margin-bottom:16px;">
                        <div class="field">
                            <label class="field-label">First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" class="input @error('first_name') error @enderror" required>
                            @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}" class="input">
                        </div>
                        <div class="field">
                            <label class="field-label">Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="input @error('last_name') error @enderror" required>
                            @error('last_name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">National ID</label>
                            <input type="text" value="{{ $employee->national_id }}" class="input" disabled>
                            <span class="field-hint">Contact administration to dispute incorrect ID.</span>
                        </div>
                        <div class="field">
                            <label class="field-label">Date of Birth <span class="req">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}" class="input @error('date_of_birth') error @enderror" required>
                            @error('date_of_birth') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Gender</label>
                            <select name="gender" class="input">
                                <option value="">Select gender</option>
                                @foreach(['male'=>'Male','female'=>'Female','other'=>'Other','prefer_not_to_say'=>'Prefer not to say'] as $val=>$label)
                                    <option value="{{ $val }}" {{ old('gender', $employee->gender) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $employee->nationality) }}" class="input" placeholder="e.g. Rwandan">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span class="card-title">Location</span>
                </div>
                <div class="card-body">
                    <div class="form-grid" style="margin-bottom:16px;">
                        <div class="field">
                            <label class="field-label">District</label>
                            <input type="text" name="district" value="{{ old('district', $employee->district) }}" class="input" placeholder="e.g. Kigali">
                        </div>
                        <div class="field">
                            <label class="field-label">Sector</label>
                            <input type="text" name="sector" value="{{ old('sector', $employee->sector) }}" class="input" placeholder="e.g. Nyarugenge">
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Full Address</label>
                        <input type="text" name="address" value="{{ old('address', $employee->address) }}" class="input" placeholder="Street, building, apartment…">
                    </div>
                </div>
            </div>

            {{-- Professional --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><path d="M8 7V5a2 2 0 0 0-4 0v2"/></svg>
                    </div>
                    <span class="card-title">Professional Details</span>
                </div>
                <div class="card-body">
                    <div class="form-grid" style="margin-bottom:16px;">
                        <div class="field">
                            <label class="field-label">Current Job Title</label>
                            <input type="text" name="current_title" value="{{ old('current_title', $employee->current_title) }}" class="input" placeholder="e.g. Senior Software Engineer">
                        </div>
                        <div class="field">
                            <label class="field-label">Employment Status</label>
                            <select name="employment_status" class="input">
                                <option value="">Select status</option>
                                @foreach(['employed'=>'Employed','unemployed'=>'Unemployed','seeking'=>'Actively Seeking'] as $val=>$label)
                                    <option value="{{ $val }}" {{ old('employment_status', $employee->employment_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full">
                            <label class="field-label">LinkedIn URL</label>
                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $employee->linkedin_url) }}" class="input" placeholder="https://linkedin.com/in/yourname">
                            @error('linkedin_url') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field full">
                            <label class="field-label">Professional Bio</label>
                            <textarea name="bio" class="input" placeholder="Brief professional summary visible to employers…">{{ old('bio', $employee->bio) }}</textarea>
                            <span class="field-hint">Max 500 characters.</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="field">
                        <label class="field-label">Skills</label>
                        <span class="field-hint" style="margin-bottom:8px; display:block;">Add skills visible on your public profile.</span>

                        {{-- Hidden inputs for skill ids --}}
                        <div id="skillInputsContainer">
                            @foreach($employee->skills as $skill)
                                <input type="hidden" name="skills[]" value="{{ $skill->id }}">
                            @endforeach
                        </div>

                        <div class="skills-editor" id="skillsEditor">
                            @foreach($employee->skills as $skill)
                            <div class="skill-chip" data-id="{{ $skill->id }}">
                                {{ $skill->name }}
                                <button type="button" onclick="removeSkill(this, {{ $skill->id }})" title="Remove">&times;</button>
                            </div>
                            @endforeach
                        </div>

                        <div class="skill-add" style="margin-top:10px;">
                            <input type="text" id="skillSearch" class="input" placeholder="Search or add a skill…" autocomplete="off">
                            <button type="button" class="btn btn-outline btn-sm" onclick="addCustomSkill()">Add</button>
                        </div>

                        {{-- Skill suggestions --}}
                        <div id="skillSuggestions" style="display:none; border:1px solid var(--border); border-top:none; border-radius:0 0 var(--radius) var(--radius); background:var(--card); max-height:180px; overflow-y:auto; margin-top:-1px;"></div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <span class="form-footer-left">All changes are logged and may be reviewed by NFER-EHVS administration.</span>
                <div style="display:flex; gap:10px;">
                    <a href="{{ route('employee.profile.show') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

@push('scripts')
<script>
// ── Photo preview ──
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        let img = document.getElementById('photoPreview');
        if (!img) {
            img = document.createElement('img');
            img.id = 'photoPreview';
            document.getElementById('photoPreviewWrapper').appendChild(img);
            document.getElementById('photoLabel')?.remove();
        }
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
});

// ── Skill management ──
const allSkills = @json($availableSkills ?? []);  // expects [{id, name}]
const addedIds = new Set({{ json_encode($employee->skills->pluck('id')->toArray()) }});

function removeSkill(btn, id) {
    btn.closest('.skill-chip').remove();
    addedIds.delete(id);
    document.querySelectorAll(`#skillInputsContainer input[value="${id}"]`).forEach(el => el.remove());
}

function addSkill(id, name) {
    if (addedIds.has(id)) return;
    addedIds.add(id);
    // chip
    const chip = document.createElement('div');
    chip.className = 'skill-chip'; chip.dataset.id = id;
    chip.innerHTML = `${name} <button type="button" onclick="removeSkill(this, ${id})" title="Remove">&times;</button>`;
    document.getElementById('skillsEditor').appendChild(chip);
    // hidden input
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'skills[]'; inp.value = id;
    document.getElementById('skillInputsContainer').appendChild(inp);
    hideSuggestions();
    document.getElementById('skillSearch').value = '';
}

function addCustomSkill() {
    const val = document.getElementById('skillSearch').value.trim();
    if (!val) return;
    // Check if it matches existing
    const match = allSkills.find(s => s.name.toLowerCase() === val.toLowerCase());
    if (match) { addSkill(match.id, match.name); return; }
    // Otherwise treat as temp (no id, just name — backend should handle creation)
    if (addedIds.has('custom:'+val)) return;
    addedIds.add('custom:'+val);
    const chip = document.createElement('div');
    chip.className = 'skill-chip'; chip.dataset.id = 'custom:'+val;
    chip.innerHTML = `${val} <span style="font-size:10px; opacity:.6">new</span> <button type="button" onclick="removeSkill(this, 'custom:${val}')" title="Remove">&times;</button>`;
    document.getElementById('skillsEditor').appendChild(chip);
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'new_skills[]'; inp.value = val;
    document.getElementById('skillInputsContainer').appendChild(inp);
    document.getElementById('skillSearch').value = '';
    hideSuggestions();
}

function hideSuggestions() {
    const s = document.getElementById('skillSuggestions');
    s.style.display = 'none'; s.innerHTML = '';
}

document.getElementById('skillSearch').addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    const sug = document.getElementById('skillSuggestions');
    if (!q || !allSkills.length) { hideSuggestions(); return; }
    const matches = allSkills.filter(s => s.name.toLowerCase().includes(q) && !addedIds.has(s.id)).slice(0,8);
    if (!matches.length) { hideSuggestions(); return; }
    sug.style.display = 'block';
    sug.innerHTML = matches.map(s =>
        `<div onclick="addSkill(${s.id}, '${s.name}')" style="padding:9px 12px; font-size:13px; cursor:pointer; border-bottom:1px solid var(--border); transition:background .1s;" onmouseenter="this.style.background='var(--surface)'" onmouseleave="this.style.background=''">${s.name}</div>`
    ).join('');
});

document.addEventListener('click', e => {
    if (!e.target.closest('#skillSearch') && !e.target.closest('#skillSuggestions')) hideSuggestions();
});
</script>
@endpush
@endsection