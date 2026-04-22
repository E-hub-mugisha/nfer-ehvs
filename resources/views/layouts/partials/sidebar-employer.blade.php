<div class="nav-section-label">Overview</div>
<a href="{{ route('employer.dashboard') }}" class="nav-link-item {{ request()->routeIs('employer.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="nav-section-label">Employment</div>
<a href="{{ route('employer.records.index') }}" class="nav-link-item {{ request()->routeIs('employer.records.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-person"></i> Employment Records
</a>
<a href="{{ route('employer.records.create') }}" class="nav-link-item {{ request()->routeIs('employer.records.create') ? 'active' : '' }}">
    <i class="bi bi-plus-circle"></i> Add Record
</a>

<div class="nav-section-label">Verification Portal</div>
<a href="{{ route('employer.search.index') }}" class="nav-link-item {{ request()->routeIs('employer.search.*') ? 'active' : '' }}">
    <i class="bi bi-search-heart"></i> Search Employees
</a>

<div class="nav-section-label">Account</div>
<a href="{{ route('employer.profile.show') }}" class="nav-link-item {{ request()->routeIs('employer.profile.*') ? 'active' : '' }}">
    <i class="bi bi-building-check"></i> Company Profile
</a>