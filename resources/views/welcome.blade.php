@extends('layouts.general')

@section('content')
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
                        <a href="{{ route('tournaments.global') }}" class="flex items-center gap-2 rounded-full h-14 px-8 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/10 text-base font-bold transition-all">
                            <span class="material-symbols-outlined">emoji_events</span>
                            All Competitions
                        </a>
                    </div>
                </div>
            </section>

            <!-- Live Tournaments (Full Width Section) -->
            <div class="w-full max-w-[1400px] flex flex-col gap-6">
                <!-- Section Header -->
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">emoji_events</span>
                        Live Tournaments
                    </h3>
                    <a href="{{ route('tournaments.global') }}" class="text-sm text-primary hover:text-white transition-colors">View All</a>
                </div>
                
                <!-- Tournament Cards -->
                <div class="flex overflow-x-auto pb-4 gap-6 scrollbar-hide snap-x">
                    @forelse($tournaments as $tournament)
                        <div class="min-w-[320px] max-w-[400px] flex-1 glass-panel rounded-2xl p-5 hover:bg-[#2c221c] transition-all hover:-translate-y-1 cursor-pointer group snap-center border-l-4 {{ $tournament->status == 'ongoing' ? 'border-l-primary' : 'border-l-slate-700' }}"
                             onclick="window.location='{{ route('tournament.detail', $tournament->id) }}'">
                            
                            <div class="flex items-center gap-4 mb-4">
                                <!-- Tournament Logo -->
                                <div class="size-16 rounded-xl bg-[#181411] overflow-hidden border border-[#393028] flex-shrink-0 group-hover:border-primary/50 transition-colors">
                                    @if($tournament->logo)
                                        <img src="{{ asset('images/logos/' . $tournament->logo) }}" alt="{{ $tournament->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-primary bg-[#primary/10]">
                                            <span class="material-symbols-outlined text-[32px]">sports_basketball</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Title and Status -->
                                <div class="flex flex-col flex-grow overflow-hidden">
                                    <h4 class="text-white font-bold text-lg truncate mb-1 group-hover:text-primary transition-colors">{{ $tournament->name }}</h4>
                                    <div class="flex items-center justify-between w-full">
                                        @php
                                            $statusClass = 'bg-slate-700/50 text-slate-300';
                                            if($tournament->status == 'ongoing') $statusClass = 'bg-red-500/20 text-red-500';
                                            elseif($tournament->status == 'upcoming') $statusClass = 'bg-primary/20 text-primary';
                                            elseif($tournament->status == 'completed') $statusClass = 'bg-slate-800 text-slate-400';
                                        @endphp
                                        <span class="{{ $statusClass }} text-[10px] font-bold px-2 py-0.5 rounded uppercase flex items-center gap-1 w-fit">
                                            @if($tournament->status == 'ongoing')
                                                <span class="size-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            @endif
                                            {{ ucfirst($tournament->status) }}
                                        </span>
                                        
                                        @if(Auth::check() && Auth::user()->role == 2 && $tournament->status == 'upcoming')
                                            @php
                                                $isJoined = DB::table('team_tournament')->where('team_id', Auth::user()->team_id)->where('tournament_id', $tournament->id)->exists();
                                            @endphp
                                            @if($isJoined)
                                                <span class="text-green-500 text-xs font-bold">Joined!</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Swap Organizer and Date -->
                            <div class="flex flex-col gap-2 pt-4 border-t border-[#393028]">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 uppercase font-bold tracking-wider">Starts</span>
                                    <span class="text-slate-200 font-semibold">{{ \Carbon\Carbon::parse($tournament->start_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 uppercase font-bold tracking-wider">Organizer</span>
                                    <span class="text-slate-200 font-semibold truncate max-w-[150px] text-right" title="{{ $tournament->organizer }}">{{ $tournament->organizer }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="w-full glass-panel rounded-2xl p-8 text-center text-slate-400">
                            No tournaments found.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Lower Dashboard Grid -->
            <div class="w-full max-w-[1400px] grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left Column: Analytics Chart (7 cols) -->
                <div class="lg:col-span-7 flex flex-col gap-8">
                    <!-- Analytics Chart Section -->
                    <div class="glass-panel rounded-2xl p-6 border border-[#393028] flex-1 flex flex-col justify-between">
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
                        <div class="relative w-full flex-1 min-h-[300px]">
                            <canvas id="perChart"></canvas>
                        </div>
                        <div class="pt-4 mt-6 text-center border-t border-[#393028]">
                            <a href="{{ route('statistics.global') }}" class="inline-block text-xs font-bold text-primary hover:text-white uppercase tracking-wider transition-colors">View Full Statistics</a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Leaderboard & Top Teams (5 cols) -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    
                    <!-- Top Players -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-yellow-500">leaderboard</span>
                            Top Players
                        </h3>
                    </div>
                    
                    @php
                        $topPlayers = DB::table('player_stats')
                            ->select(
                                'players.id',
                                'players.name as player_name',
                                'players.photo',
                                'teams.name as team_name',
                                DB::raw('ROUND(AVG(player_stats.point), 1) as avg_points'),
                                DB::raw('ROUND(AVG(player_stats.per), 1) as avg_per')
                            )
                            ->join('players', 'player_stats.players_id', '=', 'players.id')
                            ->join('teams', 'players.teams_id', '=', 'teams.id')
                            ->groupBy('players.id', 'players.name', 'players.photo', 'teams.name')
                            ->orderBy('avg_per', 'DESC')
                            ->limit(3)
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
                                                <div class="size-10 rounded-full bg-[#181411] overflow-hidden border border-[#393028] flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                                    @if($player->photo)
                                                        <img src="{{ asset('images/profiles/' . $player->photo) }}" alt="{{ $player->player_name }}" class="w-full h-full object-cover">
                                                    @else
                                                        @php
                                                            $initials = collect(explode(' ', $player->player_name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                                                        @endphp
                                                        {{ $initials }}
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-white text-sm font-bold truncate">{{ $player->player_name }}</div>
                                                    <div class="text-slate-500 text-[11px] truncate">{{ $player->team_name }}</div>
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
                            <a href="{{ route('statistics.global') }}" class="inline-block w-full text-xs font-bold text-primary hover:text-white uppercase tracking-wider transition-colors">View Full Leaderboard</a>
                        </div>
                    </div>

                    <!-- Top Teams -->
                    <div class="flex items-center justify-between mt-4">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500">groups</span>
                            Top Teams
                        </h3>
                    </div>

                    @php
                        $topTeams = DB::table('teams')
                            ->select('teams.*', DB::raw('ROUND(AVG(player_stats.per), 1) as avg_per'))
                            ->join('players', 'teams.id', '=', 'players.teams_id')
                            ->join('player_stats', 'players.id', '=', 'player_stats.players_id')
                            ->groupBy('teams.id', 'teams.name', 'teams.coach', 'teams.manager', 'teams.logo', 'teams.created_at', 'teams.updated_at')
                            ->orderBy('avg_per', 'DESC')
                            ->limit(3)
                            ->get();
                    @endphp

                    <div class="glass-panel rounded-2xl overflow-hidden border border-[#393028]">
                        <div class="divide-y divide-[#2f261f]">
                            @foreach($topTeams as $index => $team)
                                <div class="p-4 flex items-center justify-between group hover:bg-[#2c221c] transition-colors cursor-pointer"
                                     onclick="window.location='{{ route('teams.global') }}'">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="text-slate-500 text-sm font-black w-3">{{ $index + 1 }}</div>
                                        <div class="size-10 rounded bg-[#181411] border border-[#393028] overflow-hidden flex-shrink-0 group-hover:border-emerald-500/50 transition-colors">
                                            @if($team->logo)
                                                <img src="{{ asset('images/logos/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-500 m-2">shield</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-slate-100 font-bold text-sm uppercase italic truncate">{{ $team->name }}</h4>
                                            <p class="text-slate-500 text-[11px] truncate">PER: <span class="text-emerald-500 font-bold">{{ number_format($team->avg_per, 1) }}</span></p>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-600 group-hover:text-emerald-500 transition-colors">chevron_right</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-3 bg-[#2c221c] text-center border-t border-[#393028]">
                            <a href="{{ route('teams.global') }}" class="inline-block w-full text-xs font-bold text-emerald-500 hover:text-white uppercase tracking-wider transition-colors">View All Teams</a>
                        </div>
                    </div>
                </div>
            </div>

@endsection
@push('scripts')
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
@endpush