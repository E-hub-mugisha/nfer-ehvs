<div class="nav-section-label">Overview</div>
<a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="nav-section-label">Registry</div>
<a href="{{ route('admin.employees.index') }}" class="nav-link-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
    <i class="bi bi-people"></i> Employees
</a>
<a href="{{ route('admin.employers.index') }}" class="nav-link-item {{ request()->routeIs('admin.employers.*') ? 'active' : '' }}">
    <i class="bi bi-building"></i> Employers
</a>

<div class="nav-section-label">Management</div>
<a href="{{ route('admin.disputes.index') }}" class="nav-link-item {{ request()->routeIs('admin.disputes.*') ? 'active' : '' }}">
    <i class="bi bi-shield-exclamation"></i> Disputes
    @php $open = \App\Models\Dispute::where('status','open')->count() @endphp
    @if($open > 0)<span class="badge bg-danger ms-auto rounded-pill">{{ $open }}</span>@endif
</a>
<a href="{{ route('admin.reports.index') }}" class="nav-link-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-line"></i> Reports
</a>
<a href="{{ route('admin.skills.index') }}" class="nav-link-item {{ request()->routeIs('admin.skills.*') ? 'active' : '' }}">
    <i class="bi bi-tags"></i> Skills
</a>
<a href="{{ route('admin.users.index') }}" class="nav-link-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-person-gear"></i> Users
</a>