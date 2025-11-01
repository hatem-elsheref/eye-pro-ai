<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="navbar-brand">
        <img src="{{ asset('logo.jpeg') }}" alt="Eye Pro" class="navbar-logo">
        <span>Eye Pro</span>
    </a>
    
    <div class="navbar-menu">
        <a href="{{ route('dashboard') }}" class="navbar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('matches.index') }}" class="navbar-link {{ request()->routeIs('matches.*') ? 'active' : '' }}">
            <i class="fas fa-video"></i>
            <span>Matches</span>
        </a>
        
        <div class="user-menu">
            <button class="user-menu-btn" onclick="toggleUserMenu()">
                <i class="fas fa-user-circle"></i>
            </button>
            
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <div class="user-menu-header">
                    {{ Auth::user()->email ?? 'x@app.com' }}
                </div>
                <a href="{{ route('profile') }}" class="user-menu-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="{{ route('support') }}" class="user-menu-item">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="user-menu-item danger" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu.contains(event.target)) {
        document.getElementById('userMenuDropdown').classList.remove('show');
    }
});
</script>

