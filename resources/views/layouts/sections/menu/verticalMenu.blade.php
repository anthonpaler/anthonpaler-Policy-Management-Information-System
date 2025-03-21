<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo">
      <div class="logo-con">
        <div class="d-flex align-items-center gap-3">
          <div class="logo">
            <img src="{{asset('assets/img/icons/brands/slsu_logo.png')}}" alt="">
          </div>
          <div>
            <a href="{{ route(getUserRole().'.dashboard') }}" class="m-0">PolMIS</a>
          </div>
        </div>
      </div>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
   @foreach ($menuData->menu as $menu)

    {{-- adding active and open class if child is active --}}

    {{-- menu headers --}}
    @if (isset($menu->menuHeader))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
    </li>

    @else

    {{-- active menu method --}}
    @php
    $activeClass = null;
    $currentRouteName = Route::currentRouteName();

    if (isset($menu->slug) && is_array($menu->slug)) {
        foreach ($menu->slug as $slug) {
            if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                $activeClass = 'active open';
                break; // Stop checking if a match is found
            }
        }
    } elseif (isset($menu->slug) && is_string($menu->slug)) {
        if (str_contains($currentRouteName, $menu->slug) && strpos($currentRouteName, $menu->slug) === 0) {
            $activeClass = 'active open';
        }
    }
    @endphp


    {{-- main menu --}}
    <li class="menu-item {{$activeClass}}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
        @isset($menu->badge)
          <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
        @endisset
      </a>

      {{-- submenu --}}
      @isset($menu->submenu)
      @include('layouts.sections.menu.submenu',['menu' => $menu->submenu])
      @endisset
    </li>
    @endif
    @endforeach
  </ul>

</aside>
