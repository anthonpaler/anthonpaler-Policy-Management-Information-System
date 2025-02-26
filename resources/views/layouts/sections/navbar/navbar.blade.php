@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');

@endphp

<nav
    class="layout-navbar  navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
    </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    <div class="navbar-nav flex-column navbar-nav-text">
        <div class="nav-item d-flex align-items-center">
            <h6 class="m-0">Policy Management Information System - 
            {{ auth()->user()->campus->name ?? 'No Campus Assigned' }}

            </h6>
        </div>
        <div>
            <small id="date" class="date"></small>
            <small id="time" class="time"></small>
        </div>
    </div>

   
    <ul class="navbar-nav flex-row align-items-center ms-auto gap-3">
        <div class="d-flex align-items-center gap-3">
            <i class="bx bx-fullscreen" onclick="toggleFullscreen()" id="fullScreenBtn"></i>
        </div>
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="d-flex gap-3">
                <div class="flex-grow-1 user-info">
                    <span class="fw-medium d-flex justify-content-end">Rey Anthon 0. Paler</span>
                    <small class="text-muted d-flex justify-content-end">{{ config('user_roles.role.'.'1') }}</small>
                </div>
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                </div>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item" href="#">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="fw-semibold d-block">Rey Anthon 0. Paler</span>
                        <small class="text-muted">{{ config('user_roles.role.1') }}</small>
                    </div>
                </div>
            </a>
            </li>
            <li>
                <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" href="#">
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Log Out</span>
                </a>
                <form method="POST" id="logoutForm" action="">
                    @csrf
                </form>
            </li>
        </ul>
        </li>
        <!--/ User -->
    </ul>
    </div>
    <script>
        
        function toggleFullscreen() {
            let elem = document.documentElement;
            let btn = document.getElementById("fullScreenBtn");

            if (!document.fullscreenElement) {
                elem.requestFullscreen().then(() => {
                    btn.classList.remove("bx-fullscreen");
                    btn.classList.add("bx-exit-fullscreen"); 
                });
            } else {
                document.exitFullscreen().then(() => {
                    btn.classList.remove("bx-exit-fullscreen");
                    btn.classList.add("bx-fullscreen"); 
                });
            }
        }

         function updateDateTime() {
            const now = new Date();
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            
            document.getElementById('date').textContent = now.toLocaleDateString(undefined, dateOptions);
            document.getElementById('time').textContent = now.toLocaleTimeString(undefined, timeOptions);
        }
        
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</nav>
  <!-- / Navbar -->
