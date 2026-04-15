<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-pt-sumber-prima-mandiri.png') }}" alt="logo">
            <div>
                <div class="sidebar-title">PT Sumber Prima Mandiri</div>
                <div class="sidebar-sub">Production System</div>
            </div>
        </div>
    </div>

    <div class="menu">

        @php $role = auth()->user()->role; @endphp

        {{-- ── MENU ADMIN ── --}}
        @if($role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="{{ route('admin.permintaan.index') }}"
               class="{{ request()->routeIs('admin.permintaan.*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Request Management
            </a>
            <a href="{{ route('admin.planning.index') }}"
               class="{{ request()->routeIs('admin.planning.*') ? 'active' : '' }}">
                <i class="bi bi-table"></i> Production Planning
            </a>
            {{-- 
            <a href="{{ route('admin.master.index') }}"
   class="{{ request()->routeIs('admin.master.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Master Schedule
</a>
            <a href="{{ route('admin.users.index') }}"
               class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i> User Management
            </a>--}}

        {{-- ── MENU ENGINEER ── --}}
        @elseif($role === 'engineer')
            <a href="{{ route('engineer.dashboard') }}"
               class="{{ request()->routeIs('engineer.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="{{ route('engineer.request') }}"
               class="{{ request()->routeIs('engineer.request') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Request Management
            </a>
            <a href="{{ route('engineer.master') }}"
               class="{{ request()->routeIs('engineer.master') ? 'active' : '' }}">
                <i class="bi bi-table"></i> Master Schedule
            </a>

        {{-- ── MENU OPERATOR ── --}}
        @elseif($role === 'operator')
            <a href="{{ route('operator.dashboard') }}"
               class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Dashboard
            </a>
            <a href="{{ route('operator.parts') }}"
               class="{{ request()->routeIs('operator.parts') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Request Management
            </a>
            <a href="{{ route('operator.master') }}"
               class="{{ request()->routeIs('operator.master') ? 'active' : '' }}">
                <i class="bi bi-table"></i> Master Schedule
            </a>
            <a href="{{ route('operator.schedule.index') }}"
               class="{{ request()->routeIs('operator.schedule.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Schedule MFG
            </a>
        @endif

        <hr>

        {{-- Menu umum semua role --}}
        <a href="{{ route('profile') }}"
           class="{{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="bi bi-person"></i> Profil Saya
        </a>
        <a href="{{ route('logout') }}" class="text-danger"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

    </div>
</div>