<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'HoopTourney'))</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f48c25",
                        "background-light": "#f8f7f5",
                        "background-dark": "#181411",
                        "card-dark": "#221914",
                        "card-hover": "#2c221c",
                        "table-header": "#2f261f",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
                },
            },
        }
    </script>

    <style>
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #181411; 
        }
        ::-webkit-scrollbar-thumb {
            background: #393028; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f48c25; 
        }

        .glass-panel {
            background: rgba(34, 25, 20, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .glow-text {
            text-shadow: 0 0 20px rgba(244, 140, 37, 0.3);
        }
        
        .gold-row {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.1) 0%, rgba(34, 25, 20, 0) 100%);
            border-left: 4px solid #FFD700;
        }
        .silver-row {
            background: linear-gradient(90deg, rgba(192, 192, 192, 0.1) 0%, rgba(34, 25, 20, 0) 100%);
            border-left: 4px solid #C0C0C0;
        }
        .bronze-row {
            background: linear-gradient(90deg, rgba(205, 127, 50, 0.1) 0%, rgba(34, 25, 20, 0) 100%);
            border-left: 4px solid #CD7F32;
        }

        /* Prevent Icon Text Flash (FOUC) */
        .material-symbols-outlined {
            display: inline-block;
            width: 1em;
            height: 1em;
            overflow: hidden;
            white-space: nowrap;
            font-display: block;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display selection:bg-primary selection:text-white overflow-x-hidden flex flex-col min-h-screen">
    
    <!-- Header / Nav -->
    <header class="sticky top-0 z-50 flex flex-wrap items-center justify-between border-b border-solid border-[#393028] bg-[#181411]/95 backdrop-blur-md px-6 py-3 lg:px-10">
        <div class="flex items-center gap-8">
            <a href="{{ url('/') }}" class="flex items-center gap-4 text-white hover:opacity-80 transition-opacity whitespace-nowrap">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="size-10 object-contain">
                <h2 class="text-white text-xl font-black leading-tight tracking-tight uppercase italic md:block hidden">HOOP TOURNEY</h2>
            </a>
            
            <div class="hidden md:flex items-center gap-6 lg:gap-9">
                <a class="{{ request()->routeIs('tournaments.global') ? 'text-primary border-b-2 border-primary -mb-0.5 pb-0.5' : 'text-slate-300 hover:text-primary' }} transition-colors text-sm font-bold leading-normal relative" href="{{ route('tournaments.global') }}">Tournaments</a>
                <a class="{{ request()->routeIs('teams.global') ? 'text-primary border-b-2 border-primary -mb-0.5 pb-0.5' : 'text-slate-300 hover:text-primary' }} transition-colors text-sm font-bold leading-normal relative" href="{{ route('teams.global') }}">Teams</a>
                <a class="{{ request()->routeIs('players.global') ? 'text-primary border-b-2 border-primary -mb-0.5 pb-0.5' : 'text-slate-300 hover:text-primary' }} transition-colors text-sm font-bold leading-normal relative" href="{{ route('players.global') }}">Players</a>
                <a class="{{ request()->routeIs('statistics.global') ? 'text-primary border-b-2 border-primary -mb-0.5 pb-0.5' : 'text-slate-300 hover:text-primary' }} transition-colors text-sm font-bold leading-normal relative" href="{{ route('statistics.global') }}">Statistics</a>
                @auth
                    <a class="{{ request()->routeIs('tournament.mine') ? 'bg-primary text-[#181411] shadow-[0_4px_15px_-5px_rgba(244,140,37,0.5)]' : 'bg-primary/10 text-white shadow-[0_4px_15px_-5px_rgba(244,140,37,0.3)]' }} hover:bg-primary hover:text-[#181411] border border-primary/50 transition-all text-[11px] font-black uppercase tracking-widest px-4 py-2 rounded-full" href="{{ route('tournament.mine') }}">
                        My Tourneys
                    </a>
                @endauth
            </div>
        </div>
        
        <div class="flex flex-1 justify-end gap-3 lg:gap-6 items-center">
            <div class="flex gap-3 whitespace-nowrap">
                @guest
                    <a href="{{ route('login') }}" class="flex items-center justify-center rounded-full h-10 px-4 lg:px-6 bg-[#2c221c] hover:bg-[#3a2e26] text-white text-sm font-bold leading-normal transition-colors border border-[#393028]">
                        <span class="truncate">Log In</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center justify-center rounded-full h-10 px-4 lg:px-6 bg-primary hover:bg-orange-600 text-[#181411] text-sm font-bold leading-normal transition-colors shadow-[0_0_15px_rgba(244,140,37,0.4)]">
                        <span class="truncate">Join League</span>
                    </a>
                @else
                    <div class="flex items-center gap-4">
                        <span class="hidden lg:block text-slate-300 text-sm font-medium">Hello, {{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline border-l border-[#393028] pl-4">
                            @csrf
                            <button type="submit" class="flex items-center justify-center rounded-full h-10 px-6 bg-[#2c221c] hover:bg-[#3a2e26] text-white text-sm font-bold leading-normal transition-colors border border-[#393028]">
                                Log Out
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
        
        <!-- Mobile Menu Navigation (Visible only on small screens) -->
        <div class="w-full flex md:hidden items-center justify-between border-t border-[#393028] mt-3 pt-3 overflow-x-auto gap-4 pb-2">
            <a class="text-slate-300 hover:text-primary transition-colors text-xs font-bold uppercase tracking-wider" href="{{ route('tournaments.global') }}">Tournaments</a>
            <a class="text-slate-300 hover:text-primary transition-colors text-xs font-bold uppercase tracking-wider" href="{{ route('teams.global') }}">Teams</a>
            <a class="text-slate-300 hover:text-primary transition-colors text-xs font-bold uppercase tracking-wider" href="{{ route('players.global') }}">Players</a>
            <a class="text-slate-300 hover:text-primary transition-colors text-xs font-bold uppercase tracking-wider" href="{{ route('statistics.global') }}">Stats</a>
            @auth
                <a class="{{ request()->routeIs('tournament.mine') ? 'bg-primary text-[#181411]' : 'bg-primary/20 text-white' }} border border-primary/50 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap" href="{{ route('tournament.mine') }}">My Tourneys</a>
            @endauth
        </div>
    </header>

    <!-- Main Content Dynamic Injection -->
    @yield('content')

    <footer class="mt-auto border-t border-[#393028] bg-[#181411] py-8 w-full z-10">
        <div class="container mx-auto px-6 lg:px-10 flex flex-col items-center justify-center gap-6">
            <div class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 object-contain opacity-50">
                <span class="text-slate-500 font-bold text-sm">© {{ date('Y') }} HOOP TOURNEY. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Dedicated scripts stack for pages that need custom JS -->
    @stack('scripts')
</body>
</html>
