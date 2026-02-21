<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HoopTourney') }} - Welcome</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display selection:bg-primary selection:text-white overflow-x-hidden">
    <!-- Wrapper -->
    <div class="relative flex h-auto min-h-screen w-full flex-col">
        <!-- Header / Nav -->
        <header class="sticky top-0 z-50 flex items-center justify-between whitespace-nowrap border-b border-solid border-[#393028] bg-[#181411]/95 backdrop-blur-md px-6 py-3 lg:px-10">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-4 text-white hover:opacity-80 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="size-10 object-contain">
                    <h2 class="text-white text-xl font-black leading-tight tracking-tight uppercase italic">HOOP TOURNEY</h2>
                </a>
                <div class="hidden md:flex items-center gap-9">
                    <a class="text-slate-300 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Tournaments</a>
                    <a class="text-slate-300 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Teams</a>
                    <a class="text-slate-300 hover:text-primary transition-colors text-sm font-medium leading-normal" href="#">Players</a>
                    @auth
                        <a class="text-slate-300 hover:text-primary transition-colors text-sm font-medium leading-normal" href="{{ route('tournament.mine') }}">My Tourneys</a>
                    @endauth
                </div>
            </div>
            <div class="flex flex-1 justify-end gap-6 items-center">
                <label class="hidden lg:flex flex-col min-w-40 !h-10 max-w-64">
                    <div class="flex w-full flex-1 items-stretch rounded-full h-full border border-[#393028] bg-[#221914] overflow-hidden group focus-within:border-primary/50 transition-colors">
                        <div class="text-[#baab9c] flex bg-transparent items-center justify-center pl-4 pr-2">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                        </div>
                        <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden bg-transparent text-white focus:outline-0 focus:ring-0 border-none h-full placeholder:text-[#baab9c]/50 px-0 text-sm font-normal leading-normal" placeholder="Search players, teams..." value=""/>
                    </div>
                </label>
                <div class="flex gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-[#2c221c] hover:bg-[#3a2e26] text-white text-sm font-bold leading-normal transition-colors border border-[#393028]">
                            <span class="truncate">Log In</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-orange-600 text-[#181411] text-sm font-bold leading-normal transition-colors shadow-[0_0_15px_rgba(244,140,37,0.4)]">
                            <span class="truncate">Join League</span>
                        </a>
                    @else
                        <div class="flex items-center gap-4">
                            <span class="text-slate-300 text-sm font-medium">Hello, {{ Auth::user()->name }}</span>
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
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col items-center w-full px-4 py-8 md:px-10 lg:px-20 gap-10">
            <!-- Hero Banner -->
            <section class="w-full max-w-[1400px] rounded-2xl overflow-hidden relative min-h-[400px] flex items-center p-8 md:p-16 group">
                <!-- Background Image with Overlay -->
                <div class="absolute inset-0 bg-cover bg-center z-0 transition-transform duration-700 group-hover:scale-105" 
                     style="background-image: url('{{ asset('images/banner1.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-background-dark via-background-dark/80 to-transparent z-10"></div>
                <!-- Hero Content -->
                <div class="relative z-20 flex flex-col gap-6 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 border border-primary/30 w-fit backdrop-blur-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        <span class="text-primary text-xs font-bold uppercase tracking-wider">Season {{ date('Y') }} Live</span>
                    </div>
                    <h1 class="text-white text-5xl md:text-7xl font-black leading-[0.9] tracking-tighter uppercase italic glow-text">
                        Dominate <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-orange-300">The Court</span>
                    </h1>
                    <p class="text-slate-300 text-lg md:text-xl font-light max-w-md leading-relaxed">
                        Manage your dream team, track real-time stats, and climb the global leaderboards. The championship starts here.
                    </p>
                    <div class="flex flex-wrap gap-4 mt-2">
                        @auth
                            @if (Auth::user()->role != 2)
                                <a href="{{ route('tournament.create') }}" class="flex items-center gap-2 rounded-full h-14 px-8 bg-primary hover:bg-orange-500 text-[#181411] text-base font-bold transition-all hover:scale-105 shadow-[0_0_20px_rgba(244,140,37,0.5)]">
                                    <span class="material-symbols-outlined">add_circle</span>
                                    Buat Tourney
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="flex items-center gap-2 rounded-full h-14 px-8 bg-primary hover:bg-orange-500 text-[#181411] text-base font-bold transition-all hover:scale-105 shadow-[0_0_20px_rgba(244,140,37,0.5)]">
                                <span class="material-symbols-outlined">how_to_reg</span>
                                Register Now
                            </a>
                        @endauth
                        <button class="flex items-center gap-2 rounded-full h-14 px-8 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/10 text-base font-bold transition-all">
                            <span class="material-symbols-outlined">emoji_events</span>
                            All Competitions
                        </button>
                    </div>
                </div>
            </section>

            <!-- Dashboard Grid -->
            <div class="w-full max-w-[1400px] grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left Column: Tournaments & Stats (8 cols) -->
                <div class="lg:col-span-8 flex flex-col gap-8">
                    <!-- Section Header -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">emoji_events</span>
                            Live Tournaments
                        </h3>
                        <button class="text-sm text-primary hover:text-white transition-colors">View All</button>
                    </div>
                    
                    <!-- Tournament Cards -->
                    <div class="flex overflow-x-auto pb-4 gap-4 scrollbar-hide snap-x">
                        @forelse($tournaments as $tournament)
                            <div class="min-w-[300px] flex-1 glass-panel rounded-2xl p-5 hover:bg-[#2c221c] transition-colors cursor-pointer group snap-center border-l-4 {{ $tournament->status == 'ongoing' ? 'border-l-primary' : 'border-l-slate-700' }}"
                                 onclick="window.location='{{ route('tournament.detail', $tournament->id) }}'">
                                <div class="flex justify-between items-start mb-4">
                                    @php
                                        $statusClass = 'bg-slate-700/50 text-slate-300';
                                        if($tournament->status == 'ongoing') $statusClass = 'bg-red-500/20 text-red-500';
                                        elseif($tournament->status == 'upcoming') $statusClass = 'bg-primary/20 text-primary';
                                        elseif($tournament->status == 'completed') $statusClass = 'bg-slate-800 text-slate-400';
                                    @endphp
                                    <span class="{{ $statusClass }} text-xs font-bold px-2 py-1 rounded uppercase">
                                        {{ ucfirst($tournament->status) }}
                                    </span>
                                    <span class="text-slate-400 text-xs font-bold uppercase">{{ $tournament->organizer }}</span>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <h4 class="text-white font-bold text-lg truncate">{{ $tournament->name }}</h4>
                                    <div class="flex justify-between items-center text-xs text-slate-400 font-medium">
                                        <span>Starts: {{ \Carbon\Carbon::parse($tournament->start_date)->format('M d, Y') }}</span>
                                        @if(Auth::check() && Auth::user()->role == 2 && $tournament->status == 'upcoming')
                                            @php
                                                $isJoined = DB::table('team_tournament')->where('team_id', Auth::user()->team_id)->where('tournament_id', $tournament->id)->exists();
                                            @endphp
                                            @if($isJoined)
                                                <span class="text-green-500">Joined!</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="w-full glass-panel rounded-2xl p-8 text-center text-slate-400">
                                No tournaments found.
                            </div>
                        @endforelse
                    </div>

                    <!-- Analytics Chart Section -->
                    <div class="glass-panel rounded-2xl p-6 border border-[#393028]">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-white">League Efficiency Rating (PER)</h3>
                                <p class="text-slate-400 text-sm">Top Players vs League Average</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="p-2 rounded-lg bg-[#2c221c] hover:bg-[#3a2e26] text-slate-300 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">bar_chart</span>
                                </button>
                            </div>
                        </div>
                        <div class="relative w-full h-[300px]">
                            <canvas id="perChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Leaderboard (4 cols) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-yellow-500">leaderboard</span>
                            Top Players
                        </h3>
                    </div>
                    
                    @php
                        $topPlayers = DB::table('player_stats')
                            ->select(
                                'players.id',
                                'players.name as player_name',
                                'teams.name as team_name',
                                DB::raw('ROUND(AVG(player_stats.point), 1) as avg_points'),
                                DB::raw('ROUND(AVG(player_stats.per), 1) as avg_per')
                            )
                            ->join('players', 'player_stats.players_id', '=', 'players.id')
                            ->join('teams', 'players.teams_id', '=', 'teams.id')
                            ->groupBy('players.id', 'players.name', 'teams.name')
                            ->orderBy('avg_per', 'DESC')
                            ->limit(5)
                            ->get();
                    @endphp

                    <div class="glass-panel rounded-2xl overflow-hidden border border-[#393028]">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#2c221c] border-b border-[#393028]">
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Rank</th>
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Player</th>
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">PPG</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2f261f]">
                                @foreach($topPlayers as $index => $player)
                                    @php
                                        $rowClass = '';
                                        $rankClass = 'text-slate-500';
                                        if($index == 0) { $rowClass = 'gold-row'; $rankClass = 'bg-yellow-500/20 text-yellow-500'; }
                                        elseif($index == 1) { $rowClass = 'silver-row'; $rankClass = 'bg-slate-400/20 text-slate-300'; }
                                        elseif($index == 2) { $rowClass = 'bronze-row'; $rankClass = 'bg-orange-700/20 text-orange-400'; }
                                        else { $rankClass = 'bg-slate-800 text-slate-500'; }
                                    @endphp
                                    <tr class="{{ $rowClass }} group hover:bg-white/5 transition-colors">
                                        <td class="p-4">
                                            <div class="size-6 rounded-full flex items-center justify-center text-xs font-bold {{ $rankClass }}">
                                                {{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-full bg-slate-700 flex items-center justify-center text-[10px] font-bold text-white">
                                                    @php
                                                        $initials = collect(explode(' ', $player->player_name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                                                    @endphp
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="text-white text-sm font-bold">{{ $player->player_name }}</div>
                                                    <div class="text-slate-500 text-xs">{{ $player->team_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <span class="{{ $index < 3 ? 'text-primary' : 'text-slate-400' }} font-black text-lg">{{ $player->avg_points }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-3 bg-[#2c221c] text-center border-t border-[#393028]">
                            <button class="text-xs font-bold text-primary hover:text-white uppercase tracking-wider transition-colors">View Full Leaderboard</button>
                        </div>
                    </div>

                    <!-- Highlight Stat Card -->
                    <div class="glass-panel rounded-2xl p-5 border border-primary/20 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/20 rounded-full blur-2xl group-hover:bg-primary/30 transition-all"></div>
                        <h4 class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Tournament Focus</h4>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">{{ $tournaments->count() }}</span>
                            <span class="text-sm font-medium text-slate-400 mb-2">Active {{ Str::plural('Tournament', $tournaments->count()) }}</span>
                        </div>
                        <p class="text-sm text-primary mt-1 font-medium">Join the action today!</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="w-full max-w-[1400px] mt-10 border-t border-[#393028] pt-8 pb-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2 text-slate-500">
                    <span class="material-symbols-outlined text-[20px]">sports_basketball</span>
                    <span class="text-sm font-bold">HOOP TOURNEY © {{ date('Y') }}</span>
                </div>
                <div class="flex gap-6">
                    <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Privacy Policy</a>
                    <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Terms of Service</a>
                    <a class="text-sm text-slate-500 hover:text-primary transition-colors" href="#">Support</a>
                </div>
            </footer>
        </main>
    </div>

    <!-- Chart Configuration Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('perChart').getContext('2d');
            
            // Create gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(244, 140, 37, 0.9)');
            gradient.addColorStop(1, 'rgba(244, 140, 37, 0.2)');

            const playerNames = {!! json_encode($topPlayers->map(fn($p) => $p->player_name)) !!};
            const perData = {!! json_encode($topPlayers->map(fn($p) => (float) $p->avg_per)) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: playerNames,
                    datasets: [{
                        label: 'Player Efficiency Rating',
                        data: perData,
                        backgroundColor: gradient,
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 30,
                    },
                    {
                        label: 'League Avg',
                        data: Array(playerNames.length).fill(15),
                        type: 'line',
                        borderColor: '#64748b',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        pointRadius: 0,
                        order: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#221914',
                            titleColor: '#fff',
                            bodyColor: '#cbd5e1',
                            borderColor: '#393028',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            titleFont: { family: 'Lexend', size: 14 },
                            bodyFont: { family: 'Lexend', size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { family: 'Lexend' } },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#e2e8f0', font: { family: 'Lexend', weight: 'bold' } },
                            border: { display: false }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
</body>
</html>