<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Apka Budget</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Manage Users -->
    <li class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users') }}">
            <i class="fas fa-users"></i>
            <span>Manage Users</span>
        </a>
    </li>

    <!-- Manage Providers -->
    <li class="nav-item {{ request()->routeIs('admin.providers') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.providers') }}">
            <i class="fas fa-user-tie"></i>
            <span>Manage Providers</span>
        </a>
    </li>

    <!-- Zones -->
    <li class="nav-item {{ request()->routeIs('admin.zones') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.zones') }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>Zones</span>
        </a>
    </li>

    <!-- Bookings -->
    <li class="nav-item {{ request()->routeIs('admin.all_bookings') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.all_bookings') }}">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
    </li>
    <!-- Manage Categories -->
    <li class="nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.categories') }}">
            <i class="fas fa-tags"></i>
            <span>Manage Services</span>
        </a>
    </li>

    <!-- Transactions -->
    <li class="nav-item {{ request()->routeIs('admin.transaction') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.transaction') }}">
            <i class="fas fa-money-check-alt"></i>
            <span>Transactions</span>
        </a>
    </li>
    
        <!-- Manage GeoZone -->
    <li class="nav-item {{ request()->routeIs('admin.countries') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.countries') }}">
            <i class="fas fa-globe"></i>
            <span>Manage GeoZone</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
