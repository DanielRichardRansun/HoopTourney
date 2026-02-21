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
                <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Global Competitions Overview</h2>
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
                <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Global Statistic Teams</h2>
            </div>

            <!-- Top Team by PER (Full width row) -->
            <div class="bg-[#221914]/80 backdrop-blur-md border border-[#f48c25]/30 rounded-2xl overflow-hidden shadow-[0_4px_20px_-10px_rgba(244,140,37,0.3)] flex flex-col">
                <div class="bg-gradient-to-r from-[#f48c25]/20 to-[#181411] p-4 border-b border-[#f48c25]/30">
                    <h3 class="text-lg font-black text-[#f48c25] uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">monitoring</span>
                        Top Teams by PER
                    </h3>
                </div>
                <div class="p-4 flex-grow grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach($topTeamPer as $team)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#181411] border border-[#393028] hover:border-[#f48c25]/50 transition-colors">
                            <div class="size-10 rounded-full bg-[#221914] overflow-hidden flex-shrink-0 border border-[#393028] flex items-center justify-center">
                                @if($team->logo)
                                    <img src="{{ asset('images/logos/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-600">shield</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-bold text-sm uppercase italic truncate w-full">{{ $team->name }}</p>
                                <p class="text-[#f48c25] font-black text-lg">{{ number_format($team->avg_per, 1) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2x2 Team Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Team PPG -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-emerald-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">local_fire_department</span>
                            Top Scorers (PPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topTeamPts as $team)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($team->logo)
                                            <img src="{{ asset('images/logos/' . $team->logo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="text-slate-100 font-bold uppercase italic truncate w-32 md:w-48">{{ $team->name }}</p>
                                </div>
                                <span class="font-black text-emerald-400 text-lg">{{ number_format($team->avg_points, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Team AST -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-blue-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-blue-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">sports_handball</span>
                            Top Assists (APG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topTeamAst as $team)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($team->logo)
                                            <img src="{{ asset('images/logos/' . $team->logo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="text-slate-100 font-bold uppercase italic truncate w-32 md:w-48">{{ $team->name }}</p>
                                </div>
                                <span class="font-black text-blue-400 text-lg">{{ number_format($team->avg_assists, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Team REB -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-purple-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-purple-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">height</span>
                            Top Rebounds (RPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topTeamReb as $team)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($team->logo)
                                            <img src="{{ asset('images/logos/' . $team->logo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="text-slate-100 font-bold uppercase italic truncate w-32 md:w-48">{{ $team->name }}</p>
                                </div>
                                <span class="font-black text-purple-400 text-lg">{{ number_format($team->avg_rebounds, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Team Steals -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-red-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-red-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">front_hand</span>
                            Top Steals (SPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topTeamStl as $team)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($team->logo)
                                            <img src="{{ asset('images/logos/' . $team->logo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="text-slate-100 font-bold uppercase italic truncate w-32 md:w-48">{{ $team->name }}</p>
                                </div>
                                <span class="font-black text-red-400 text-lg">{{ number_format($team->avg_steals, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: PLAYER STATISTICS -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-[#393028] pb-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl text-emerald-500">star</span>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-100 uppercase italic tracking-wide">Global Statistic Player</h2>
                </div>
            </div>

            <!-- Top Players by PER (Full width row) -->
            <div class="bg-[#221914]/80 backdrop-blur-md border border-[#f48c25]/30 rounded-2xl overflow-hidden shadow-[0_4px_20px_-10px_rgba(244,140,37,0.3)] flex flex-col">
                <div class="bg-gradient-to-r from-[#f48c25]/20 to-[#181411] p-4 border-b border-[#f48c25]/30">
                    <h3 class="text-lg font-black text-[#f48c25] uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">monitoring</span>
                        Top Players by PER
                    </h3>
                </div>
                <div class="p-4 flex-grow grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach($topPer as $index => $player)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#181411] border border-[#393028] hover:border-[#f48c25]/50 transition-colors">
                            <span class="text-slate-500 font-bold">{{ $index + 1 }}.</span>
                            <div class="size-10 rounded-full bg-[#221914] overflow-hidden flex-shrink-0 border border-[#393028] flex items-center justify-center">
                                @if($player->photo)
                                    <img src="{{ asset('images/profiles/' . $player->photo) }}" alt="{{ $player->player_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-600">person</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-bold text-sm truncate w-full">{{ $player->player_name }}</p>
                                <p class="text-[#f48c25] font-black text-lg">{{ number_format($player->avg_per, 1) }}</p>
                            </div>
                        </div>
                    @endforeach
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
                                <div class="flex items-center gap-4 min-w-0">
                                    <span class="text-slate-500 font-bold w-4 flex-shrink-0">{{ $index + 1 }}.</span>
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($player->photo)
                                            <img src="{{ asset('images/profiles/' . $player->photo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-100 font-bold truncate w-24 md:w-40">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500 truncate w-24 md:w-40">{{ $player->team_name }}</p>
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
                                <div class="flex items-center gap-4 min-w-0">
                                    <span class="text-slate-500 font-bold w-4 flex-shrink-0">{{ $index + 1 }}.</span>
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($player->photo)
                                            <img src="{{ asset('images/profiles/' . $player->photo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-100 font-bold truncate w-24 md:w-40">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500 truncate w-24 md:w-40">{{ $player->team_name }}</p>
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
                                <div class="flex items-center gap-4 min-w-0">
                                    <span class="text-slate-500 font-bold w-4 flex-shrink-0">{{ $index + 1 }}.</span>
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($player->photo)
                                            <img src="{{ asset('images/profiles/' . $player->photo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-100 font-bold truncate w-24 md:w-40">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500 truncate w-24 md:w-40">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-purple-400 text-lg">{{ number_format($player->avg_rebounds, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Steals Card -->
                <div class="bg-[#221914]/80 backdrop-blur-md border border-[#393028] rounded-2xl overflow-hidden flex flex-col">
                    <div class="bg-gradient-to-r from-red-500/20 to-[#181411] p-4 border-b border-[#393028]">
                        <h3 class="text-lg font-black text-red-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">front_hand</span>
                            Top Steals (SPG)
                        </h3>
                    </div>
                    <div class="p-4 flex-grow">
                        @foreach($topSteals as $index => $player)
                            <div class="flex items-center justify-between py-3 border-b border-[#393028]/50 last:border-0">
                                <div class="flex items-center gap-4 min-w-0">
                                    <span class="text-slate-500 font-bold w-4 flex-shrink-0">{{ $index + 1 }}.</span>
                                    <div class="size-8 rounded-full bg-[#181411] flex items-center justify-center overflow-hidden flex-shrink-0 border border-[#393028]">
                                        @if($player->photo)
                                            <img src="{{ asset('images/profiles/' . $player->photo) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-100 font-bold truncate w-24 md:w-40">{{ $player->player_name }}</p>
                                        <p class="text-xs text-slate-500 truncate w-24 md:w-40">{{ $player->team_name }}</p>
                                    </div>
                                </div>
                                <span class="font-black text-red-400 text-lg">{{ number_format($player->avg_steals, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>
@endsection
