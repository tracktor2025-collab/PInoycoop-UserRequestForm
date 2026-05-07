<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f7ff;
            min-height: 100vh;
        }
        * {
            margin: 0;
            padding: 0;
        }
        .navbar-logo {
            padding: 15px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            line-height: 1;
        }
        .navbar-logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 12px;
            padding: 6px 10px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
        }
        .navbar-logo-img {
            height: 46px;
            width: auto;
            object-fit: contain;
            display: block;
            filter: none;
        }
        .navbar-logo-text {
            display: inline-flex;
            flex-direction: column;
            line-height: 1.05;
        }
        .navbar-logo-text .title {
            font-weight: 800;
            letter-spacing: 0.02em;
        }
        .navbar-logo-text .subtitle {
            font-weight: 600;
            font-size: 0.75rem;
            opacity: 0.85;
        }
        .navbar-mainbg {
            background-color: #1f3d7c;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
        }
        #navbarSupportedContent {
            /* visible so Bootstrap dropdown menus are not clipped */
            overflow: visible;
            position: relative;
        }
        #navbarSupportedContent .navbar-nav {
            position: relative;
        }
        #navbarSupportedContent ul {
            padding: 0;
            margin: 0;
        }
        /* Only bar links — do not use "ul li a" or dropdown .dropdown-item inherits white text on white menu */
        #navbarSupportedContent > ul.navbar-nav > li > a.nav-link i {
            margin-right: 10px;
        }
        #navbarSupportedContent li {
            list-style-type: none;
            float: none;
            position: relative;
            z-index: 1;
        }
        #navbarSupportedContent > ul.navbar-nav > li > a.nav-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 15px;
            display: block;
            padding: 16px 16px;
            white-space: nowrap;
            transition-duration: 0.6s;
            transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
        }
        #navbarSupportedContent > ul.navbar-nav > li.active > a.nav-link {
            color: #1f3d7c;
            background-color: transparent;
            transition: all 0.7s;
        }
        #navbarSupportedContent .dropdown-menu {
            z-index: 1050;
            min-width: 14rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        #navbarSupportedContent .dropdown-item {
            color: #1f2937 !important;
            font-size: 0.95rem;
            padding: 0.55rem 1rem;
        }
        #navbarSupportedContent .dropdown-item:hover,
        #navbarSupportedContent .dropdown-item:focus {
            color: #111827 !important;
            background-color: #f3f4f6;
        }
        #navbarSupportedContent .dropdown-item i {
            margin-right: 0;
        }
        #navbarSupportedContent .dropdown-header {
            font-size: 0.7rem;
            letter-spacing: 0.06em;
        }
        .hori-selector {
            display: inline-block;
            position: absolute;
            height: calc(100% - 12px);
            top: 6px;
            left: 0;
            z-index: 0;
            transition-duration: 0.6s;
            transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
            background-color: #fff;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .hori-selector .right,
        .hori-selector .left {
            position: absolute;
            width: 25px;
            height: 25px;
            background-color: #fff;
            bottom: 10px;
        }
        .hori-selector .right {
            right: -25px;
        }
        .hori-selector .left {
            left: -25px;
        }
        .hori-selector .right:before,
        .hori-selector .left:before {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #1f3d7c;
        }
        .hori-selector .right:before {
            bottom: 0;
            right: -25px;
        }
        .hori-selector .left:before {
            bottom: 0;
            left: -25px;
        }
        .navbar-toggler {
            border: none;
            padding: 0.5rem 0.9rem;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .logout-btn-nav {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 15px;
            display: block;
            padding: 16px 16px;
            white-space: nowrap;
            transition-duration: 0.6s;
            transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
        }
        .dashboard-switch-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.38rem 0.75rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #fff;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.14);
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        .dashboard-switch-btn:hover {
            background: #fff;
            color: #1f3d7c;
            border-color: #fff;
        }
        .logout-btn-nav:hover {
            color: #fff;
        }
        .admin-wrap {
            max-width: 1140px;
            margin: 0 auto;
            padding: 1.1rem 1rem 1.4rem;
        }
        .nav-shell {
            width: 100%;
            padding: 0;
            background: #1f3d7c;
            border-bottom: none;
        }
        .nav-inner {
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
        }
        .dashboard-card {
            background: #fff;
            border: 1px solid #e8ecf8;
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(12, 24, 65, 0.05);
        }
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2a4a;
        }
        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .page-subtitle {
            color: #6f7b98;
            margin: 0;
        }
        .table > :not(caption) > * > * {
            border-color: #eef1fa;
        }
        .table-sort-link {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
        }
        .table-sort-link:hover {
            color: #1f3d7c;
            text-decoration: underline;
        }
        @media (max-width: 991px) {
            #navbarSupportedContent > ul.navbar-nav > li > a.nav-link,
            .logout-btn-nav {
                padding: 12px 30px;
            }
            .hori-selector {
                display: none;
            }
            .nav-shell {
                padding: 0;
            }
            .nav-inner {
                padding: 0;
            }
            .admin-wrap {
                padding: 1rem 0.6rem 1.2rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
@php
    $isSuperAdmin = isset($currentAdmin) && method_exists($currentAdmin, 'isSuperAdmin') && $currentAdmin->isSuperAdmin();
@endphp
<div class="nav-shell">
    <div class="nav-inner">
        <nav class="navbar navbar-expand-lg navbar-mainbg">
            <a class="navbar-brand navbar-logo" href="{{ $isSuperAdmin ? route('admin.super.dashboard') : route('admin.dashboard') }}">
                <span class="navbar-logo-badge">
                    <img
                        src="{{ asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}"
                        alt="MASS-SPECC"
                        class="navbar-logo-img"
                        decoding="async"
                        loading="lazy"
                    >
                </span>
                <span class="navbar-logo-text">
                    <span class="title">{{ $isSuperAdmin ? 'Access Request Super Admin' : 'Access Request Admin' }}</span>
                    <span class="subtitle">MASS-SPECC Cooperative</span>
                </span>
            </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <div class="hori-selector"><div class="left"></div><div class="right"></div></div>
                <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.super.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ $isSuperAdmin ? route('admin.super.dashboard') : route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
                </li>
                @if(! $isSuperAdmin)
                    <li class="nav-item {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.approvals') }}"><i class="bi bi-clipboard-check"></i>Approval Module</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.account.my') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.account.my') }}"><i class="bi bi-person-circle"></i>Profile</a>
                    </li>
                @endif
                <li class="nav-item {{ request()->routeIs('admin.pdf.archive') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.pdf.archive') }}"><i class="bi bi-file-earmark-pdf"></i>PDF Backup Module</a>
                </li>
                @if($isSuperAdmin)
                    @if(request()->routeIs('admin.dashboard'))
                        <li class="nav-item d-flex align-items-center me-lg-2 my-2 my-lg-0">
                            <a class="dashboard-switch-btn" href="{{ route('super.dashboard') }}">
                                <i class="bi bi-shield-lock"></i>
                                <span>Login as Super Admin</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item {{ request()->routeIs('admin.account.my') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.account.my') }}"><i class="bi bi-person-circle"></i>Profile</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.account.admins') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.account.admins') }}"><i class="bi bi-shield-lock"></i>Admin accounts</a>
                    </li>
                    <li class="nav-item dropdown {{ request()->routeIs('admin.system.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="adminSystemDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false"><i class="bi bi-gear-wide-connected"></i>System management</a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2" aria-labelledby="adminSystemDropdown">
                            <li><h6 class="dropdown-header text-uppercase small text-muted mb-1">System management</h6></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.system.index') }}">
                                    <i class="bi bi-grid text-secondary" style="width: 1.1rem;"></i>
                                    <span>Overview</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.system.audit') }}">
                                    <i class="bi bi-clock-history text-secondary" style="width: 1.1rem;"></i>
                                    <span>Audit Trail (History Log)</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="logout-btn-nav border-0 bg-transparent"><i class="bi bi-box-arrow-right"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
        </nav>
    </div>
</div>

<div class="admin-wrap">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function runNavSelectorAnimation() {
        var tabsNewAnim = $('#navbarSupportedContent > ul.navbar-nav');
        var activeItemNewAnim = tabsNewAnim.find('.active');

        if (!activeItemNewAnim.length) {
            return;
        }

        var activeHeight = activeItemNewAnim.outerHeight();
        var activeWidth = activeItemNewAnim.outerWidth();
        var itemPos = activeItemNewAnim.position();
        if (!itemPos) {
            return;
        }

        $(".hori-selector").css({
            "top": (itemPos.top + 6) + "px",
            "left": itemPos.left + "px",
            "height": Math.max(34, activeHeight - 12) + "px",
            "width": activeWidth + "px"
        });
    }

    $(document).ready(function () {
        setTimeout(runNavSelectorAnimation, 50);
    });

    $(window).on('resize', function () {
        setTimeout(runNavSelectorAnimation, 300);
    });

    $('#navbarSupportedContent').on('shown.bs.collapse', function () {
        runNavSelectorAnimation();
    });
</script>
@stack('scripts')
</body>
</html>
