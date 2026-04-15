<!DOCTYPE html>
//app.blade.php
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- ✅ Meta wajib --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Production System')</title>

    {{-- ✅ Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ✅ Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- ✅ Google Fonts: Inter (Engineer design system) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .content {
    padding: 24px 32px;
    background-color: #F9FAFB;
    min-height: calc(100vh - 70px);
}
        /* ================================================================
           GLOBAL
        ================================================================ */
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #F8F9FB;
            color: #101828;
        }
        .legacy-ui body {
            font-family: Arial;
            background: #f4f6f9;
        }

        .legacy-ui .sidebar {
            background: #fff;
        }

        .legacy-ui .topbar {
            background: #f3f4f6;
        }

        .legacy-ui .content {
            padding: 30px;
        }
            /* GLOBAL RESET */
* {
    box-sizing: border-box;
}

/* BODY */
body {
    margin: 0;
    font-family: 'Inter', Arial, sans-serif;
    background: #F8F9FB;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    height: 100vh;
    background: white;
    position: fixed;
    border-right: 1px solid #eaecf0;
}

/* MAIN */
.main {
    margin-left: 250px;
}

/* CONTENT */
.content {
    padding: 24px 32px;
}

/* TOPBAR */
.topbar {
    height: 70px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
}

/* CARD */
.card-clean {
    background: white;
    border: 1px solid #eaecf0;
    border-radius: 12px;
    padding: 20px;
}
        /* ================================================================
           SIDEBAR
        ================================================================ */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: white;
            position: fixed;
            border-right: 1px solid #eaecf0;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        /* Mobile: sembunyikan sidebar */
        @media (max-width: 768px) {
            .sidebar               { transform: translateX(-100%); }
            .sidebar.show          { transform: translateX(0); }
            .main                  { margin-left: 0 !important; }
            .sidebar-overlay       { display: block !important; }
        }

        /* Overlay gelap saat sidebar mobile terbuka */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1039;
        }

        /* ── Sidebar Header & Logo ── */
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            transform: rotate(-95deg);
        }

        .sidebar-title { font-size: 14px; font-weight: 600; }
        .sidebar-sub   { font-size: 12px; color: #777; }

        /* Logo bulat (Engineer style) */
        .logo-area {
            display: flex;
            align-items: center;
            padding: 0 24px 30px 24px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: #2F6BFF;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .company-name {
            font-weight: 700;
            font-size: 14px;
            line-height: 1.3;
            color: #101828;
        }

        /* ── Sidebar Menu ── */
        .menu { padding: 20px; }

        .menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            text-decoration: none;
            color: #333;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .menu a:hover  { background: #f3f4f6; }
        .menu a.active { background: #2563eb; color: white; }
        .menu hr       { border: none; border-top: 1px solid #eee; margin: 10px 0; }

        /* Sidebar nav (Engineer style) */
        .sidebar-nav .nav-link {
            color: #475467;
            font-weight: 500;
            padding: 10px 16px;
            margin: 0 16px 4px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar-nav .nav-link:hover  { background-color: #f9fafb; color: #101828; }
        .sidebar-nav .nav-link.active { background-color: #2F6BFF; color: white; }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 1px;
            padding: 20px 24px 8px;
        }

        /* ================================================================
           MAIN & TOPBAR
        ================================================================ */
        .main    { margin-left: 250px; transition: margin-left 0.3s ease; }
        .content { padding: 24px 32px; background-color: #F9FAFB; min-height: calc(100vh - 70px); }

        .topbar {
            height: 70px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            border-bottom: 1px solid #eaecf0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Hamburger — hanya mobile */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #333;
            padding: 0;
            margin-right: 10px;
        }

        @media (max-width: 768px) { .sidebar-toggle { display: block; } }

        /* ── Search Box ── */
        .search-box          { position: relative; }
        .search-box input,
        .search-input {
            width: 420px;
            height: 40px;
            padding-left: 38px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            outline: none;
            font-size: 14px;
            background: #fff;
            transition: border-color 0.2s;
        }

        .search-box input:focus,
        .search-input:focus  { border-color: #2F6BFF; }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        @media (max-width: 576px) {
            .search-box input,
            .search-input { width: 160px; }
        }

        /* ── Topbar Right ── */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification { font-size: 20px; color: black; cursor: pointer; }

        /* ── User Card (original style) ── */
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 6px 12px;
            border-radius: 10px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            background: #2563eb;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ── User Profile (Engineer style) ── */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-name { font-size: 14px; font-weight: 600; color: #101828; }

        .user-role {
            font-size: 12px;
            background-color: #F2F4F7;
            color: #344054;
            padding: 2px 8px;
            border-radius: 16px;
            font-weight: 500;
            display: inline-block;
        }

        /* ── Logout ── */
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid #d0d5dd;
            text-decoration: none;
            color: #344054;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .logout-btn:hover { background: #f9fafb; color: #101828; }

        /* ================================================================
           FLASH MESSAGES
        ================================================================ */
        /* Original style (tetap dipertahankan) */
        .flash-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .flash-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* ================================================================
           PAGE HEADER
        ================================================================ */
        .page-title    { font-size: 24px; font-weight: 600; color: #101828; margin-bottom: 4px; }
        .page-subtitle { color: #475467; font-size: 14px; margin-bottom: 24px; }

        /* ================================================================
           CARDS
        ================================================================ */
        /* Original dashboard cards */
        .custom-card        { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: 100%; }
        .card-border-blue   { border: 2px solid #e0ebff; }
        .card-border-yellow { border: 2px solid #fff3cd; }
        .card-border-orange { border: 2px solid #ffe5d0; }
        .card-border-green  { border: 2px solid #d1f2e2; }

        /* Engineer stat card */
        .stat-card {
            background: white;
            border: 1px solid #eaecf0;
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-title { font-size: 14px; color: #475467; font-weight: 500; margin-bottom: 8px; }
        .stat-value { font-size: 30px; font-weight: 600; color: #101828; }
        .stat-label { font-size: 14px; color: #475467; }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Content card */
        .content-card {
            background: white;
            border: 1px solid #eaecf0;
            border-radius: 12px;
            padding: 24px;
            margin-top: 24px;
        }

        /* ================================================================
           ICON BOXES
        ================================================================ */
        .icon-box    { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 15px; }

        /* Original colors */
        .icon-blue   { background: #eff5ff; color: #0d6efd; }
        .icon-yellow { background: #fffdf0; color: #ffc107; }
        .icon-orange { background: #fff6f0; color: #fd7e14; }
        .icon-green  { background: #f0fdf4; color: #198754; }

        /* Engineer colors */
        .bg-icon-blue   { background-color: #EFF8FF; color: #2F6BFF; }
        .bg-icon-orange { background-color: #FFFAEB; color: #B54708; }
        .bg-icon-green  { background-color: #ECFDF3; color: #027A48; }

        /* ================================================================
           TABLE
        ================================================================ */
        .table thead th {
            font-size: 12px;
            color: #475467;
            font-weight: 600;
            background-color: #F9FAFB;
            border-bottom: 1px solid #eaecf0;
            padding: 12px 16px;
            text-transform: uppercase;
        }

        .table tbody td {
            padding: 16px 16px;
            color: #101828;
            font-size: 14px;
            vertical-align: middle;
            border-bottom: 1px solid #eaecf0;
        }

        .clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .clickable-row:hover { background-color: #F9FAFB; }

        /* ================================================================
           BADGES & STATUS
        ================================================================ */
        .status-badge  { padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500; background-color: #F2F4F7; color: #344054; }
        .status-blue   { background-color: #EFF8FF; color: #2F6BFF; }
        .status-yellow { background-color: #FFFAEB; color: #B54708; }

        .badge-code {
            background-color: #EFF8FF;
            color: #2F6BFF;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        .status-pill { padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500; }
        .status-act  { background-color: #FFFAEB; color: #B54708; }
        .status-done { background-color: #ECFDF3; color: #027A48; }

        /* ================================================================
           LINKS & ACTIONS
        ================================================================ */
        .request-id              { color: #2F6BFF; font-weight: 500; text-decoration: none; }
        .action-link             { color: #2F6BFF; text-decoration: none; font-weight: 500; }
        .action-link:hover       { text-decoration: underline; }
        .btn-add-parts-link      { color: #2F6BFF; text-decoration: none; font-weight: 500; cursor: pointer; }
        .btn-add-parts-link:hover{ text-decoration: underline; }

        .back-link {
            color: #475467;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            cursor: pointer;
        }
        .back-link:hover { color: #2F6BFF; }

        /* ================================================================
           DETAIL VIEW
        ================================================================ */
        .detail-label { font-size: 12px; font-weight: 600; color: #475467; text-transform: uppercase; margin-bottom: 8px; }
        .detail-value { font-size: 14px; color: #101828; font-weight: 400; }

        /* ================================================================
           CUSTOM ALERTS
        ================================================================ */
        .alert-info-custom {
            background-color: #EFF8FF;
            border: 1px solid #B2DDFF;
            color: #175CD3;
            border-radius: 12px;
            font-size: 14px;
        }

        .alert-warning-custom {
            background-color: #FFFAEB;
            border: 1px solid #FEDF89;
            color: #B54708;
            border-radius: 12px;
            font-size: 14px;
        }

        /* ================================================================
           BUTTONS
        ================================================================ */
        .btn-primary-custom        { background-color: #2F6BFF; border-color: #2F6BFF; color: white; font-weight: 500; }
        .btn-primary-custom:hover  { background-color: #1e5afa; color: white; }
        .btn-success-custom        { background-color: #12B76A; border-color: #12B76A; color: white; font-weight: 500; }
        .btn-success-custom:hover  { background-color: #0e9f5d; color: white; }
        .btn-outline-custom        { border: 1px solid #d0d5dd; color: #344054; background: white; font-weight: 500; }

        /* ================================================================
           FORMS
        ================================================================ */
        .form-label    { font-weight: 500; font-size: 14px; color: #344054; }
        .form-control,
        .form-select   { border-radius: 8px; border: 1px solid #d0d5dd; padding: 10px 14px; font-size: 14px; }
        .required-star { color: #F04438; margin-left: 4px; }

        /* ================================================================
           ACTION ICONS
        ================================================================ */
        .action-icon-btn       { border: none; background: none; padding: 4px 8px; cursor: pointer; transition: opacity 0.2s; }
        .edit-icon             { color: #2F6BFF; }
        .view-icon             { color: #12B76A; }
        .delete-icon           { color: #F04438; }
        .action-icon-btn:hover { opacity: 0.7; }

        /* ================================================================
           TOAST
        ================================================================ */
        .toast-container { z-index: 1060; }
        .custom-toast    { border-left: 4px solid #12B76A; }

        /* ================================================================
           VIEW SWITCHING
        ================================================================ */
        .view-section        { display: none; }
        .view-section.active { display: block; }

        /* ================================================================
           DASHBOARD — CHART & TIMELINE (original)
        ================================================================ */
        .chart-toggle-btn        { border: 1px solid #eee; background: #f8f9fa; color: #6c757d; font-size: 13px; padding: 5px 15px; }
        .chart-toggle-btn.active { background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-weight: 600; color: #0d6efd; border-radius: 6px; }
        .legend-item             { background: #fff; border: 1px solid #eee; padding: 10px 15px; border-radius: 10px; text-align: center; width: 80px; cursor: pointer; transition: 0.2s; }
        .legend-item:hover       { border-color: #0d6efd; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .dot                     { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .activity-feed           { background: #fffdfa; }
        .timeline                { list-style: none; padding-left: 10px; }
        .timeline-item           { position: relative; padding-left: 25px; padding-bottom: 20px; display: flex; justify-content: space-between; cursor: pointer; transition: 0.2s; border-radius: 8px; }
        .timeline-item:hover     { background-color: #f8f9fa; padding-right: 10px; }
        .timeline-item::before   { content: ''; position: absolute; left: 0; top: 6px; width: 8px; height: 8px; border-radius: 50%; }
        .timeline-item.dot-blue::before   { background-color: #0d6efd; }
        .timeline-item.dot-green::before  { background-color: #20c997; }
        .timeline-item.dot-grey::before   { background-color: #adb5bd; }
        .timeline-item.dot-yellow::before { background-color: #ffc107; }
        .time-text { font-size: 12px; color: #adb5bd; }
            /* ================================================================
   PROFILE GLOBAL FIX
================================================================ */
.profile-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding-bottom: 40px;
}

/* Card konsisten */
.edit-card,
.info-card,
.banner-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 16px;
}

/* Banner */
.banner-img-placeholder {
    width: 100%;
    height: 140px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
}

/* Avatar */
.avatar-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-initials {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 24px;
}

/* Button */
.btn-update {
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-weight: 600;
}

.btn-update:hover {
    background: #2563eb;
}
    </style>

    {{-- ✅ CSS stack dari child views --}}
    @stack('styles')
</head>
<body>

{{-- Overlay mobile sidebar --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- SIDEBAR --}}
@include('layouts.sidebar')

<div class="main">

    {{-- TOPBAR --}}
    @include('layouts.topbar')

    <div class="content">

       @if(session('success'))
    <div class="flash-success">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flash-error">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

        {{-- Konten dari masing-masing halaman --}}
        @yield('content')

    </div>
</div>

{{-- ✅ Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- ✅ Script sidebar mobile --}}
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function openSidebar() {
        document.querySelector('.sidebar').classList.add('show');
        document.getElementById('sidebarOverlay').style.display = 'block';
    }

    function closeSidebar() {
        document.querySelector('.sidebar').classList.remove('show');
        document.getElementById('sidebarOverlay').style.display = 'none';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSidebar();
    });

    function previewImage(input, targetId) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById(targetId).src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>

{{-- ✅ Script dari child views --}}
@stack('scripts')
    
</body>
</html>