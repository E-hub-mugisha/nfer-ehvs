<div class="nav-section-label">Overview</div>
<a href="{{ route('employee.dashboard') }}" class="nav-link-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<div class="nav-section-label">My Records</div>
<a href="{{ route('employee.history.index') }}" class="nav-link-item {{ request()->routeIs('employee.history.*') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i> Employment History
</a>
<a href="{{ route('employee.dispute.index') }}" class="nav-link-item {{ request()->routeIs('employee.dispute.*') ? 'active' : '' }}">
    <i class="bi bi-shield-exclamation"></i> My Disputes
</a>

<div class="nav-section-label">Account</div>
<a href="{{ route('employee.profile.show') }}" class="nav-link-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-badge"></i> My Profile
</a>