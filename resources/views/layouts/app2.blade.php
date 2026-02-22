<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8" />
    <title>HOOPTOURNEY | Tournament Dashboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Tailwind CSS (CDN for quick prototyping, ideally compiled via build step) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#f48c25',
                        background: '#181411',
                    },
                    fontFamily: {
                        sans: ['Lexend', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Default Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .glass-panel {
            background: rgba(34, 25, 20, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="bg-background text-slate-300 font-sans antialiased overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between p-4 bg-[#221914] border-b border-[#393028] fixed top-0 w-full z-50">
        <a href="{{ route('welcome') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="HoopTourney" class="h-8 object-contain">
        </a>
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-300 focus:outline-none focus:text-primary transition-colors">
            <span class="material-symbols-outlined text-3xl" x-text="sidebarOpen ? 'close' : 'menu'"></span>
        </button>
    </div>

    <div class="flex h-screen overflow-hidden pt-[65px] md:pt-0">
        
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" 
             x-transition.opacity 
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm md:hidden" 
             @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-[#1c1613] border-r border-[#393028] flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 overflow-y-auto">
            
            <!-- Logo Area (Desktop) -->
            <div class="hidden md:flex items-center justify-center p-6 border-b border-[#393028]">
                <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="HoopTourney Logo" class="h-10 object-contain drop-shadow-[0_0_15px_rgba(244,140,37,0.3)] group-hover:scale-105 transition-transform duration-300">
                    <span class="font-black text-xl italic tracking-tight text-white uppercase group-hover:text-primary transition-colors">HOOP <span class="text-primary group-hover:text-white transition-colors">TOURNEY</span></span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-8 space-y-2">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-[#2c221c] transition-all mb-4">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-bold text-sm tracking-wide">Back to Home</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-black uppercase text-slate-500 tracking-widest">Tournament Menu</p>
                </div>

                @php
                    $tId = $tournament->id ?? request()->route('id') ?? request()->route('tournament_id') ?? 0;
                @endphp

                <!-- Overview Tournament -->
                <a href="{{ route('tournament.detail', $tId) }}" 
                   class="{{ request()->routeIs('tournament.detail') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">dashboard</span>
                    Overview
                </a>

                <!-- Bracket -->
                <a href="{{ route('tournament.bracket', $tId) }}" 
                   class="{{ request()->routeIs('tournament.bracket') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">account_tree</span>
                    Bracket
                </a>

                <!-- Teams -->
                <a href="{{ route('tournament.teams', $tId) }}" 
                   class="{{ request()->routeIs('tournament.teams') || request()->routeIs('teams.show') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">group</span>
                    Teams
                </a>

                <!-- Klasemen -->
                <a href="{{ route('tournament.klasemen', $tId) }}" 
                   class="{{ request()->routeIs('tournament.klasemen') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">leaderboard</span>
                    Standings
                </a>

                <!-- Jadwal -->
                <a href="{{ route('dashboard.jadwal', $tId) }}" 
                   class="{{ request()->routeIs('dashboard.jadwal') || request()->routeIs('matchResults.*') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">calendar_month</span>
                    Schedule
                </a>

                <!-- Statistik -->
                <a href="{{ route('statistik', $tId) }}" 
                   class="{{ request()->routeIs('statistik') ? 'bg-primary/10 text-primary border-r-4 border-primary' : 'text-slate-400 hover:text-white hover:bg-[#2c221c]' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm">
                    <span class="material-symbols-outlined">bar_chart</span>
                    Statistics
                </a>
            </nav>

            <!-- Bottom Area (Auth/Logout) -->
            <div class="p-4 border-t border-[#393028]">
                @auth
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:text-white hover:bg-red-500/20 transition-all font-bold text-sm tracking-wide border border-transparent hover:border-red-500/50">
                            <span class="material-symbols-outlined">logout</span>
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="flex-1 overflow-y-auto relative flex flex-col bg-[#181411]">
            
            <!-- Global Background Elements for Dashboard Area -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay pointer-events-none z-0"></div>
            <div class="absolute -top-32 -right-32 size-[500px] bg-primary rounded-full blur-[200px] opacity-5 pointer-events-none z-0"></div>

            <!-- Content Area -->
            <div class="flex-grow p-6 md:p-10 relative z-10 w-full max-w-[1600px] mx-auto">
                @yield('content')
            </div>

            <!-- Minimal Footer -->
            <footer class="p-6 border-t border-[#393028] bg-[#1c1613] text-center z-10">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">&copy; {{ date('Y') }} HOOPTOURNEY. ALL RIGHTS RESERVED.</p>
            </footer>

        </main>
    </div>

    <!-- Modals or Scripts specific to the dashboard -->
    @stack('scripts')
</body>
</html>