<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ZuriTym Admin')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6C63FF; --primary-dark: #5a52d5; --secondary: #FF6584;
            --success: #2ecc71; --warning: #f39c12; --danger: #e74c3c; --info: #3498db;
            --dark: #1a1a2e; --sidebar-w: 260px; --header-h: 65px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f0f2f7; color:#333; }
        /* Sidebar */
        .sidebar { position:fixed; top:0; left:0; width:var(--sidebar-w); height:100vh; background:var(--dark); color:#fff; z-index:1000; display:flex; flex-direction:column; overflow-y:auto; }
        .sidebar-logo { padding:22px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid rgba(255,255,255,.1); }
        .sidebar-logo img { width:36px; height:36px; border-radius:8px; }
        .sidebar-logo span { font-size:1.3rem; font-weight:700; color:#fff; }
        .sidebar-logo small { display:block; font-size:.7rem; color:rgba(255,255,255,.5); }
        .sidebar-nav { flex:1; padding:15px 0; }
        .nav-section { padding:8px 20px 5px; font-size:.65rem; font-weight:600; color:rgba(255,255,255,.3); text-transform:uppercase; letter-spacing:1px; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:11px 20px; color:rgba(255,255,255,.7); text-decoration:none; transition:.2s; font-size:.875rem; cursor:pointer; border-left:3px solid transparent; }
        .nav-item:hover,.nav-item.active { color:#fff; background:rgba(108,99,255,.2); border-left-color:var(--primary); }
        .nav-item i { width:18px; text-align:center; font-size:.9rem; }
        .nav-badge { margin-left:auto; background:var(--danger); color:#fff; font-size:.65rem; padding:2px 7px; border-radius:10px; }
        /* Header */
        .header { position:fixed; top:0; left:var(--sidebar-w); right:0; height:var(--header-h); background:#fff; box-shadow:0 2px 10px rgba(0,0,0,.08); display:flex; align-items:center; justify-content:space-between; padding:0 25px; z-index:999; }
        .header-title { font-size:1.1rem; font-weight:600; color:var(--dark); }
        .header-right { display:flex; align-items:center; gap:15px; }
        .header-btn { background:none; border:none; cursor:pointer; padding:8px; border-radius:8px; color:#666; transition:.2s; }
        .header-btn:hover { background:#f0f2f7; color:var(--primary); }
        .user-avatar { width:36px; height:36px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:.85rem; cursor:pointer; }
        /* Main */
        .main { margin-left:var(--sidebar-w); margin-top:var(--header-h); padding:25px; min-height:calc(100vh - var(--header-h)); }
        /* Cards */
        .card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.06); padding:22px; margin-bottom:20px; }
        .card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
        .card-title { font-size:1rem; font-weight:600; color:var(--dark); }
        /* Stat Cards */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:18px; margin-bottom:24px; }
        .stat-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 12px rgba(0,0,0,.06); display:flex; align-items:center; gap:16px; }
        .stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff; flex-shrink:0; }
        .stat-info .value { font-size:1.6rem; font-weight:700; color:var(--dark); }
        .stat-info .label { font-size:.78rem; color:#888; margin-top:2px; }
        /* Table */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.875rem; }
        th { background:#f8f9ff; padding:11px 14px; text-align:left; font-weight:600; color:#555; font-size:.78rem; text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid #eee; }
        td { padding:12px 14px; border-bottom:1px solid #f0f0f0; color:#444; vertical-align:middle; }
        tr:hover td { background:#fafbff; }
        /* Badges */
        .badge { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:600; display:inline-block; }
        .badge-success { background:#e8f8f0; color:var(--success); }
        .badge-danger  { background:#fde8e8; color:var(--danger); }
        .badge-warning { background:#fef3e0; color:var(--warning); }
        .badge-info    { background:#e3f2fd; color:var(--info); }
        .badge-purple  { background:#f0eeff; color:var(--primary); }
        /* Buttons */
        .btn { padding:8px 18px; border-radius:8px; font-size:.85rem; font-weight:500; cursor:pointer; border:none; transition:.2s; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:#fff; }
        .btn-danger  { background:var(--danger); color:#fff; }
        .btn-warning { background:var(--warning); color:#fff; }
        .btn-sm { padding:5px 12px; font-size:.78rem; }
        .btn-outline { background:transparent; border:1px solid #ddd; color:#555; }
        .btn-outline:hover { background:#f0f2f7; }
        /* Forms */
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:.85rem; font-weight:500; color:#555; margin-bottom:6px; }
        .form-control { width:100%; padding:9px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-size:.875rem; transition:.2s; outline:none; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(108,99,255,.12); }
        .form-select { width:100%; padding:9px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-size:.875rem; background:#fff; cursor:pointer; }
        .form-check { display:flex; align-items:center; gap:8px; cursor:pointer; font-size:.875rem; }
        .form-check input { width:16px; height:16px; cursor:pointer; accent-color:var(--primary); }
        /* Alerts */
        .alert { padding:12px 18px; border-radius:8px; margin-bottom:18px; font-size:.875rem; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#e8f8f0; color:#1a7a45; border-left:4px solid var(--success); }
        .alert-danger  { background:#fde8e8; color:#9b1c1c; border-left:4px solid var(--danger); }
        .alert-warning { background:#fef3e0; color:#7d4f00; border-left:4px solid var(--warning); }
        /* Pagination */
        .pagination { display:flex; gap:6px; margin-top:18px; }
        .pagination a, .pagination span { padding:6px 12px; border-radius:6px; font-size:.85rem; text-decoration:none; border:1px solid #e0e0e0; color:#555; }
        .pagination .active span { background:var(--primary); color:#fff; border-color:var(--primary); }
        /* Misc */
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
        .page-title { font-size:1.4rem; font-weight:700; color:var(--dark); }
        .page-subtitle { font-size:.85rem; color:#888; margin-top:2px; }
        .divider { height:1px; background:#eee; margin:16px 0; }
        .avatar-sm { width:34px; height:34px; border-radius:50%; object-fit:cover; background:var(--primary); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:600; }
        @media (max-width:768px) { .sidebar { transform:translateX(-100%); } .main { margin-left:0; } .stats-grid { grid-template-columns:1fr 1fr; } }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div style="width:36px;height:36px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;color:#fff;">Z</div>
        <div><span>ZuriTym</span><small>Admin Panel</small></div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="nav-section">Users</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Users
        </a>

        <div class="nav-section">Earnings</div>
        <a href="{{ route('admin.tasks.index') }}" class="nav-item {{ request()->routeIs('admin.tasks*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
        <a href="{{ route('admin.offerwalls.index') }}" class="nav-item {{ request()->routeIs('admin.offerwalls*') ? 'active' : '' }}">
            <i class="fas fa-store"></i> Offerwalls
        </a>
        <a href="{{ route('admin.spin-rewards.index') }}" class="nav-item {{ request()->routeIs('admin.spin-rewards*') ? 'active' : '' }}">
            <i class="fas fa-dharmachakra"></i> Spin Wheel
        </a>
        <a href="{{ route('admin.promo-codes.index') }}" class="nav-item {{ request()->routeIs('admin.promo-codes*') ? 'active' : '' }}">
            <i class="fas fa-tag"></i> Promo Codes
        </a>

        <div class="nav-section">Payments</div>
        <a href="{{ route('admin.withdrawals.index') }}" class="nav-item {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> Withdrawals
            @php $pending = \App\Models\Withdrawal::where('status','pending')->count(); @endphp
            @if($pending > 0)<span class="nav-badge">{{ $pending }}</span>@endif
        </a>

        <div class="nav-section">Manage</div>
        <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifications
        </a>
        <a href="{{ route('admin.reports.transactions') }}" class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="{{ route('admin.ad-networks.index') }}" class="nav-item {{ request()->routeIs('admin.ad-networks*') ? 'active' : '' }}">
            <i class="fas fa-ad"></i> Ad Networks
        </a>

        <div class="divider"></div>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:none;border:none;text-align:left;color:rgba(255,255,255,.6);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>
</aside>

<!-- Header -->
<header class="header">
    <div class="header-title">@yield('page-title', 'Dashboard')</div>
    <div class="header-right">
        <button class="header-btn"><i class="fas fa-bell"></i></button>
        <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
    </div>
</header>

<!-- Main -->
<main class="main">
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
