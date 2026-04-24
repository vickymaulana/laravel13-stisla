@auth
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <!-- Sidebar Toggle Button on the Left -->
    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
        <i class="fas fa-bars"></i>
    </a>

    <!-- Right Side Navbar Items -->
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a
                href="#"
                class="nav-link nav-link-lg"
                title="Toggle theme"
                x-data="{ dark: document.documentElement.getAttribute('data-bs-theme') === 'dark' }"
                @click.prevent="
                    dark = !dark;
                    const theme = dark ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-bs-theme', theme);
                    localStorage.setItem('theme', theme);
                "
            >
                <i class="fas" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
            </a>
        </li>
        <li class="dropdown">
            <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle me-1">
                <div class="d-sm-none d-lg-inline-block">
                    Hi, {{ substr(auth()->user()->name, 0, 10) }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-title">
                    Welcome, {{ substr(auth()->user()->name, 0, 10) }}
                </div>
                <a class="dropdown-item has-icon edit-profile" href="{{ route('profile.edit') }}">
                    <i class="fa fa-user"></i> Edit Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
@endauth
