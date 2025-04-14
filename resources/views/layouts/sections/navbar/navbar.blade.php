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
      <div class="">
          <i class="bx bx-fullscreen" onclick="toggleFullscreen()" id="fullScreenBtn"></i>
      </div>

      {{-- FOR MULTI ROLE USERS --}}
      @php
      $activeRoles = session('available_roles') ?? []; // Ensure it's always an array
      $currentRole = array_search(session('user_role'), [
              'Local Secretary' => 3,
              'University Secretary' => 4,
              'Board Secretary' => 5
          ]) ?: (count($activeRoles) > 0 ? $activeRoles[0] : '');@endphp

      @if(count($activeRoles) > 1)
          <div class="btn-group">
              <button type="button" class="btn btn-sm btn-primary">SWITCH AS</button>
              <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                  @foreach($activeRoles as $role)
                      <li>
                          <a class="dropdown-item switch-role" href="javascript:void(0);" data-role="{{ $role }}">
                              {{ $role }}
                          </a>
                      </li>
                  @endforeach
              </ul>
          </div>
      @endif


      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="d-flex gap-3">
                <div class="flex-grow-1 user-info">
                    <span class="fw-medium d-flex justify-content-end">{{ session('name') }}</span>
                    <small class="text-muted d-flex justify-content-end">{{ config('usersetting.role.'.session('user_role')) }}</small>
                </div>
                <div class="avatar avatar-online">
                    <img src="{{ session('profile_photo') && trim(session('profile_photo')) !== '' ? session('profile_photo') : asset('assets/img/avatars/default-avatar.jpg') }}"
     alt="Profile Photo"
     class="w-px-40 h-auto rounded-circle" />
                </div>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item" href="#">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                        <img src="{{ session('profile_photo') && trim(session('profile_photo')) !== '' ? session('profile_photo') : asset('assets/img/avatars/default-avatar.jpg') }}"
     alt="Profile Photo"
     class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="fw-semibold d-block">{{session('name') }}</span>
                        <small class="text-muted">{{ config('usersetting.role.'.session('user_role')) }}</small>
                    </div>
                </div>
            </a>
            </li>
            <li>
              <a class="dropdown-item" href="/logout">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">Log Out</span>
              </a>
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



    $(document).on('click', '.switch-role', function () {
        let selectedRole = $(this).data('role');

        $.ajax({
            url: "{{ route('switch.role') }}",
            type: 'POST',
            data: { role: selectedRole, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect; // 🔥 Refresh with new role
                }
            },
            error: function () {
                alert('Error switching role. Try again.');
            }
        });
    });

    </script>
</nav>
  <!-- / Navbar -->
