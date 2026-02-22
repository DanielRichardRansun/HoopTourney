<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>HOOPTOURNEY | {{ $tournament->name ?? 'Dashboard' }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="MobileOptimized" content="320">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#f48c25' },
                    fontFamily: { lexend: ['Lexend', 'sans-serif'] }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Lexend', sans-serif; background-color: #181411; color: #f1f5f9; overflow-x: hidden; }
        .glass-panel { background: rgba(34, 25, 20, 0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid #393028; }

        /* Sidebar */
        .app-sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1310 0%, #181411 100%);
            border-right: 1px solid #393028;
            position: fixed;
            top: 0; left: 0;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }
        .app-main { margin-left: 260px; min-height: 100vh; transition: margin-left 0.3s ease; }

        /* Sidebar nav item */
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px;
            color: #94a3b8;
            font-size: 13px; font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            letter-spacing: 0.03em;
        }
        .nav-item:hover { background: rgba(244, 140, 37, 0.08); color: #f1f5f9; border-left-color: rgba(244, 140, 37, 0.3); }
        .nav-item.active { background: rgba(244, 140, 37, 0.12); color: #f48c25; border-left-color: #f48c25; font-weight: 700; }
        .nav-item .material-symbols-outlined { font-size: 20px; }

        /* Mobile */
        @media (max-width: 768px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); }
            .app-main { margin-left: 0; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 40; }
            .sidebar-overlay.active { display: block; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #181411; }
        ::-webkit-scrollbar-thumb { background: #393028; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f48c25; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Hamburger -->
    <button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-[60] size-10 rounded-xl bg-[#221914] border border-[#393028] text-primary flex items-center justify-center shadow-lg hover:bg-primary hover:text-[#181411] transition-all">
        <span class="material-symbols-outlined text-[20px]">menu</span>
    </button>

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- Logo & Tournament Info -->
        <div class="p-5 border-b border-[#393028]">
            <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 object-contain">
                <span class="text-white font-black text-sm uppercase tracking-wider group-hover:text-primary transition-colors">HoopTourney</span>
            </a>
            @if(isset($tournament))
                <div class="glass-panel rounded-xl p-3 mt-2">
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Tournament</p>
                    <h4 class="text-white font-bold text-sm leading-tight line-clamp-2">{{ $tournament->name }}</h4>
                    @if(isset($tournament->status))
                        @php
                            $sClass = 'bg-slate-700/50 text-slate-300';
                            $sIcon = 'radio_button_unchecked';
                            if($tournament->status == 'ongoing') { $sClass = 'bg-red-500/20 text-red-400 border border-red-500/30'; $sIcon = 'radio_button_checked'; }
                            elseif($tournament->status == 'upcoming') { $sClass = 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'; $sIcon = 'event_upcoming'; }
                            elseif($tournament->status == 'scheduled') { $sClass = 'bg-blue-500/20 text-blue-400 border border-blue-500/30'; $sIcon = 'calendar_month'; }
                            elseif($tournament->status == 'completed') { $sClass = 'bg-slate-800 text-slate-400 border border-slate-700'; $sIcon = 'check_circle'; }
                        @endphp
                        <span class="{{ $sClass }} text-[9px] font-black px-2 py-0.5 rounded uppercase inline-flex items-center gap-1 mt-2">
                            <span class="material-symbols-outlined text-[11px]">{{ $sIcon }}</span>
                            {{ ucfirst($tournament->status) }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 overflow-y-auto">
            <p class="px-5 text-[10px] text-slate-600 uppercase font-bold tracking-widest mb-3">Navigation</p>

            <a class="nav-item {{ Request::is('tournament/detail/*') ? 'active' : '' }}" href="{{ route('tournament.detail', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">info</span> Detail
            </a>
            <a class="nav-item {{ Request::is('dashboard/bracket/*') ? 'active' : '' }}" href="{{ route('tournament.bracket', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">account_tree</span> Bracket
            </a>
            <a class="nav-item {{ Request::is('tournament/*/teams*') || Request::is('tournament/*/teams/*') ? 'active' : '' }}" href="{{ route('tournament.teams', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">groups</span> Teams
            </a>
            <a class="nav-item {{ Request::is('dashboard/klasemen/*') ? 'active' : '' }}" href="{{ route('tournament.klasemen', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">emoji_events</span> Standings
            </a>
            <a class="nav-item {{ Request::is('dashboard/jadwal/*') ? 'active' : '' }}" href="{{ route('dashboard.jadwal', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">calendar_month</span> Schedule
            </a>
            <a class="nav-item {{ Request::is('dashboard/statistik/*') ? 'active' : '' }}" href="{{ route('statistik', $tournament->id ?? 0) }}">
                <span class="material-symbols-outlined">bar_chart</span> Statistics
            </a>
        </nav>

        <!-- Bottom Actions -->
        <div class="border-t border-[#393028] p-4 space-y-2">
            <a href="{{ url('/') }}" class="nav-item rounded-lg !border-l-0 !px-4 hover:!bg-primary/10">
                <span class="material-symbols-outlined">home</span> Back to Home
            </a>
            @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" class="nav-item rounded-lg !border-l-0 !px-4 hover:!bg-red-500/10 hover:!text-red-400">
                    <span class="material-symbols-outlined">logout</span> Logout
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endauth
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <div class="min-h-screen p-6 md:p-8 lg:p-10">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="border-t border-[#393028] bg-[#181411] py-6 px-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 object-contain opacity-40">
                <span class="text-slate-600 font-bold text-xs">© {{ date('Y') }} HOOP TOURNEY. All rights reserved.</span>
            </div>
        </footer>
    </main>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');

        if (toggle) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>