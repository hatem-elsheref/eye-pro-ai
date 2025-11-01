<aside class="sidebar">
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="{{ route('matches.index') }}" class="sidebar-link {{ request()->routeIs('matches.*') ? 'active' : '' }}">
                <i class="fas fa-video"></i>
                <span>Matches</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="{{ route('profile') }}" class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="{{ route('support') }}" class="sidebar-link {{ request()->routeIs('support') ? 'active' : '' }}">
                <i class="fas fa-life-ring"></i>
                <span>Support</span>
            </a>
        </li>
        
        @if(Auth::user()->is_admin ?? false)
        <li class="sidebar-item">
            <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Admin</span>
            </a>
        </li>
        @endif
    </ul>
</aside>



