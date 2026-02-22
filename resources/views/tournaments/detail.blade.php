@extends('layouts.app2')

@section('title', 'Detail Lomba')

@section('content')
<div class="space-y-6 max-w-[1200px] mx-auto">

    <!-- Header & Action Buttons -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Overview</h1>
            <p class="text-slate-400 text-sm mt-1">Dashboard for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
        </div>
        
        <div class="flex gap-3">
            @if(auth()->id() === $tournament->users_id)
                <a href="{{ route('tournament.edit', $tournament->id) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#221914] border border-[#393028] text-primary hover:bg-primary/10 hover:border-primary/50 transition-all font-bold text-sm tracking-wide">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    Edit
                </a>

                @if($tournament->status === 'scheduled')
                    <button type="button" @click="$dispatch('open-generate-modal')" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-[0_4px_15px_-5px_rgba(16,185,129,0.5)] hover:scale-105 transition-all font-bold text-sm tracking-wide">
                        <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                        Generate Bracket
                    </button>
                @elseif($tournament->status === 'upcoming')
                    <button type="button" disabled class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#221914] border border-[#393028] text-slate-500 cursor-not-allowed font-bold text-sm tracking-wide" title="Change status to 'Scheduled' to generate brackets">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        Generate Bracket
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-400/50 hover:text-emerald-400 transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
        </div>
    @endif

    @if(auth()->id() === $tournament->users_id && $tournament->status === 'upcoming')
        <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-500">
            <span class="material-symbols-outlined mt-0.5">warning</span>
            <div>
                <p class="font-bold text-sm">Action Required</p>
                <p class="text-xs text-amber-500/80 mt-1">To generate brackets, ensure all teams are registered and change status to <strong>Scheduled</strong>.</p>
            </div>
        </div>
    @endif

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Teams -->
        <div class="glass-panel border border-[#393028] rounded-2xl p-5 relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="absolute -top-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[80px] text-primary">groups</span>
            </div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2">Total Teams</p>
            <p class="text-3xl font-black text-white tabular-nums">{{ $totalTeams }}</p>
        </div>
        <!-- Total Players -->
        <div class="glass-panel border border-[#393028] rounded-2xl p-5 relative overflow-hidden group hover:border-blue-500/30 transition-all">
            <div class="absolute -top-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[80px] text-blue-400">person</span>
            </div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2">Total Players</p>
            <p class="text-3xl font-black text-white tabular-nums">{{ $totalPlayers }}</p>
        </div>
        <!-- Matches Played -->
        <div class="glass-panel border border-[#393028] rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/30 transition-all">
            <div class="absolute -top-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[80px] text-emerald-400">check_circle</span>
            </div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2">Matches Played</p>
            <p class="text-3xl font-black text-white tabular-nums">{{ $matchesCompleted }} <span class="text-sm font-bold text-slate-500">/ {{ $totalMatches }}</span></p>
        </div>
        <!-- Matches Remaining -->
        <div class="glass-panel border border-[#393028] rounded-2xl p-5 relative overflow-hidden group hover:border-amber-500/30 transition-all">
            <div class="absolute -top-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-[80px] text-amber-400">pending</span>
            </div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-2">Remaining</p>
            <p class="text-3xl font-black text-white tabular-nums">{{ $matchesRemaining }}</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Col: Hero + Next Match -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hero Card -->
            <div class="glass-panel p-8 rounded-2xl border border-[#393028] relative overflow-hidden group">
                <div class="absolute -top-24 -right-24 size-64 bg-primary/10 rounded-full blur-3xl pointer-events-none group-hover:bg-primary/20 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="size-16 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center shadow-inner shrink-0">
                            @if(isset($tournament->logo) && $tournament->logo)
                                <img src="{{ asset('images/logos/' . $tournament->logo) }}" alt="{{ $tournament->name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <span class="material-symbols-outlined text-4xl text-primary">emoji_events</span>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-white leading-tight uppercase">{{ $tournament->name }}</h2>
                            <p class="text-primary font-bold tracking-widest text-xs mt-1 uppercase">
                                Organized by {{ $tournament->organizer }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-[#181411]/50 border border-[#393028] rounded-xl p-5 mt-4">
                        <h4 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[14px]">description</span>
                            Description
                        </h4>
                        <p class="text-slate-300 leading-relaxed text-sm whitespace-pre-line">{{ $tournament->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Next Match Widget -->
            <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden relative">
                <div class="px-6 py-4 border-b border-[#393028] bg-[#1c1613]/50">
                    <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] flex items-center gap-2">
                        <span class="material-symbols-outlined text-[14px] text-primary">schedule</span>
                        Next Match
                    </h4>
                </div>

                @if($allMatchesCompleted)
                    <!-- All Matches Completed -->
                    <div class="p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-amber-500/5 to-transparent pointer-events-none"></div>
                        <div class="relative z-10">
                            <span class="material-symbols-outlined text-5xl text-amber-500 mb-3 drop-shadow-lg">emoji_events</span>
                            <h3 class="text-xl font-black text-white uppercase tracking-tight mb-1">Tournament Complete!</h3>
                            <p class="text-slate-500 text-sm">All {{ $totalMatches }} matches have been played.</p>
                        </div>
                    </div>
                @elseif($nextMatch)
                    <!-- Next Match Display -->
                    <a href="{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $nextMatch->id]) }}" class="block p-6 hover:bg-white/[0.02] transition-colors group/match">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6 flex-1">
                                <!-- Team 1 -->
                                <div class="flex flex-col items-center gap-2 flex-1">
                                    <div class="size-14 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden group-hover/match:border-primary/40 transition-all">
                                        @if($nextMatch->team1 && $nextMatch->team1->logo)
                                            <img src="{{ asset('images/logos/' . $nextMatch->team1->logo) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-2xl text-slate-600">groups</span>
                                        @endif
                                    </div>
                                    <span class="text-white font-bold text-sm text-center truncate max-w-[120px]">{{ $nextMatch->team1->name ?? 'TBD' }}</span>
                                </div>

                                <!-- VS -->
                                <div class="flex flex-col items-center gap-1 shrink-0">
                                    <span class="text-2xl font-black text-primary italic">VS</span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                        {{ $nextMatch->date ? \Carbon\Carbon::parse($nextMatch->date)->format('d M Y') : 'Date TBD' }}
                                    </span>
                                </div>

                                <!-- Team 2 -->
                                <div class="flex flex-col items-center gap-2 flex-1">
                                    <div class="size-14 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden group-hover/match:border-primary/40 transition-all">
                                        @if($nextMatch->team2 && $nextMatch->team2->logo)
                                            <img src="{{ asset('images/logos/' . $nextMatch->team2->logo) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-2xl text-slate-600">groups</span>
                                        @endif
                                    </div>
                                    <span class="text-white font-bold text-sm text-center truncate max-w-[120px]">{{ $nextMatch->team2->name ?? 'TBD' }}</span>
                                </div>
                            </div>

                            <span class="material-symbols-outlined text-slate-600 group-hover/match:text-primary transition-colors ml-4">chevron_right</span>
                        </div>
                    </a>
                @else
                    <!-- No Matches Yet -->
                    <div class="p-8 flex flex-col items-center justify-center text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-600 mb-2">event_busy</span>
                        <p class="text-slate-500 text-sm">No matches scheduled yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Col: Status & Timeline -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="glass-panel p-6 rounded-2xl border border-[#393028]">
                <h4 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">info</span>
                    Current Status
                </h4>
                @php
                    $statusClass = 'bg-slate-700/20 text-slate-400 border-slate-700/50';
                    $statusIcon = 'radio_button_unchecked';
                    $statusGlow = '';
                    if($tournament->status == 'ongoing') { $statusClass = 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50'; $statusIcon = 'radio_button_checked'; $statusGlow = 'shadow-[0_0_15px_rgba(16,185,129,0.2)]'; }
                    elseif($tournament->status == 'upcoming') { $statusClass = 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50'; $statusIcon = 'event_upcoming'; }
                    elseif($tournament->status == 'scheduled') { $statusClass = 'bg-blue-500/20 text-blue-500 border-blue-500/50'; $statusIcon = 'calendar_month'; }
                    elseif($tournament->status == 'completed') { $statusClass = 'bg-slate-800 text-slate-500 border-slate-700'; $statusIcon = 'check_circle'; }
                @endphp
                <div class="flex items-center justify-center p-6 border border-dashed border-[#393028] rounded-xl bg-[#181411]">
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex items-center gap-2 px-4 py-2 rounded-full border {{ $statusClass }} {{ $statusGlow }} uppercase tracking-widest font-black text-sm">
                            <span class="material-symbols-outlined text-[18px]">{{ $statusIcon }}</span>
                            {{ ucfirst($tournament->status) }}
                        </div>
                        <p class="text-slate-500 text-[10px] uppercase font-bold text-center mt-2 max-w-[200px]">
                            @if($tournament->status == 'ongoing') Tournament is currently live.
                            @elseif($tournament->status == 'upcoming') Waiting for teams to register.
                            @elseif($tournament->status == 'scheduled') Bracket generated. Ready to start.
                            @elseif($tournament->status == 'completed') All matches finished.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Date Card -->
            <div class="glass-panel p-6 rounded-2xl border border-[#393028]">
                <h4 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    Timeline
                </h4>
                <div class="space-y-4 relative before:absolute before:inset-y-0 before:left-[15px] before:w-px before:bg-[#393028]">
                    <div class="flex items-start gap-4 relative">
                        <div class="size-8 rounded-full bg-[#221914] border-2 border-primary flex items-center justify-center relative z-10 shrink-0 shadow-[0_0_10px_rgba(244,140,37,0.3)]">
                            <span class="material-symbols-outlined text-[14px] text-primary">play_arrow</span>
                        </div>
                        <div class="pt-1.5 flex flex-col">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Start Date</span>
                            <span class="text-white font-semibold">{{ \Carbon\Carbon::parse($tournament->start_date)->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 relative">
                        <div class="size-8 rounded-full bg-[#221914] border-2 border-[#393028] flex items-center justify-center relative z-10 shrink-0">
                            <span class="material-symbols-outlined text-[14px] text-slate-500">stop</span>
                        </div>
                        <div class="pt-1.5 flex flex-col">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">End Date</span>
                            <span class="text-white font-semibold">{{ \Carbon\Carbon::parse($tournament->end_date)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Top 3 Players & Top 3 Teams -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 3 Players -->
        <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#393028] bg-[#1c1613]/50">
                <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px] text-amber-500">star</span>
                    Top 3 Best PER
                </h4>
            </div>
            @if($topPlayers->isNotEmpty())
                <div class="divide-y divide-[#393028]/50">
                    @foreach($topPlayers as $i => $player)
                        @php
                            $medals = ['🥇', '🥈', '🥉'];
                            $medalColors = ['text-amber-400', 'text-slate-300', 'text-amber-700'];
                        @endphp
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">
                            <span class="text-2xl">{{ $medals[$i] ?? '' }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-sm truncate">{{ $player->name }}</p>
                                <p class="text-slate-500 text-xs truncate">{{ $player->team_name }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-primary font-black text-lg tabular-nums">{{ $player->avg_per }}</p>
                                <p class="text-slate-500 text-[10px] font-bold uppercase">PER</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-600 mb-2">person_off</span>
                    <p class="text-slate-500 text-sm">No player statistics recorded yet.</p>
                </div>
            @endif
        </div>

        <!-- Top 3 Teams -->
        <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#393028] bg-[#1c1613]/50">
                <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px] text-emerald-500">military_tech</span>
                    Top 3 Teams
                </h4>
            </div>
            @if($topTeams->isNotEmpty())
                <div class="divide-y divide-[#393028]/50">
                    @foreach($topTeams as $i => $team)
                        @php $medals = ['🥇', '🥈', '🥉']; @endphp
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">
                            <span class="text-2xl">{{ $medals[$i] ?? '' }}</span>
                            <div class="size-10 rounded-xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden shrink-0">
                                @if($team->logo)
                                    <img src="{{ asset('images/logos/' . $team->logo) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-600">groups</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-sm truncate">{{ $team->name }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-emerald-400 font-black text-lg tabular-nums">{{ $team->wins }}</p>
                                <p class="text-slate-500 text-[10px] font-bold uppercase">WINS</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-600 mb-2">leaderboard</span>
                    <p class="text-slate-500 text-sm">No match results recorded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Alpine.js Modal for Bracket Generation -->
<div x-data="{ open: false }" 
     @open-generate-modal.window="open = true" 
     @keydown.escape.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition.opacity 
         class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
         @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="glass-panel border border-[#393028] rounded-3xl overflow-hidden shadow-2xl w-full max-w-lg relative z-10 m-auto">
            
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-center size-16 mx-auto bg-amber-500/10 border border-amber-500/30 rounded-full mb-6">
                    <span class="material-symbols-outlined text-3xl text-amber-500">warning</span>
                </div>
                
                <h3 class="text-2xl font-black text-white text-center uppercase italic tracking-tight mb-2">Generate Bracket</h3>
                <p class="text-slate-400 text-center text-sm mb-6">Are you sure you want to generate the bracket and schedule? <br><span class="text-amber-500 font-semibold">If you have generated before, previous data will be overwritten and lost.</span></p>

                <form action="{{ route('generate.schedule', $tournament->id) }}" method="POST" id="generateForm">
                    @csrf
                    
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-[#393028] bg-[#181411] cursor-pointer hover:border-primary/50 transition-colors mb-8 group">
                        <div class="relative flex items-center justify-center shrink-0">
                            <input type="checkbox" name="randomize_teams" value="1" class="peer appearance-none size-5 border-2 border-[#393028] rounded bg-[#221914] checked:bg-primary checked:border-primary transition-all">
                            <span class="material-symbols-outlined absolute text-white text-[14px] opacity-0 peer-checked:opacity-100 pointer-events-none">check</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-white font-bold text-sm">Randomize Team Order</span>
                            <span class="text-slate-500 text-xs mt-0.5 group-hover:text-slate-400 transition-colors">Shuffle teams randomly instead of registration order.</span>
                        </div>
                    </label>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl border border-[#393028] text-slate-400 hover:text-white hover:bg-[#221914] font-bold text-sm uppercase tracking-wider transition-all">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-[0_4px_15px_-5px_rgba(16,185,129,0.5)] hover:scale-105 font-bold text-sm uppercase tracking-wider transition-all shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">account_tree</span>
                            Generate Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
