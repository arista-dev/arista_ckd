<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CKD Inspection System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: #1a2634;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transition: all .3s ease;
        }

        .sidebar.collapsed {
            margin-left: -240px;
        }

        .main-content {
            margin-left: 240px;
            min-height: 100vh;
            transition: all .3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .sidebar .brand {
            padding: 20px 16px;
            border-bottom: 1px solid #2d3f50;
        }

        .sidebar .brand h5 {
            color: #fff;
            margin: 0;
            font-size: 15px;
            font-weight: 700;
        }

        .sidebar .brand small {
            color: #8aacbf;
            font-size: 11px;
        }

        .sidebar .nav-label {
            color: #556e82;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 16px 24px 4px;
        }

        .sidebar .nav-link {
            color: #9ab3c7;
            padding: 9px 16px;
            margin: 2px 8px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: .2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #2a3f54;
            color: #fff;
        }

        .sidebar-toggle {
            border: none;
            background: transparent;
            font-size: 22px;
            color: #495057;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -240px;
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .content-area {
            padding: 24px;
        }

        /* Status badges */
        .badge-OPEN {
            background: #17a2b8;
        }

        .badge-WAITING_APPROVAL {
            background: #ffc107;
            color: #000;
        }

        .badge-CLOSED {
            background: #28a745;
        }

        .badge-RECEIVED {
            background: #6c757d;
        }

        .badge-INSPECTION_OPEN {
            background: #007bff;
        }

        .badge-OK {
            background: #28a745;
        }

        .badge-SHORT {
            background: #ffc107;
            color: #000;
        }

        .badge-DAMAGE {
            background: #dc3545;
        }
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('mainContent');
        const btn = document.getElementById('sidebarToggle');

        // Restore previous state
        if (localStorage.getItem('sidebar') === 'collapsed') {
            sidebar.classList.add('collapsed');
            main.classList.add('expanded');
        }

        btn.addEventListener('click', function() {

            // Mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                return;
            }

            // Desktop
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');

            localStorage.setItem(
                'sidebar',
                sidebar.classList.contains('collapsed') ?
                'collapsed' :
                'expanded'
            );
        });
    });
</script>

<body>

    {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h5><i class="bi bi-box-seam me-2"></i>CKD Inspection</h5>
            <small>ARISTA System</small>
        </div>

        <nav class="py-2">
            <div class="nav-label">Menu</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            @if (session('user.role') === 'admin')
                <div class="nav-label">Master Data</div>

                <a href="{{ route('master.index') }}"
                    class="nav-link {{ request()->routeIs('master.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i> Master Model
                </a>

                <div class="nav-label">Operasional</div>

                <a href="{{ route('receiving.index') }}"
                    class="nav-link {{ request()->routeIs('receiving.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Receiving
                </a>
            @endif

            @if (in_array(session('user.role'), ['admin', 'inspector']))
                @if (session('user.role') === 'inspector')
                    <div class="nav-label">Operasional</div>
                @endif
                <a href="{{ route('inspection.index') }}"
                    class="nav-link {{ request()->routeIs('inspection.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Inspection
                </a>
            @endif

            @if (in_array(session('user.role'), ['admin', 'supervisor']))
                <div class="nav-label">Approval</div>
                <a href="{{ route('approval.index') }}"
                    class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check"></i> Approval
                </a>
            @endif

            @if (in_array(session('user.role'), ['admin', 'supervisor']))
                <div class="nav-label">Laporan</div>
                <a href="{{ route('report.index') }}"
                    class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Report
                </a>
            @endif
        </nav>
    </div>

    {{-- ─── Main Content ────────────────────────────────────────────────────── --}}
    <div class="main-content" id="mainContent">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>

                <span class="fw-semibold text-secondary" style="font-size:14px;">
                    @yield('page-title', 'Dashboard')
                </span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-secondary text-uppercase">{{ session('user.role') }}</span>
                <span class="text-muted" style="font-size:13px;">{{ session('user.name') }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any() && !$errors->has('login') && !$errors->has('delete') && !$errors->has('is_active'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Terdapat kesalahan input:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
