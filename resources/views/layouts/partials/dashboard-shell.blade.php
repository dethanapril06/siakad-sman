<!DOCTYPE html>
<html
    lang="id"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('template/assets') }}/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>@yield('title', 'Dashboard') | SIAKAD SMAN</title>
    <meta name="description" content="Sistem Informasi Akademik SMAN" />

    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    @stack('styles')

    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('template/assets/js/config.js') }}"></script>
</head>
<body>
    @php
        $avatarName = auth()->user()->name ?? 'Pengguna';
        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($avatarName) . '&background=696cff&color=fff&bold=true';
    @endphp

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route($dashboardRoute) }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <i class="bx bxs-school text-primary fs-3"></i>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">SIAKAD</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    @foreach ($menus as $menu)
                        @if (($menu['type'] ?? 'item') === 'header')
                            <li class="menu-header small text-uppercase">
                                <span class="menu-header-text">{{ $menu['label'] }}</span>
                            </li>
                        @elseif (($menu['type'] ?? 'item') === 'dropdown')
                            @php
                                $isOpen = collect($menu['children'] ?? [])
                                    ->contains(fn ($child) => request()->routeIs(...(array) ($child['active'] ?? $child['route'])));
                            @endphp
                            <li class="menu-item {{ $isOpen ? 'active open' : '' }}">
                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                    <i class="menu-icon tf-icons bx {{ $menu['icon'] }}"></i>
                                    <div>{{ $menu['label'] }}</div>
                                </a>

                                <ul class="menu-sub">
                                    @foreach ($menu['children'] ?? [] as $child)
                                        @php($isChildActive = request()->routeIs(...(array) ($child['active'] ?? $child['route'])))
                                        <li class="menu-item {{ $isChildActive ? 'active' : '' }}">
                                            <a href="{{ route($child['route']) }}" class="menu-link">
                                                <div>{{ $child['label'] }}</div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            @php($isActive = request()->routeIs(...(array) ($menu['active'] ?? $menu['route'])))
                            <li class="menu-item {{ $isActive ? 'active' : '' }}">
                                <a href="{{ route($menu['route']) }}" class="menu-link">
                                    <i class="menu-icon tf-icons bx {{ $menu['icon'] }}"></i>
                                    <div>{{ $menu['label'] }}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </aside>

            <div class="layout-page">
                <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar"
                >
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input
                                    type="text"
                                    class="form-control border-0 shadow-none"
                                    placeholder="Cari..."
                                    aria-label="Cari..."
                                />
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $avatarUrl }}" alt="avatar" class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ $avatarUrl }}" alt="avatar" class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                                                    <small class="text-muted">{{ $roleLabel }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route($profileRoute) }}">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Profil Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Logout</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                &copy; {{ date('Y') }} SIAKAD SMAN
                            </div>
                        </div>
                    </footer>

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('template/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
