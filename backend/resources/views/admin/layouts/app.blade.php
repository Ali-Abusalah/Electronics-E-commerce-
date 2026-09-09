<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Electronics Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: linear-gradient(180deg, #5b21b6 0%, #4f46e5 100%); color: white; flex-shrink: 0; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.15); }
        .sidebar-header h2 { font-size: 18px; font-weight: 700; }
        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255,255,255,0.85); text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: white; border-left-color: white; }
        .sidebar-nav a span { margin-left: 12px; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: white; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .topbar h1 { font-size: 20px; font-weight: 600; color: #111827; }
        .user-menu { display: flex; align-items: center; gap: 16px; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #5b21b6, #4f46e5); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; }
        .user-name { font-weight: 500; font-size: 14px; color: #111827; }
        .logout-btn { padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .logout-btn:hover { background: #dc2626; }
        .content { padding: 32px; flex: 1; }
        .flash-message { padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; font-size: 14px; }
        .flash-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .flash-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .card-header h2 { font-size: 18px; font-weight: 600; color: #111827; }
        .card-body { padding: 24px; }
        .btn { display: inline-flex; align-items: center; padding: 9px 18px; border-radius: 7px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; gap: 6px; }
        .btn-primary { background: linear-gradient(135deg, #5b21b6, #4f46e5); color: white; }
        .btn-primary:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .btn-secondary:hover { background: #e5e7eb; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f9fafb; padding: 14px 16px; text-align: left; font-weight: 600; font-size: 13px; color: #374151; border-bottom: 2px solid #e5e7eb; text-transform: uppercase; letter-spacing: 0.025em; }
        table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #1f2937; vertical-align: middle; }
        table tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #e5e7eb; color: #4b5563; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-processing { background: #dbeafe; color: #1e40af; }
        .badge-shipped { background: #e0e7ff; color: #3730a3; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-stock-low { background: #fee2e2; color: #991b1b; }
        .badge-stock-ok { background: #d1fae5; color: #065f46; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 7px; font-weight: 500; font-size: 14px; color: #111827; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; transition: all 0.2s; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        textarea.form-control { min-height: 100px; resize: vertical; }
        select.form-control { background: white; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .form-actions { display: flex; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .search-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .search-bar .form-control { flex: 1; min-width: 200px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-left: 4px solid #4f46e5; }
        .stat-card h3 { font-size: 13px; font-weight: 500; color: #6b7280; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .stat-value { font-size: 32px; font-weight: 700; color: #111827; }
        .stat-card.revenue { border-left-color: #10b981; }
        .stat-card.revenue .stat-value { color: #10b981; }
        .stat-card.orders { border-left-color: #3b82f6; }
        .stat-card.orders .stat-value { color: #3b82f6; }
        .stat-card.categories { border-left-color: #f59e0b; }
        .stat-card.categories .stat-value { color: #f59e0b; }
        .stat-card.outofstock { border-left-color: #ef4444; }
        .stat-card.outofstock .stat-value { color: #ef4444; }
        .pagination { display: flex; justify-content: center; gap: 4px; margin-top: 24px; list-style: none; padding: 0; }
        .pagination li a, .pagination li span { display: inline-block; padding: 8px 14px; border: 1px solid #d1d5db; border-radius: 6px; text-decoration: none; color: #374151; font-size: 14px; background: white; }
        .pagination li.active span { background: linear-gradient(135deg, #5b21b6, #4f46e5); color: white; border-color: #4f46e5; }
        .pagination li a:hover { background: #f3f4f6; }
        .order-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .detail-box { background: #f9fafb; padding: 20px; border-radius: 8px; }
        .detail-box h4 { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 14px; }
        .detail-box p { font-size: 14px; color: #4b5563; margin-bottom: 8px; }
        .detail-box p strong { color: #111827; }
        .thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #f3f4f6; }
        .action-buttons { display: flex; gap: 6px; }
        .inline-form { display: inline; }
        .required::after { content: ' *'; color: #ef4444; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .order-details-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Electronics Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Brands</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Orders</span>
                </a>
            </nav>
        </aside>

        <div class="main-content">
            <div class="topbar">
                <h1>@yield('title', 'Dashboard')</h1>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                        <span class="user-name">{{ Auth::user()->name ?? 'Admin User' }}</span>
                    </div>
                    <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="flash-message flash-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="flash-message flash-error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="flash-message flash-error">
                        Please fix the following errors:
                        <ul style="margin-top: 8px; margin-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
