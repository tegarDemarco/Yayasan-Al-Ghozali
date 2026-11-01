<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi') – Yayasan Al-Ghozaly</title>
    <meta name="description" content="Sistem Informasi Manajemen Guru Yayasan Al-Ghozaly – SD, MTs, SMK">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary: #16a34a;
            --primary-dark: #15803d;
            --primary-light: #22c55e;
            --primary-50: #f0fdf4;
            --primary-100: #dcfce7;
            --primary-200: #bbf7d0;
            --secondary: #064e3b;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --sidebar-bg: #0f2419;
            --sidebar-hover: #1a3a2a;
            --sidebar-active: #16a34a;
            --sidebar-text: #9ca3af;
            --sidebar-text-active: #ffffff;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --red: #ef4444;
            --yellow: #f59e0b;
            --blue: #3b82f6;
            --purple: #8b5cf6;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --sidebar-width: 260px;
            --header-height: 64px;
            --transition: 0.2s ease;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { font-size: 15px; scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--body-bg);
            color: var(--text-primary);
            line-height: 1.5;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform var(--transition);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--sidebar-hover); border-radius: 2px; }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35);
        }

        .sidebar-brand-text { flex: 1; min-width: 0; }
        .sidebar-brand-name {
            font-size: 14px; font-weight: 700; color: #fff;
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            font-size: 11px; color: var(--sidebar-text);
            margin-top: 2px;
        }

        .sidebar-section {
            padding: 16px 12px 8px;
            font-size: 10px; font-weight: 600;
            color: rgba(255,255,255,0.25);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-nav { flex: 1; padding: 8px 12px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 13.5px;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .nav-item.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(22,163,74,0.3);
        }

        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            background: rgba(255,255,255,0.05);
        }

        .sidebar-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name {
            font-size: 13px; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 11px; color: var(--sidebar-text); margin-top: 1px;
        }

        /* ===== MAIN LAYOUT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 0 var(--border);
        }

        .topbar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 6px;
            border-radius: var(--radius-sm);
        }

        .topbar-title {
            flex: 1;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: var(--radius-sm);
            background: none;
            border: 1px solid var(--border);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary);
            transition: all var(--transition);
            position: relative;
        }

        .topbar-btn:hover { background: var(--primary-50); border-color: var(--primary-200); color: var(--primary); }
        .topbar-btn svg { width: 18px; height: 18px; }

        .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--red);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            background: none;
            transition: all var(--transition);
        }

        .topbar-profile:hover { background: var(--primary-50); border-color: var(--primary-200); }

        .topbar-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff;
        }

        .topbar-username { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .topbar-role { font-size: 11px; color: var(--text-muted); }

        /* ===== PAGE CONTENT ===== */
        .page-content { flex: 1; padding: 28px 28px 40px; }

        /* ===== BREADCRUMB ===== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb-sep { font-size: 10px; }
        .breadcrumb-current { color: var(--text-primary); font-weight: 500; }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-subtitle { font-size: 13px; color: var(--text-muted); }

        /* ===== STATS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow);
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: var(--accent-color, var(--primary));
        }

        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 22px; height: 22px; }

        .stat-info { flex: 1; }
        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .stat-change {
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
        }
        .stat-change.up { color: var(--primary); }
        .stat-change.down { color: var(--red); }

        /* Color variants */
        .accent-green { --accent-color: var(--primary); }
        .accent-blue { --accent-color: var(--blue); }
        .accent-yellow { --accent-color: var(--yellow); }
        .accent-red { --accent-color: var(--red); }
        .accent-purple { --accent-color: var(--purple); }

        .icon-green { background: var(--primary-100); color: var(--primary); }
        .icon-blue { background: #dbeafe; color: var(--blue); }
        .icon-yellow { background: var(--accent-light); color: var(--yellow); }
        .icon-red { background: #fee2e2; color: var(--red); }
        .icon-purple { background: #ede9fe; color: var(--purple); }

        /* ===== TABLES ===== */
        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        thead th {
            background: var(--primary-50);
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 2px solid var(--primary-200);
            white-space: nowrap;
        }

        tbody td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr:hover { background: var(--primary-50); }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-green { background: var(--primary-100); color: var(--primary-dark); }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-yellow { background: var(--accent-light); color: #92400e; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-gray { background: #f1f5f9; color: var(--text-secondary); }
        .badge-purple { background: #ede9fe; color: #6d28d9; }
        .badge-orange { background: #ffedd5; color: #9a3412; }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all var(--transition);
            white-space: nowrap;
        }

        .btn svg { width: 16px; height: 16px; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(22,163,74,0.25);
        }

        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(22,163,74,0.35); }

        .btn-secondary {
            background: #fff;
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover { background: var(--primary-50); border-color: var(--primary-200); color: var(--primary); }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .btn-danger:hover { background: #fecaca; }

        .btn-warning {
            background: var(--accent-light);
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .btn-sm { padding: 6px 12px; font-size: 12.5px; }
        .btn-icon { padding: 7px; }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-label span.req { color: var(--red); margin-left: 2px; }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            color: var(--text-primary);
            background: #fff;
            transition: all var(--transition);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
        }

        .form-control::placeholder { color: var(--text-muted); }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        textarea.form-control { resize: vertical; min-height: 90px; }

        .form-error { font-size: 12px; color: var(--red); margin-top: 4px; }

        .form-grid { display: grid; gap: 16px; }
        .form-grid-2 { grid-template-columns: 1fr 1fr; }
        .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }

        /* ===== ALERTS ===== */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            margin-bottom: 20px;
        }

        .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: var(--primary-100); color: var(--primary-dark); border: 1px solid var(--primary-200); }
        .alert-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-warning { background: var(--accent-light); color: #92400e; border: 1px solid #fde68a; }
        .alert-info { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-top: 1px solid var(--border);
            margin-top: 4px;
        }

        .pagination-info { font-size: 13px; color: var(--text-muted); }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .page-link {
            min-width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            transition: all var(--transition);
            padding: 0 8px;
        }

        .page-link:hover { background: var(--primary-50); border-color: var(--primary-200); color: var(--primary); }
        .page-link.active { background: var(--primary); border-color: var(--primary); color: #fff; }
        .page-link.disabled { opacity: 0.4; pointer-events: none; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state svg {
            width: 56px; height: 56px;
            color: #cbd5e1;
            margin: 0 auto 16px;
            display: block;
        }

        .empty-state h3 { font-size: 16px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; }
        .empty-state p { font-size: 13.5px; }

        /* ===== FILTERS ===== */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .filter-bar .form-control {
            width: auto;
            min-width: 160px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 320px;
        }

        .search-box svg {
            position: absolute;
            left: 12px; top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            color: var(--text-muted);
        }

        .search-box .form-control { padding-left: 38px; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header-left h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .page-header-left p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ===== CHART CONTAINERS ===== */
        .chart-container { position: relative; width: 100%; }

        /* ===== ACTIVITY FEED ===== */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child { border-bottom: none; }

        .activity-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .activity-dot svg { width: 14px; height: 14px; }
        .activity-dot.green { background: var(--primary-100); color: var(--primary); }
        .activity-dot.blue { background: #dbeafe; color: var(--blue); }
        .activity-dot.yellow { background: var(--accent-light); color: var(--yellow); }
        .activity-dot.red { background: #fee2e2; color: var(--red); }

        .activity-text { flex: 1; }
        .activity-text p { font-size: 13px; color: var(--text-primary); line-height: 1.4; }
        .activity-time { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ===== MODAL ===== */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--transition);
        }

        .modal-overlay.active { opacity: 1; pointer-events: all; }

        .modal {
            background: #fff;
            border-radius: var(--radius);
            padding: 24px;
            width: 100%;
            max-width: 540px;
            box-shadow: var(--shadow-lg);
            transform: scale(0.95);
            transition: transform var(--transition);
        }

        .modal-overlay.active .modal { transform: scale(1); }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .modal-title { font-size: 16px; font-weight: 700; }
        .modal-close { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px; border-radius: 6px; }
        .modal-close:hover { background: var(--border); }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }

        /* ===== STATUS INDICATORS ===== */
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-dot.green { background: var(--primary); }
        .status-dot.red { background: var(--red); }
        .status-dot.yellow { background: var(--yellow); }
        .status-dot.gray { background: var(--text-muted); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .topbar-toggle { display: flex; }
            .sidebar-overlay { display: block; }
            .form-grid-3 { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .page-content { padding: 20px 16px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
            .topbar { padding: 0 16px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .form-control, .search-box { max-width: 100%; min-width: 0; width: 100%; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 99;
        }

        .sidebar-overlay.open { display: block; }

        /* ===== JENJANG BADGE ===== */
        .jenjang-sd { background: #dbeafe; color: #1e40af; border-radius: 6px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; }
        .jenjang-mts { background: #fef9c3; color: #854d0e; border-radius: 6px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; }
        .jenjang-smk { background: #fce7f3; color: #9d174d; border-radius: 6px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; }

        /* ===== WELCOME BANNER ===== */
        .welcome-banner {
            background: linear-gradient(135deg, var(--secondary) 0%, #166534 40%, var(--primary) 100%);
            border-radius: var(--radius);
            padding: 24px 28px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 180px; height: 180px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -50px; right: 80px;
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .welcome-banner h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .welcome-banner p { font-size: 13.5px; opacity: 0.8; }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="22" height="22">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
            </svg>
        </div>
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">Yayasan Al-Ghozaly</div>
            <div class="sidebar-brand-sub">Sistem Informasi Guru</div>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" id="nav-dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            Dashboard
        </a>

        @auth
            @if(auth()->user()->isGuru() || auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
            <div class="sidebar-section">Akademik</div>

            <a href="{{ route('jadwal-mengajar.index') }}" class="nav-item {{ request()->routeIs('jadwal-mengajar.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Jadwal Mengajar
            </a>

            <a href="{{ route('presensi.index') }}" class="nav-item {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Presensi
            </a>

            <a href="{{ route('jurnal.index') }}" class="nav-item {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                Jurnal Mengajar
            </a>

            <a href="{{ route('rpp.index') }}" class="nav-item {{ request()->routeIs('rpp.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                RPP & Modul Ajar
            </a>

            <a href="{{ route('bahan-ajar.index') }}" class="nav-item {{ request()->routeIs('bahan-ajar.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                Upload Bahan Ajar
            </a>

            <a href="{{ route('penilaian.index') }}" class="nav-item {{ request()->routeIs('penilaian.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                Penilaian
            </a>

            @if(auth()->user()->isGuru())
            <a href="{{ route('pengajuan-izin.index') }}" class="nav-item {{ request()->routeIs('pengajuan-izin.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                Pengajuan Izin/Cuti
            </a>
            @else
            <a href="{{ route('pengajuan-izin.index') }}" class="nav-item {{ request()->routeIs('pengajuan-izin.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                Pengajuan Izin/Cuti
            </a>
            @endif

            {{-- SK Tugas: guru hanya bisa lihat miliknya sendiri --}}
            <a href="{{ route('sk-tugas.index') }}" class="nav-item {{ request()->routeIs('sk-tugas.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" /></svg>
                SK Tugas
            </a>

            @if(auth()->user()->isGuru())
            <a href="{{ route('evaluasi-kinerja.index') }}" class="nav-item {{ request()->routeIs('evaluasi-kinerja.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                Evaluasi Kinerja
            </a>
            @endif
            @endif

            @if(auth()->user()->isPengurusYayasan() || auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
            <div class="sidebar-section">Manajemen</div>

            @if(auth()->user()->isPengurusYayasan() || auth()->user()->isKepalaSekolah() || auth()->user()->isAdminSekolah())
            <a href="{{ route('guru.index') }}" class="nav-item {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                Data Guru
            </a>
            @endif

            <a href="{{ route('rekap-beban.index') }}" class="nav-item {{ request()->routeIs('rekap-beban.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                Rekap Beban Mengajar
            </a>

            <a href="{{ route('sk-tugas.index') }}" class="nav-item {{ request()->routeIs('sk-tugas.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" /></svg>
                SK Tugas
            </a>
            @endif

            @if(auth()->user()->isPengurusYayasan() || auth()->user()->isKepalaSekolah())
            <a href="{{ route('evaluasi-kinerja.index') }}" class="nav-item {{ request()->routeIs('evaluasi-kinerja.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                Evaluasi Kinerja
            </a>
            @endif
        @endauth
    </nav>

    <div class="sidebar-footer">
        @auth
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">
                    @php
                        $roleLabels = [
                            'pengurus_yayasan' => 'Pengurus Yayasan',
                            'kepala_sekolah' => 'Kepala Sekolah '.auth()->user()->jenjang,
                            'guru' => 'Guru '.auth()->user()->jenjang,
                            'admin_sekolah' => 'Admin Sekolah '.auth()->user()->jenjang,
                        ];
                    @endphp
                    {{ $roleLabels[auth()->user()->role] ?? auth()->user()->role }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:8px;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;background:rgba(239,68,68,0.1);color:#ef4444;border:none;cursor:pointer;text-align:left;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                Keluar
            </button>
        </form>
        @endauth
    </div>
</aside>

<!-- Sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay2" onclick="toggleSidebar()"></div>

<!-- Main wrapper -->
<div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <button class="topbar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>

        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

        <div class="topbar-actions">
            <button class="topbar-btn" title="Notifikasi" id="topbar-notif-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                <span class="notif-dot"></span>
            </button>

            @auth
            <div style="display:flex;align-items:center;gap:10px;padding:4px 12px;border:1px solid var(--border);border-radius:var(--radius);">
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="topbar-username">{{ explode(' ', auth()->user()->name)[0] }}</div>
                    <div class="topbar-role">
                        @php
                            $roleShort = [
                                'pengurus_yayasan' => 'Pengurus Yayasan',
                                'kepala_sekolah' => 'Kepala Sekolah',
                                'guru' => 'Guru',
                                'admin_sekolah' => 'Admin Sekolah',
                            ];
                        @endphp
                        {{ $roleShort[auth()->user()->role] ?? auth()->user()->role }}
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <!-- Page content -->
    <main class="page-content">

        @if(session('success'))
        <div class="alert alert-success" id="flash-alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" id="flash-alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Toggle sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const overlay2 = document.getElementById('sidebarOverlay2');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
        overlay2.classList.toggle('open');
    }

    // Auto-hide flash message
    const flashAlert = document.getElementById('flash-alert');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.transition = 'opacity 0.4s';
            flashAlert.style.opacity = '0';
            setTimeout(() => flashAlert.remove(), 400);
        }, 4000);
    }
</script>

@stack('scripts')
</body>
</html>
