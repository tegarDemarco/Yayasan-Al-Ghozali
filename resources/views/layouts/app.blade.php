<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Akademik') – Yayasan Al Ghozaly</title>
    <meta name="description" content="Sistem Informasi Akademik Yayasan Al Ghozaly – SD, MTs, dan SMK">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            /* Forest Green Palette (from Wireframes) */
            --forest-950: #091a14;
            --forest-900: #0f2821;
            --forest-850: #12332a;
            --forest-800: #153e32;
            --forest-750: #1a483a;
            --forest-700: #205847;
            --forest-600: #2b705b;
            --forest-500: #388d74;
            --forest-100: #dcf2ea;
            --forest-50:  #eef7f3;

            /* Surface & Canvas */
            --canvas-bg: #f4f7f5;
            --card-bg: #ffffff;
            --card-border: #e8edea;
            --card-hover-border: #cfe0d8;

            /* Text Colors */
            --text-heading: #111827;
            --text-body: #374151;
            --text-muted: #6b7280;
            --text-subtle: #9ca3af;

            /* Accents */
            --status-green-bg: #dcfce7;
            --status-green-text: #15803d;
            --status-green-dot: #16a34a;

            --status-amber-bg: #fef3c7;
            --status-amber-text: #b45309;
            --status-amber-dot: #d97706;

            --status-red-bg: #fee2e2;
            --status-red-text: #b91c1c;
            --status-red-dot: #ef4444;

            --status-blue-bg: #e0f2fe;
            --status-blue-text: #0369a1;
            --status-blue-dot: #0284c7;

            /* Layout Measurements */
            --sidebar-width: 256px;
            --topbar-height: 64px;
            --radius-xl: 18px;
            --radius-lg: 14px;
            --radius-md: 10px;
            --radius-sm: 6px;
            --radius-pill: 9999px;
            --shadow-subtle: 0 1px 3px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.02);
            --shadow-card: 0 2px 6px -1px rgba(0,0,0,0.04), 0 1px 4px -1px rgba(0,0,0,0.02);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--canvas-bg);
            color: var(--text-body);
            line-height: 1.5;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== SIDEBAR ===== */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--forest-900);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.04);
        }

        .app-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .app-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        /* Sidebar Brand */
        .sidebar-brand-wrapper {
            padding: 24px 20px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-ag-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.03);
        }

        .brand-text-col {
            display: flex;
            flex-direction: column;
        }

        .brand-main-title {
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        .brand-sub-caption {
            color: rgba(255, 255, 255, 0.55);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Unit Active Card in Sidebar */
        .sidebar-unit-box {
            margin: 6px 14px 16px;
            padding: 10px 12px;
            background: var(--forest-850);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .unit-indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
            flex-shrink: 0;
        }

        .unit-info-content {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .unit-info-name {
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .unit-info-desc {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Menu Section Header */
        .sidebar-menu-header {
            padding: 8px 18px 6px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        /* Sidebar Nav */
        .sidebar-nav-container {
            flex: 1;
            padding: 4px 10px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 14px;
            border-radius: var(--radius-md);
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
            position: relative;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            background-color: var(--forest-750);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .sidebar-link i,
        .sidebar-link svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            color: currentColor;
            opacity: 0.85;
        }

        .sidebar-link.active i,
        .sidebar-link.active svg {
            opacity: 1;
            color: #4ade80;
        }

        .sidebar-count-badge {
            margin-left: auto;
            background: #2b705b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .sidebar-link.active .sidebar-count-badge {
            background: #ffffff;
            color: var(--forest-900);
        }

        /* Sidebar Profile & Footer */
        .sidebar-bottom-area {
            padding: 16px 14px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-footer-org {
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.4;
            margin-bottom: 12px;
            padding: 0 4px;
        }

        .sidebar-user-block {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding: 6px 4px;
        }

        .user-avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #1e5241;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-block-details {
            overflow: hidden;
        }

        .user-block-name {
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-block-role {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-logout-btn {
            width: 100%;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: var(--radius-pill);
            padding: 7px 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 11.5px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        /* ===== MAIN LAYOUT ===== */
        .app-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .app-topbar {
            height: var(--topbar-height);
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-nav-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-body);
            cursor: pointer;
            padding: 4px;
        }

        .topbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .topbar-breadcrumb-root {
            color: var(--text-muted);
            text-decoration: none;
        }

        .topbar-breadcrumb-root:hover {
            color: var(--text-heading);
        }

        .topbar-breadcrumb-sep {
            color: var(--text-subtle);
            font-size: 11px;
        }

        .topbar-breadcrumb-current {
            color: var(--text-heading);
            font-weight: 600;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Topbar Pill Selectors (Peran & Unit) */
        .topbar-pill-select {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-md);
            padding: 5px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .topbar-pill-select:hover {
            border-color: var(--forest-500);
        }

        .topbar-pill-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .topbar-pill-value {
            color: var(--text-heading);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .topbar-pill-value select {
            border: none;
            background: transparent;
            font-weight: 700;
            color: var(--text-heading);
            font-family: inherit;
            font-size: 12.5px;
            cursor: pointer;
            outline: none;
        }

        /* Topbar Icon Buttons */
        .topbar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: #ffffff;
            border: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            position: relative;
            transition: all 0.15s;
        }

        .topbar-icon-btn:hover {
            border-color: var(--forest-500);
            color: var(--forest-700);
        }

        .topbar-icon-btn svg,
        .topbar-icon-btn i {
            width: 17px;
            height: 17px;
        }

        .notif-unread-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 7px;
            height: 7px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 1.5px solid #ffffff;
        }

        /* Notification Dropdown */
        .notif-dropdown-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 340px;
            max-width: 90vw;
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 150;
            display: none;
            overflow: hidden;
        }

        .notif-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-dropdown-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .notif-dropdown-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .notif-entry-row {
            padding: 10px 16px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
        }

        .notif-entry-row:hover {
            background-color: var(--forest-50);
        }

        .notif-entry-row.unread {
            background-color: #f6fcf8;
        }

        /* ===== PAGE CONTENT WRAPPER ===== */
        .page-content-area {
            flex: 1;
            padding: 24px 28px 40px;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }

        /* Page Heading & Eyebrow */
        .page-header-block {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-eyebrow {
            font-size: 11px;
            font-weight: 700;
            color: var(--forest-700);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }

        .page-main-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.3px;
            line-height: 1.25;
        }

        .page-sub-caption {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ===== WIREFRAME COMPONENTS ===== */

        /* 1. Hero Banner (Wireframe Style) */
        .hero-banner-card {
            background: linear-gradient(135deg, #164034 0%, #113328 100%);
            border-radius: var(--radius-xl);
            padding: 24px 30px;
            color: #ffffff;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 16px rgba(18, 51, 40, 0.18);
        }

        /* Concentric circles background watermark */
        .hero-banner-card::before {
            content: '';
            position: absolute;
            right: -60px;
            top: -100px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: inset 0 0 0 40px rgba(255, 255, 255, 0.02),
                        inset 0 0 0 80px rgba(255, 255, 255, 0.02),
                        inset 0 0 0 120px rgba(255, 255, 255, 0.02);
            pointer-events: none;
        }

        .hero-banner-content {
            position: relative;
            z-index: 2;
        }

        .hero-date-tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.9px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .hero-headline {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.2px;
            margin-bottom: 4px;
        }

        .hero-description {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.8);
            max-width: 650px;
            line-height: 1.45;
        }

        .hero-badge-pill {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        /* 2. Four Stats Grid */
        .stats-four-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .stat-white-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 104px;
            box-shadow: var(--shadow-subtle);
            transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
        }

        .stat-white-card:hover {
            border-color: var(--card-hover-border);
            box-shadow: var(--shadow-card);
        }

        .stat-card-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .stat-label-text {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .stat-icon-square {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background-color: var(--forest-50);
            color: var(--forest-700);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon-square svg,
        .stat-icon-square i {
            width: 16px;
            height: 16px;
        }

        .stat-value-big {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .stat-subtext-detail {
            font-size: 11.5px;
            color: var(--text-subtle);
            margin-top: 4px;
        }

        /* 3. Four Quick Action Cards */
        .action-four-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .action-white-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--shadow-subtle);
            transition: all 0.15s ease;
        }

        .action-white-card:hover {
            border-color: var(--forest-500);
            transform: translateY(-2px);
            box-shadow: var(--shadow-card);
            background-color: #fafcfb;
        }

        .action-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background-color: var(--forest-50);
            color: var(--forest-800);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background-color 0.15s, color 0.15s;
        }

        .action-white-card:hover .action-icon-box {
            background-color: var(--forest-800);
            color: #ffffff;
        }

        .action-icon-box svg,
        .action-icon-box i {
            width: 17px;
            height: 17px;
        }

        .action-text-col {
            display: flex;
            flex-direction: column;
        }

        .action-title-text {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-heading);
            line-height: 1.3;
        }

        .action-desc-text {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* 4. Two Column Dashboard Content */
        .dashboard-split-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            margin-bottom: 30px;
        }

        .surface-white-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-subtle);
        }

        .card-header-flex {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.2px;
        }

        .card-section-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .card-header-link {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--forest-700);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .card-header-link:hover {
            color: var(--forest-900);
            text-decoration: underline;
        }

        /* Wireframe Table Styling */
        .wireframe-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .wireframe-table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.6px;
            text-transform: uppercase;
            border-bottom: 1px solid #edf1ee;
        }

        .wireframe-table td {
            padding: 14px 14px;
            border-bottom: 1px solid #f3f5f4;
            color: var(--text-body);
            vertical-align: middle;
        }

        .wireframe-table tr:last-child td {
            border-bottom: none;
        }

        .wireframe-table tr:hover td {
            background-color: #f9fbf9;
        }

        /* Pill Status Badges */
        .pill-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 12px;
            border-radius: var(--radius-pill);
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.4;
            width: fit-content;
        }

        .pill-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
        }

        .pill-status-menunggu,
        .pill-status-draft {
            background-color: var(--status-amber-bg);
            color: var(--status-amber-text);
        }

        .pill-status-selesai,
        .pill-status-terpublikasi,
        .pill-status-valid {
            background-color: var(--status-green-bg);
            color: var(--status-green-text);
        }

        .pill-status-perlu_tindakan,
        .pill-status-revisi {
            background-color: var(--status-red-bg);
            color: var(--status-red-text);
        }

        .pill-status-info {
            background-color: var(--status-blue-bg);
            color: var(--status-blue-text);
        }

        /* Action Buttons */
        .btn-pill-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 14px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-pill-primary {
            background-color: var(--status-green-bg);
            color: var(--status-green-text);
        }
        .btn-pill-primary:hover {
            background-color: #bbf7d0;
        }

        .btn-pill-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }
        .btn-pill-secondary:hover {
            background-color: #e5e7eb;
        }

        .btn-pill-danger {
            background-color: var(--status-red-bg);
            color: var(--status-red-text);
        }
        .btn-pill-danger:hover {
            background-color: #fecaca;
        }

        .btn-pill-done {
            background-color: var(--status-green-bg);
            color: var(--status-green-text);
            cursor: default;
        }

        .btn-forest-primary {
            background-color: var(--forest-800);
            color: #ffffff;
            border-radius: var(--radius-md);
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-forest-primary:hover {
            background-color: var(--forest-900);
            box-shadow: 0 2px 8px rgba(18, 51, 40, 0.2);
        }

        /* Timeline list (Guru Dashboard) */
        .timeline-agenda-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .timeline-agenda-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f4f2;
        }

        .timeline-agenda-item:last-child {
            border-bottom: none;
        }

        .timeline-time-badge {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-muted);
            width: 48px;
            flex-shrink: 0;
        }

        .timeline-content-col {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .timeline-item-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .timeline-item-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Right Column Cards */
        .sidebar-item-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f4f2;
        }

        .sidebar-item-box:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .sidebar-item-icon-square {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background-color: var(--forest-50);
            color: var(--forest-700);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-item-icon-square svg,
        .sidebar-item-icon-square i {
            width: 15px;
            height: 15px;
        }

        .sidebar-item-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-heading);
            line-height: 1.3;
        }

        .sidebar-item-desc {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 3px;
            line-height: 1.4;
        }

        /* Period Summary Card */
        .period-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f4f2;
            font-size: 12.5px;
        }

        .period-summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .period-row-key {
            color: var(--text-muted);
        }

        .period-row-val {
            font-weight: 700;
            color: var(--text-heading);
        }

        /* Info Banner Callout (as in screen-validasi-kepala) */
        .info-callout-box {
            background-color: #f7faf8;
            border: 1px solid #e2ece6;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: 12.5px;
            color: #2b5446;
            line-height: 1.5;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Filter & Search Bar Card */
        .table-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .filter-controls-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            max-width: 600px;
        }

        .search-input-wrap {
            position: relative;
            flex: 1;
        }

        .search-input-wrap svg,
        .search-input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--text-subtle);
            pointer-events: none;
        }

        .form-control-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-family: inherit;
            color: var(--text-heading);
            background: #ffffff;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-control-input:focus {
            border-color: var(--forest-500);
            box-shadow: 0 0 0 3px rgba(43, 112, 91, 0.1);
        }

        .form-control-select {
            padding: 8px 30px 8px 12px;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-family: inherit;
            color: var(--text-heading);
            background: #ffffff;
            outline: none;
            cursor: pointer;
        }

        .form-control-select:focus {
            border-color: var(--forest-500);
        }

        .filter-data-count {
            font-size: 12px;
            color: var(--text-subtle);
            font-weight: 500;
        }

        .footnote-prototype {
            text-align: center;
            font-size: 11.5px;
            color: var(--text-subtle);
            margin-top: 24px;
        }

        /* Bottom Footer */
        .app-footer {
            text-align: center;
            padding: 24px 0 32px;
            color: var(--text-subtle);
            font-size: 12px;
        }

        /* Flash Message */
        .flash-alert-card {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-alert-danger {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* SVG Safety & Legacy Stat Cards Styling */
        svg {
            max-width: 100%;
        }

        .stats-grid {
            display: grid;
            gap: 16px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 16px 18px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg,
        .stat-icon i {
            width: 20px !important;
            height: 20px !important;
            max-width: 20px;
            max-height: 20px;
        }

        .stat-icon.icon-green { background-color: var(--forest-50); color: var(--forest-700); }
        .stat-icon.icon-blue { background-color: #e0f2fe; color: #0284c7; }
        .stat-icon.icon-yellow { background-color: #fef3c7; color: #b45309; }
        .stat-icon.icon-red { background-color: #fee2e2; color: #dc2626; }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ===== General System Compatibility & Forms Styling ===== */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-subtle);
            margin-bottom: 20px;
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
            color: var(--text-heading);
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .page-header {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-header-left h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.3px;
        }

        .page-header-left p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Common Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-primary {
            background-color: var(--forest-800);
            color: #ffffff;
            border-color: var(--forest-800);
        }
        .btn-primary:hover {
            background-color: var(--forest-900);
            border-color: var(--forest-900);
            box-shadow: 0 2px 8px rgba(18, 51, 40, 0.2);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--text-body);
            border-color: var(--card-border);
        }
        .btn-secondary:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        .btn-warning {
            background-color: #fef3c7;
            color: #b45309;
            border-color: #fde68a;
        }
        .btn-warning:hover {
            background-color: #fde68a;
        }

        .btn-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }
        .btn-danger:hover {
            background-color: #fecaca;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
            border-radius: var(--radius-sm);
        }

        .btn-icon {
            padding: 6px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }

        /* Filters & Form Controls */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--text-subtle);
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-md);
            font-size: 13px;
            font-family: inherit;
            color: var(--text-heading);
            background: #ffffff;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-control:focus {
            border-color: var(--forest-500);
            box-shadow: 0 0 0 3px rgba(43, 112, 91, 0.1);
        }

        .search-box .form-control {
            padding-left: 36px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 6px;
        }

        .req {
            color: #ef4444;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .form-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .form-error {
            font-size: 11.5px;
            color: #ef4444;
            margin-top: 4px;
        }

        /* Tables */
        .table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.6px;
            text-transform: uppercase;
            border-bottom: 1px solid #edf1ee;
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid #f3f5f4;
            color: var(--text-body);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f9fbf9;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.4;
        }

        .badge-green { background-color: #dcfce7; color: #15803d; }
        .badge-blue { background-color: #e0f2fe; color: #0369a1; }
        .badge-yellow { background-color: #fef3c7; color: #b45309; }
        .badge-red { background-color: #fee2e2; color: #b91c1c; }
        .badge-purple { background-color: #ede9fe; color: #6d28d9; }
        .badge-orange { background-color: #ffedd5; color: #c2410c; }
        .badge-gray { background-color: #f3f4f6; color: #4b5563; }

        /* Jenjang Badges */
        .jenjang-sd {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .jenjang-mts {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background-color: #fef3c7;
            color: #b45309;
        }

        .jenjang-smk {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background-color: #fce7f3;
            color: #be185d;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state svg {
            width: 36px;
            height: 36px;
            color: var(--text-subtle);
            margin: 0 auto 10px;
            display: block;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 4px;
        }

        .empty-state p {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
            font-size: 12px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .page-link {
            padding: 6px 12px;
            border: 1px solid var(--card-border, #e2e8f0);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-body, #334155);
            background: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .page-link:hover:not(.disabled):not(.active) {
            border-color: var(--forest-600, #16a34a);
            background: var(--forest-50, #f0fdf4);
            color: var(--forest-800, #166534);
        }

        .page-link.active {
            background: var(--forest-800, #166534);
            color: #ffffff;
            border-color: var(--forest-800, #166534);
        }

        .page-link.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
            background: #f8fafc;
            color: var(--text-muted, #94a3b8);
        }

        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
        }

        /* Modal Dialog */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }

        .modal-overlay.active,
        .modal-overlay.open {
            display: flex !important;
        }

        .modal,
        .modal-box {
            background: #ffffff;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 480px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-box-lg {
            max-width: 620px;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-subtle);
            font-size: 20px;
            line-height: 1;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 8px;
        }

        /* Mobile Overlay */
        .sidebar-mobile-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 90;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-four-grid,
            .action-four-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .dashboard-split-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            .app-sidebar.open {
                transform: translateX(0);
            }
            .sidebar-mobile-backdrop.open {
                display: block;
            }
            .app-main {
                margin-left: 0;
            }
            .mobile-nav-toggle {
                display: block;
            }
            .stats-four-grid,
            .action-four-grid {
                grid-template-columns: 1fr;
            }
            .hero-banner-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .topbar-pill-select {
                display: none;
            }
        }
    </style>
</head>
<body>

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $authUser = auth()->user();
    $role = $authUser?->role ?? 'guru';
    $jenjang = $authUser?->jenjang ?? 'SD';
    $unitLabel = ($role === 'pengurus_yayasan')
        ? 'Yayasan Al Ghozaly'
        : ($jenjang . ' Al Ghozaly');
    $unitStatus = ($role === 'pengurus_yayasan')
        ? 'Akses lintas satuan pendidikan'
        : 'Unit pendidikan aktif';

    $roleTitles = [
        'pengurus_yayasan' => 'Pengurus Yayasan',
        'kepala_sekolah' => 'Kepala Sekolah',
        'admin_sekolah' => 'Admin Sekolah',
        'guru' => 'Guru',
    ];
    $roleTitle = $roleTitles[$role] ?? 'Pengguna';

    // User initials
    $nameParts = explode(' ', $authUser?->name ?? 'User');
    $initials = count($nameParts) > 1
        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
        : strtoupper(substr($authUser?->name ?? 'US', 0, 2));

    // Dynamic unread count
    try {
        $unreadCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
    } catch (\Throwable $e) {
        $unreadCount = 0;
    }
@endphp

<!-- Mobile Backdrop -->
<div class="sidebar-mobile-backdrop" id="mobileBackdrop" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="app-sidebar" id="appSidebar">
    <!-- Brand -->
    <a href="{{ route('dashboard') }}" class="sidebar-brand-wrapper">
        <div class="brand-ag-badge">AG</div>
        <div class="brand-text-col">
            <span class="brand-main-title">Al Ghozaly</span>
            <span class="brand-sub-caption">SISTEM AKADEMIK</span>
        </div>
    </a>

    <!-- Unit Card in Sidebar -->
    <div class="sidebar-unit-box">
        <span class="unit-indicator-dot"></span>
        <div class="unit-info-content">
            <span class="unit-info-name">{{ $unitLabel }}</span>
            <span class="unit-info-desc">{{ $unitStatus }}</span>
        </div>
    </div>

    <!-- Menu Section Header -->
    <div class="sidebar-menu-header">Menu Utama</div>

    <!-- Navigation -->
    <nav class="sidebar-nav-container">
        {{-- Beranda --}}
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="home"></i>
            <span>Beranda</span>
        </a>

        @if($role === 'pengurus_yayasan')
            {{-- Pengurus Yayasan Menu --}}
            <a href="{{ route('sk-tugas.index') }}" class="sidebar-link {{ request()->routeIs('sk-tugas.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>SK Tugas</span>
            </a>
            <a href="{{ route('rekap-beban.index') }}" class="sidebar-link {{ request()->routeIs('rekap-beban.*') ? 'active' : '' }}">
                <i data-lucide="table-properties"></i>
                <span>Rekap Beban Mengajar</span>
            </a>
            <a href="{{ route('guru.index') }}" class="sidebar-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Data Guru</span>
            </a>
            <a href="{{ route('evaluasi-kinerja.index') }}" class="sidebar-link {{ request()->routeIs('evaluasi-kinerja.*') ? 'active' : '' }}">
                <i data-lucide="award"></i>
                <span>Evaluasi Kinerja</span>
            </a>

        @elseif($role === 'kepala_sekolah')
            {{-- Kepala Sekolah Menu --}}
            @php
                try {
                    $pendingRppCount = \App\Models\RppModulAjar::where('sekolah_id', $authUser->sekolah_id)->where('status_validasi', 'menunggu')->count();
                    $pendingIzinCount = \App\Models\PengajuanIzinCuti::where('sekolah_id', $authUser->sekolah_id)->where('status_approval', 'menunggu')->count();
                } catch (\Throwable $e) {
                    $pendingRppCount = 0;
                    $pendingIzinCount = 0;
                }
            @endphp
            <a href="{{ route('rpp.index') }}" class="sidebar-link {{ request()->routeIs('rpp.*') ? 'active' : '' }}">
                <i data-lucide="check-square"></i>
                <span>Validasi RPP / Modul</span>
                @if($pendingRppCount > 0)
                    <span class="sidebar-count-badge">{{ $pendingRppCount }}</span>
                @endif
            </a>
            <a href="{{ route('pengajuan-izin.index') }}" class="sidebar-link {{ request()->routeIs('pengajuan-izin.*') ? 'active' : '' }}">
                <i data-lucide="clock"></i>
                <span>Pengajuan Izin / Cuti</span>
                @if($pendingIzinCount > 0)
                    <span class="sidebar-count-badge">{{ $pendingIzinCount }}</span>
                @endif
            </a>
            <a href="{{ route('evaluasi-kinerja.index') }}" class="sidebar-link {{ request()->routeIs('evaluasi-kinerja.*') ? 'active' : '' }}">
                <i data-lucide="sparkles"></i>
                <span>Evaluasi Kinerja</span>
            </a>
            <a href="{{ route('jadwal-mengajar.index') }}" class="sidebar-link {{ request()->routeIs('jadwal-mengajar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Jadwal Mengajar</span>
            </a>
            <a href="{{ route('presensi.index') }}" class="sidebar-link {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                <i data-lucide="check"></i>
                <span>Presensi Guru</span>
            </a>
            <a href="{{ route('rekap-beban.index') }}" class="sidebar-link {{ request()->routeIs('rekap-beban.*') ? 'active' : '' }}">
                <i data-lucide="table-properties"></i>
                <span>Lihat Beban Ajar</span>
            </a>
            <a href="{{ route('guru.index') }}" class="sidebar-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Data Guru</span>
            </a>

        @elseif($role === 'admin_sekolah')
            {{-- Admin Sekolah Menu --}}
            <a href="{{ route('guru.index') }}" class="sidebar-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Data Guru</span>
            </a>
            <a href="{{ route('jadwal-mengajar.index') }}" class="sidebar-link {{ request()->routeIs('jadwal-mengajar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Jadwal Mengajar</span>
            </a>
            <a href="{{ route('presensi.index') }}" class="sidebar-link {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                <i data-lucide="check"></i>
                <span>Presensi Guru</span>
            </a>
            <a href="{{ route('rpp.index') }}" class="sidebar-link {{ request()->routeIs('rpp.*') ? 'active' : '' }}">
                <i data-lucide="folder"></i>
                <span>Arsip RPP / Modul</span>
            </a>
            <a href="{{ route('sk-tugas.index') }}" class="sidebar-link {{ request()->routeIs('sk-tugas.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>Arsip SK Tugas</span>
            </a>

        @else
            {{-- Guru Menu --}}
            <a href="{{ route('jadwal-mengajar.index') }}" class="sidebar-link {{ request()->routeIs('jadwal-mengajar.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Jadwal Mengajar</span>
            </a>
            <a href="{{ route('presensi.index') }}" class="sidebar-link {{ request()->routeIs('presensi.*') ? 'active' : '' }}">
                <i data-lucide="check"></i>
                <span>Presensi Guru</span>
            </a>
            <a href="{{ route('jurnal.index') }}" class="sidebar-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                <i data-lucide="book-open"></i>
                <span>Jurnal Mengajar</span>
            </a>
            <a href="{{ route('rpp.index') }}" class="sidebar-link {{ request()->routeIs('rpp.*') ? 'active' : '' }}">
                <i data-lucide="folder"></i>
                <span>RPP / Modul Ajar</span>
            </a>
            <a href="{{ route('bahan-ajar.index') }}" class="sidebar-link {{ request()->routeIs('bahan-ajar.*') ? 'active' : '' }}">
                <i data-lucide="upload-cloud"></i>
                <span>Upload Bahan Ajar</span>
            </a>
            <a href="{{ route('penilaian.index') }}" class="sidebar-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}">
                <i data-lucide="pen-tool"></i>
                <span>Penilaian</span>
            </a>
            <a href="{{ route('rekap-beban.index') }}" class="sidebar-link {{ request()->routeIs('rekap-beban.*') ? 'active' : '' }}">
                <i data-lucide="table-properties"></i>
                <span>Rekap Beban Mengajar</span>
            </a>
            <a href="{{ route('pengajuan-izin.index') }}" class="sidebar-link {{ request()->routeIs('pengajuan-izin.*') ? 'active' : '' }}">
                <i data-lucide="clock"></i>
                <span>Pengajuan Izin / Cuti</span>
            </a>
            <a href="{{ route('sk-tugas.index') }}" class="sidebar-link {{ request()->routeIs('sk-tugas.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>SK Tugas</span>
            </a>
            <a href="{{ route('evaluasi-kinerja.index') }}" class="sidebar-link {{ request()->routeIs('evaluasi-kinerja.*') ? 'active' : '' }}">
                <i data-lucide="sparkles"></i>
                <span>Evaluasi Kinerja</span>
            </a>
        @endif
    </nav>

    <!-- Sidebar Bottom / Profile Card -->
    <div class="sidebar-bottom-area">
        <div class="sidebar-footer-org">
            <div>Pusat administrasi pendidikan</div>
            <div>Yayasan Al Ghozaly · 2026</div>
        </div>
        <div class="sidebar-user-block" onclick="openSettingsModal()" style="cursor: pointer;" title="Klik untuk edit profil">
            <div class="user-avatar-circle" style="overflow:hidden;position:relative;">
                @if($authUser?->foto)
                    <img src="{{ asset('storage/' . $authUser->foto) }}" alt="{{ $authUser->name }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" onerror="this.onerror=null;this.style.display='none';this.parentElement.textContent='{{ $initials }}';">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="user-block-details" style="flex:1;">
                <div class="user-block-name">{{ $authUser?->name ?? 'Pengguna' }}</div>
                <div class="user-block-role" style="display:flex;align-items:center;justify-content:space-between;">
                    <span>{{ $roleTitle }}</span>
                    <i data-lucide="settings" style="width:12px;height:12px;opacity:0.75;"></i>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <i data-lucide="log-out" style="width:13px;height:13px;"></i>
                <span>Keluar dari sistem</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main Wrapper -->
<div class="app-main">
    <!-- Topbar -->
    <header class="app-topbar">
        <div class="topbar-left">
            <button class="mobile-nav-toggle" onclick="toggleSidebar()" aria-label="Buka Menu">
                <i data-lucide="menu"></i>
            </button>
            <nav class="topbar-breadcrumb">
                <a href="{{ route('dashboard') }}" class="topbar-breadcrumb-root">Yayasan Al Ghozaly</a>
                <span class="topbar-breadcrumb-sep">/</span>
                <span class="topbar-breadcrumb-current">@yield('page-title', 'Beranda')</span>
            </nav>
        </div>

        <div class="topbar-right">
            <!-- Notifikasi Bell -->
            <div style="position: relative;">
                <button class="topbar-icon-btn" id="notifBellBtn" title="Notifikasi" aria-label="Notifikasi">
                    <i data-lucide="bell"></i>
                    @if($unreadCount > 0)
                        <span class="notif-unread-dot"></span>
                    @endif
                </button>

                <!-- Dropdown -->
                <div class="notif-dropdown-panel" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <span class="notif-dropdown-title">Notifikasi @if($unreadCount > 0) ({{ $unreadCount }}) @endif</span>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifikasi.mark-all-read') }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:var(--forest-600);font-size:11.5px;font-weight:600;cursor:pointer;">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </div>
                    <div class="notif-dropdown-list">
                        @php
                            try {
                                $recentNotifs = $authUser ? $authUser->notifications()->take(4)->get() : collect();
                            } catch (\Throwable $e) {
                                $recentNotifs = collect();
                            }
                        @endphp
                        @forelse($recentNotifs as $n)
                        <a href="{{ route('notifikasi.read-and-redirect', $n->id) }}" class="notif-entry-row {{ is_null($n->read_at) ? 'unread' : '' }}">
                            <div style="font-size:12px;">
                                <div style="font-weight:700;color:var(--text-heading);">{{ $n->data['title'] ?? 'Pemberitahuan' }}</div>
                                <div style="color:var(--text-muted);font-size:11.5px;">{{ $n->data['message'] ?? '' }}</div>
                                <div style="font-size:10.5px;color:var(--text-subtle);margin-top:2px;">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                        @empty
                        <div style="padding:24px;text-align:center;color:var(--text-subtle);font-size:12.5px;">
                            Tidak ada notifikasi baru
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Settings Button -->
            <button class="topbar-icon-btn" title="Edit Profil & Pengaturan" onclick="openSettingsModal()" aria-label="Pengaturan">
                <i data-lucide="settings"></i>
            </button>
        </div>
    </header>

    <!-- Page Content Container -->
    <main class="page-content-area">
        @if(session('success'))
        <div class="flash-alert-card" id="flashAlert">
            <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="flash-alert-card flash-alert-danger" id="flashAlert">
            <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')

        <!-- Footer -->
        <footer class="app-footer">
            Sistem Informasi Akademik · Yayasan Al Ghozaly
        </footer>
    </main>
</div>

<!-- Edit Profile & Settings Modal -->
<div class="modal-overlay" id="settingsModal" onclick="if(event.target===this)closeSettingsModal()">
    <div class="modal-box modal-box-lg" style="max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 22px 24px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; border-bottom: 1px solid var(--card-border); padding-bottom: 12px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:8px; background:var(--forest-100); color:var(--forest-800); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="user-cog" style="width:18px;height:18px;"></i>
                </div>
                <div>
                    <div class="modal-title" style="margin-bottom:0; font-size:15px; font-weight:700;">Edit Profil & Data Diri</div>
                    <div style="font-size:11.5px; color:var(--text-subtle);">Perbarui informasi data pribadi dan foto profil Anda</div>
                </div>
            </div>
            <button type="button" onclick="closeSettingsModal()" style="background:none; border:none; cursor:pointer; color:var(--text-subtle); font-size:22px; line-height:1; padding:2px 6px;">&times;</button>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="modalProfileForm">
            @csrf
            @method('PUT')

            <!-- Foto Profil & Quick Info -->
            <div style="display:flex; align-items:center; gap:14px; padding:12px 14px; background:var(--forest-50); border-radius:var(--radius-md); border:1px solid #cce3dc; margin-bottom:16px;">
                <div style="position:relative; width:62px; height:62px; flex-shrink:0;">
                    <div id="modalAvatarDisplay" class="user-avatar-circle" style="width:62px; height:62px; font-size:20px; overflow:hidden; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.1); position:relative;">
                        <span id="modalAvatarInitials" style="{{ $authUser?->foto ? 'display:none;' : '' }}">{{ $initials }}</span>
                        <img id="modalAvatarImg" src="{{ $authUser?->foto ? asset('storage/' . $authUser->foto) : '' }}" alt="Foto Profil" style="{{ $authUser?->foto ? 'width:100%; height:100%; object-fit:cover;' : 'display:none;' }}" onerror="this.onerror=null;this.style.display='none';document.getElementById('modalAvatarInitials').style.display='inline-block';">
                    </div>
                </div>
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:4px;">
                        <label for="modalFotoInput" class="btn-pill-action btn-pill-secondary" style="cursor:pointer; font-size:11.5px; padding:4px 10px; display:inline-flex; align-items:center; gap:5px;">
                            <i data-lucide="camera" style="width:13px; height:13px;"></i>
                            <span>Pilih Foto</span>
                        </label>
                        <input type="file" name="foto" id="modalFotoInput" accept="image/png,image/jpeg,image/jpg,image/webp" style="display:none;" onchange="previewModalAvatar(event)">

                        @if($authUser?->foto)
                            <button type="button" class="btn-pill-action" id="modalDeletePhotoBtn" style="font-size:11.5px; padding:4px 10px; color:#ef4444; border-color:#fca5a5; display:inline-flex; align-items:center; gap:4px;" onclick="hapusFotoProfilModal()">
                                <i data-lucide="trash-2" style="width:12px; height:12px;"></i>
                                <span>Hapus Foto</span>
                            </button>
                        @endif
                    </div>
                    <div style="font-size:11px; color:var(--text-subtle);">Format: JPG, PNG, atau WEBP. Maks 2 MB.</div>
                    <div style="display:flex; gap:6px; margin-top:6px; flex-wrap:wrap;">
                        <span class="badge badge-success" style="font-size:10.5px;">{{ $roleTitle }}</span>
                        <span class="badge badge-info" style="font-size:10.5px;">{{ $unitLabel }}</span>
                    </div>
                </div>
            </div>

            <!-- Hidden input for delete photo -->
            <input type="hidden" name="hapus_foto" id="modalHapusFotoInput" value="0">

            <!-- Data Fields Grid -->
            <div class="form-grid form-grid-2" style="margin-bottom:12px;">
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label" for="modal_name">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="modal_name" name="name" class="form-control" value="{{ old('name', $authUser?->name) }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label" for="modal_email">Email <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="modal_email" name="email" class="form-control" value="{{ old('email', $authUser?->email) }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label" for="modal_nip">NIP / NUPTK</label>
                    <input type="text" id="modal_nip" name="nip" class="form-control" value="{{ old('nip', $authUser?->nip) }}" placeholder="Nomor Induk Pegawai">
                    @error('nip')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label" for="modal_telepon">No. Telepon / WhatsApp</label>
                    <input type="text" id="modal_telepon" name="telepon" class="form-control" value="{{ old('telepon', $authUser?->telepon) }}" placeholder="08xxxxxxxxxx">
                    @error('telepon')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom:10px;">
                <label class="form-label" for="modal_jabatan">Jabatan</label>
                <input type="text" id="modal_jabatan" name="jabatan" class="form-control" value="{{ old('jabatan', $authUser?->jabatan) }}" placeholder="Contoh: Guru Pengajar / Staf">
                @error('jabatan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" for="modal_alamat">Alamat Tempat Tinggal</label>
                <textarea id="modal_alamat" name="alamat" class="form-control" rows="2" placeholder="Alamat domisili saat ini...">{{ old('alamat', $authUser?->alamat) }}</textarea>
                @error('alamat')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Accordion / Section -->
            <div style="border-top:1px dashed var(--card-border); padding-top:12px; margin-top:6px; margin-bottom:14px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; cursor:pointer;" onclick="togglePasswordSection()">
                    <span style="font-size:12.5px; font-weight:700; color:var(--text-heading); display:flex; align-items:center; gap:6px;">
                        <i data-lucide="lock" style="width:14px; height:14px;"></i>
                        Ubah Kata Sandi (Opsional)
                    </span>
                    <span id="pwToggleIcon" style="font-size:11px; color:var(--forest-700); font-weight:600;">Klik untuk ubah &darr;</span>
                </div>

                <div id="passwordFieldsWrapper" style="display:none;">
                    <div class="form-grid form-grid-2">
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label" for="modal_password">Password Baru</label>
                            <input type="password" id="modal_password" name="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password">
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label" for="modal_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" id="modal_password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" autocomplete="new-password">
                        </div>
                    </div>
                    <div style="font-size:11px; color:var(--text-subtle); margin-top:2px;">Kosongkan jika tidak bermaksud mengganti password akun.</div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid var(--card-border); margin-top:12px;">
                <a href="{{ route('profile.edit') }}" style="font-size:12px; color:var(--forest-700); text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                    <i data-lucide="external-link" style="width:13px; height:13px;"></i>
                    <span>Halaman Lengkap Profil</span>
                </a>
                <div style="display:flex; align-items:center; gap:8px;">
                    <button type="button" class="btn-pill-action btn-pill-secondary" onclick="closeSettingsModal()">Batal</button>
                    <button type="submit" class="btn-forest-primary" style="padding:7px 18px; font-size:13px;">
                        <i data-lucide="save" style="width:14px; height:14px;"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Toggle Sidebar Mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('mobileBackdrop');
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('open');
    }

    // Toggle Notification
    const notifBtn = document.getElementById('notifBellBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.style.display = notifDropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                notifDropdown.style.display = 'none';
            }
        });
    }

    // Settings Modal
    function openSettingsModal() {
        document.getElementById('settingsModal').style.display = 'flex';
        lucide.createIcons();
    }
    function closeSettingsModal() {
        document.getElementById('settingsModal').style.display = 'none';
    }

    // Preview photo on select in modal
    function previewModalAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('modalAvatarImg');
            const initials = document.getElementById('modalAvatarInitials');
            img.src = e.target.result;
            img.style.display = 'block';
            if (initials) {
                initials.style.display = 'none';
            }
            document.getElementById('modalHapusFotoInput').value = '0';
        };
        reader.readAsDataURL(file);
    }

    // Mark photo for deletion in modal
    function hapusFotoProfilModal() {
        if (!confirm('Apakah Anda yakin ingin menghapus foto profil?')) return;
        document.getElementById('modalHapusFotoInput').value = '1';
        const img = document.getElementById('modalAvatarImg');
        const initials = document.getElementById('modalAvatarInitials');
        if (img) img.style.display = 'none';
        if (initials) initials.style.display = 'inline-block';
        const btn = document.getElementById('modalDeletePhotoBtn');
        if (btn) btn.style.display = 'none';
    }

    // Toggle password section in modal
    function togglePasswordSection(forceOpen = false) {
        const wrapper = document.getElementById('passwordFieldsWrapper');
        const icon = document.getElementById('pwToggleIcon');
        if (!wrapper) return;

        if (forceOpen || wrapper.style.display === 'none') {
            wrapper.style.display = 'block';
            if (icon) icon.innerHTML = 'Tutup &uarr;';
        } else {
            wrapper.style.display = 'none';
            if (icon) icon.innerHTML = 'Klik untuk ubah &darr;';
        }
    }

    @if($errors->any())
        // Auto open modal if validation error occurs
        openSettingsModal();
        @if($errors->has('password') || $errors->has('password_confirmation'))
            togglePasswordSection(true);
        @endif
    @endif

    // Auto dismiss alert
    const flashAlert = document.getElementById('flashAlert');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.transition = 'opacity 0.4s';
            flashAlert.style.opacity = '0';
            setTimeout(() => flashAlert.remove(), 400);
        }, 4000);
    }

    // Search input enhancements
    document.querySelectorAll('.search-input-wrap input, .search-box input').forEach(input => {
        input.setAttribute('autocomplete', 'off');
    });
</script>

@stack('scripts')
</body>
</html>
