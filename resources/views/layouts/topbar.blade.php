<div class="topbar">

    {{-- SEARCH --}}
    <div class="search-box">
        <i class="bi bi-search search-icon"></i>
        <input type="text" placeholder="Search request, machine, activity..." />
    </div>

    {{-- RIGHT --}}
    <div class="topbar-right">

        <div class="notification">
            <i class="bi bi-bell"></i>
        </div>

        <div class="user-card">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->nama }}</div>
                <div class="user-role">{{ auth()->user()->role_name }}</div>
            </div>
        </div>

        {{-- LOGOUT: gunakan form POST + @csrf 
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>--}}
        </form>

    </div>
</div>