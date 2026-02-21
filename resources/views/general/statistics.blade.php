@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-black text-slate-100 italic uppercase tracking-tight mb-6">
                Global <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Statistics</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium">
                The ultimate numbers behind Hoop Tourney. Discover the top performing teams and players across the entire platform.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-16 space-y-16">
        
        <!-- SECTION 1: TOURNAMENT OVERVIEW -->
        <div class="space-y-6">
            <div class="flex items-center gap-3 border-b border-[#393028] pb-4">
                <span class="material-symbols-outlined text-3xl text-[#f48c25]">public</span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Platform Overview</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl p-6 text-center shadow-[0_4px_20px_-10px_rgba(0,0,0,0.5)]">
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-2">Tournaments</p>
                    <p class="text-4xl font-black text-slate-100">{{ $overviewStats['total_tournaments'] }}</p>
                </div>
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl p-6 text-center shadow-[0_4px_20px_-10px_rgba(0,0,0,0.5)]">
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-2">Active Now</p>
                    <p class="text-4xl font-black text-[#f48c25]">{{ $overviewStats['active_tournaments'] }}</p>
                </div>
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl p-6 text-center shadow-[0_4px_20px_-10px_rgba(0,0,0,0.5)]">
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-2">Total Teams</p>
                    <p class="text-4xl font-black text-slate-100">{{ $overviewStats['total_teams'] }}</p>
                </div>
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl p-6 text-center shadow-[0_4px_20px_-10px_rgba(0,0,0,0.5)]">
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-2">Total Players</p>
                    <p class="text-4xl font-black text-slate-100">{{ $overviewStats['total_players'] }}</p>
                </div>
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl p-6 text-center shadow-[0_4px_20px_-10px_rgba(0,0,0,0.5)]">
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mb-2">Matches Played</p>
                    <p class="text-4xl font-black text-slate-100">{{ $overviewStats['total_matches'] }}</p>
                </div>
            </div>
        </div>

        <!-- SECTION 2: TEAM STATISTICS -->
        <div class="space-y-6">
            <div class="flex items-center gap-3 border-b border-[#393028] pb-4">
                <span class="material-symbols-outlined text-3xl text-amber-500">shield</span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Top Teams by PER</h2>
            </div>
            
            <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden shadow-[0_10px_30px_-15px_rgba(0,0,0,0.6)]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#181411] text-slate-500 text-[11px] uppercase tracking-widest font-bold">
                                <th class="py-5 px-6 font-semibold">Rank</th>
                                <th class="py-5 px-6 font-semibold">Team</th>
                                <th class="py-5 px-6 font-semibold text-center">Avg PER</th>
                                <th class="py-5 px-6 font-semibold text-center hidden md:table-cell">PPG</th>
                                <th class="py-5 px-6 font-semibold text-center hidden md:table-cell">APG</th>
                                <th class="py-5 px-6 font-semibold text-center hidden lg:table-cell">RPG</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium transition-colors">
                            @foreach ($topTeams as $index => $team)
                                @php
                                    $rowClass = 'border-b border-[#393028]/50 hover:bg-[#2c221c] transition-colors';
                                    $rankClass = 'text-slate-400 font-black text-lg';
                                    
                                    if ($index === 0) {
                                        $rowClass = 'bg-gradient-to-r from-amber-500/10 to-transparent border-b border-amber-500/30 hover:bg-amber-500/20';
                                        $rankClass = 'text-amber-400 font-black text-2xl drop-shadow-[0_0_8px_rgba(251,191,36,0.6)]';
                                    } elseif ($index === 1) {
                                        $rowClass = 'bg-gradient-to-r from-slate-300/10 to-transparent border-b border-slate-300/30 hover:bg-slate-300/20';
                                        $rankClass = 'text-slate-300 font-black text-xl drop-shadow-[0_0_8px_rgba(203,213,225,0.4)]';
                                    } elseif ($index === 2) {
                                        $rowClass = 'bg-gradient-to-r from-amber-700/10 to-transparent border-b border-amber-700/30 hover:bg-amber-700/20';
                                        $rankClass = 'text-amber-600 font-black text-xl drop-shadow-[0_0_8px_rgba(180,83,9,0.4)]';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="py-4 px-6 {{ $rankClass }}">
                                        @if($index < 3)
                                            <span class="material-symbols-outlined align-middle mr-1">Trophy</span>
                                        @endif
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-100 uppercase italic">{{ $team->name }}</td>
                                    <td class="py-4 px-6 text-center text-[#f48c25] font-black text-lg">{{ number_format($team->avg_per, 1) }}</td>
                                    <td class="py-4 px-6 text-center text-slate-300 hidden md:table-cell">{{ number_format($team->avg_points, 1) }}</td>
                                    <td class="py-4 px-6 text-center text-slate-300 hidden md:table-cell">{{ number_format($team->avg_assists, 1) }}</td>
                                    <td class="py-4 px-6 text-center text-slate-300 hidden lg:table-cell">{{ number_format($team->avg_rebounds, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 3: PLAYER STATISTICS -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-[#393028] pb-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl text-emerald-500">star</span>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Player Leaderboards</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Points Card -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-emerald-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">local_fire_department</span>
                            Top Scorers (PPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topScorers as $index => $player)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4">
                                    <span class="text-slate-500 font-bold w-4">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-slate-100 font-bold">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-emerald-400 text-lg">{{ number_format($player->avg_points, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Assists Card -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-blue-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-blue-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">sports_handball</span>
                            Top Assists (APG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topAssists as $index => $player)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4">
                                    <span class="text-slate-500 font-bold w-4">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-slate-100 font-bold">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-blue-400 text-lg">{{ number_format($player->avg_assists, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Rebounds Card -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-purple-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-purple-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">height</span>
                            Top Rebounds (RPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topRebounds as $index => $player)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4">
                                    <span class="text-slate-500 font-bold w-4">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-slate-100 font-bold">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-purple-400 text-lg">{{ number_format($player->avg_rebounds, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- PER Card (Overall Efficiency) -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#f48c25]/30 rounded-2xl overflow-hidden flex flex-col shadow-[0_4px_20px_-10px_rgba(244,140,37,0.3)]">
                    <div class="bg-gradient-to-r from-[#f48c25]/20 to-[#181411] p-4 border-b border-[#f48c25]/30">
                        <h3 class="text-lg font-black text-[#f48c25] uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">monitoring</span>
                            League MVPs (Avg PER)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topPer as $index => $player)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4">
                                    <span class="text-slate-500 font-bold w-4">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-slate-100 font-bold">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-[#f48c25] text-lg">{{ number_format($player->avg_per, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </section>
</main>
@endsection
