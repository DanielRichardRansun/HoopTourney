@extends('layouts.app2')

@section('title', 'Match Summary - ' . $tournament->name)

@section('content')
@php
    // Prepare Team Scores per Quarter
    $quarterScores = $quarterResults->keyBy('quarter_number')->map(function($qr) {
        return [
            't1' => $qr->team1_score,
            't2' => $qr->team2_score
        ];
    });

    // Function to prepare player stats for a team
    $prepareTeamStats = function($team, $playerStatsPerQuarter) {
        return $team->players->map(function($player) use ($playerStatsPerQuarter, $team) {
            $statsByQuarter = [];
            $total = [
                'pts' => 0, 'fgm' => 0, 'fga' => 0, 'ftm' => 0, 'fta' => 0,
                'orb' => 0, 'drb' => 0, 'ast' => 0, 'stl' => 0, 'blk' => 0,
                'to' => 0, 'pf' => 0, 'per' => 0, 'q_count' => 0
            ];

            foreach ($playerStatsPerQuarter as $qNum => $stats) {
                $stat = $stats->where('player.teams_id', $team->id)->where('players_id', $player->id)->first();
                if ($stat) {
                    $statsByQuarter[$qNum] = [
                        'pts' => (int)$stat->point,
                        'fgm' => (int)$stat->fgm,
                        'fga' => (int)$stat->fga,
                        'ftm' => (int)$stat->ftm,
                        'fta' => (int)$stat->fta,
                        'orb' => (int)$stat->orb,
                        'drb' => (int)$stat->drb,
                        'ast' => (int)$stat->ast,
                        'stl' => (int)$stat->stl,
                        'blk' => (int)$stat->blk,
                        'to' => (int)$stat->to,
                        'pf' => (int)$stat->pf,
                        'per' => (float)$stat->per
                    ];
                    
                    // Accumulate totals
                    foreach ($total as $key => $val) {
                        if ($key !== 'per' && $key !== 'q_count' && isset($statsByQuarter[$qNum][$key])) {
                            $total[$key] += $statsByQuarter[$qNum][$key];
                        }
                    }
                    $total['per'] += (float)$stat->per;
                    $total['q_count']++;
                }
            }

            if ($total['q_count'] > 0) {
                $total['per'] = round($total['per'] / $total['q_count'], 2);
            }

            return [
                'id' => $player->id,
                'name' => $player->name,
                'quarters' => $statsByQuarter,
                'all' => $total
            ];
        });
    };

    $team1Stats = $prepareTeamStats($schedule->team1, $playerStatsPerQuarter);
    $team2Stats = $prepareTeamStats($schedule->team2, $playerStatsPerQuarter);
@endphp

<div class="space-y-6 max-w-[1400px] mx-auto" x-data="{
    activeQuarter: 'all',
    team1Stats: {{ json_encode($team1Stats) }},
    team2Stats: {{ json_encode($team2Stats) }},
    quarterScores: {{ json_encode($quarterScores) }},
    finalScore: {
        t1: {{ $matchResult->team1_score ?? 0 }},
        t2: {{ $matchResult->team2_score ?? 0 }}
    },
    getTeamName(id) {
        return id === 1 ? '{{ $schedule->team1->name }}' : '{{ $schedule->team2->name }}';
    }
}">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Match Summary</h1>
            <p class="text-slate-400 text-sm mt-1">Detailed statistics and results</p>
        </div>
        <a href="{{ route('dashboard.jadwal', $tournament->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-[#221914] border border-[#393028] text-slate-300 hover:text-white hover:border-slate-500 transition-colors tooltip" data-tip="Back to Schedule">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span class="text-sm font-semibold hidden md:inline">Back</span>
        </a>
    </div>

    <!-- Match Scoreboard Card -->
    <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden relative shadow-2xl mb-8">
        <!-- Ambient Glow -->
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/5 via-[#1c1613] to-[#181411] opacity-50"></div>

        <div class="p-8 relative z-10 w-full">
            
            <!-- Context Match Info -->
            <div class="flex flex-wrap justify-center gap-4 sm:gap-8 mb-8 text-sm font-semibold text-slate-400 border-b border-[#393028]/50 pb-6">
                <div class="flex items-center gap-2 bg-[#181411]/50 px-4 py-2 rounded-xl border border-[#393028]">
                    <span class="material-symbols-outlined text-sm text-primary">event</span>
                    <span>{{ \Carbon\Carbon::parse($schedule->date)->format('l, F j, Y') }}</span>
                </div>
                <div class="flex items-center gap-2 bg-[#181411]/50 px-4 py-2 rounded-xl border border-[#393028]">
                    <span class="material-symbols-outlined text-sm text-amber-500">schedule</span>
                    <span>{{ \Carbon\Carbon::parse($schedule->date)->format('g:i A') }}</span>
                </div>
                <div class="flex items-center gap-2 bg-[#181411]/50 px-4 py-2 rounded-xl border border-[#393028]">
                    <span class="material-symbols-outlined text-sm text-emerald-500">location_on</span>
                    <span>{{ $schedule->location }}</span>
                </div>
            </div>

            <!-- Scoreboard -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-16">
                
                <!-- Team 1 -->
                @php
                    $t1Winner = $matchResult && $matchResult->winning_team_id == $schedule->team1->id;
                @endphp
                <div class="flex flex-col items-center flex-1 w-full max-w-[300px]">
                    <div class="size-32 rounded-full bg-[#181411] border-4 {{ $t1Winner ? 'border-primary shadow-[0_0_30px_rgba(244,140,37,0.3)]' : 'border-[#393028]' }} flex items-center justify-center mb-6 overflow-hidden relative">
                        @if(isset($schedule->team1->logo) && $schedule->team1->logo)
                            <img src="{{ asset('images/logos/' . $schedule->team1->logo) }}" alt="{{ $schedule->team1->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-[64px] text-slate-700">security</span>
                        @endif
                    </div>
                    <h3 class="text-2xl font-black text-white text-center uppercase tracking-tight mb-2 {{ $t1Winner ? 'text-primary' : '' }}">{{ $schedule->team1->name ?? 'TBD' }}</h3>
                    
                    @if($matchResult)
                        <div class="mt-4 px-8 py-4 rounded-2xl bg-[#1c1613] border border-[#393028] shadow-inner w-full text-center">
                            <span class="text-6xl font-black {{ $t1Winner ? 'text-primary drop-shadow-[0_0_15px_rgba(244,140,37,0.5)]' : 'text-slate-400' }} tracking-tighter">{{ $matchResult->team1_score }}</span>
                        </div>
                    @endif
                </div>

                <!-- VS Badge -->
                <div class="flex flex-col items-center justify-center px-4 shrink-0">
                    <div class="size-16 rounded-2xl bg-[#221914] border border-[#393028] flex items-center justify-center shadow-lg rotate-45">
                        <span class="text-xl font-black text-slate-500 italic -rotate-45">VS</span>
                    </div>
                </div>

                <!-- Team 2 -->
                @php
                    $t2Winner = $matchResult && $matchResult->winning_team_id == $schedule->team2->id;
                @endphp
                <div class="flex flex-col items-center flex-1 w-full max-w-[300px]">
                    <div class="size-32 rounded-full bg-[#181411] border-4 {{ $t2Winner ? 'border-primary shadow-[0_0_30px_rgba(244,140,37,0.3)]' : 'border-[#393028]' }} flex items-center justify-center mb-6 overflow-hidden relative">
                        @if(isset($schedule->team2->logo) && $schedule->team2->logo)
                            <img src="{{ asset('images/logos/' . $schedule->team2->logo) }}" alt="{{ $schedule->team2->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-[64px] text-slate-700">security</span>
                        @endif
                    </div>
                    <h3 class="text-2xl font-black text-white text-center uppercase tracking-tight mb-2 {{ $t2Winner ? 'text-primary' : '' }}">{{ $schedule->team2->name ?? 'TBD' }}</h3>
                    
                    @if($matchResult)
                        <div class="mt-4 px-8 py-4 rounded-2xl bg-[#1c1613] border border-[#393028] shadow-inner w-full text-center">
                            <span class="text-6xl font-black {{ $t2Winner ? 'text-primary drop-shadow-[0_0_15px_rgba(244,140,37,0.5)]' : 'text-slate-400' }} tracking-tighter">{{ $matchResult->team2_score }}</span>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Winner Banner -->
            @if($matchResult)
            <div class="mt-10 bg-primary/10 border border-primary/30 rounded-xl p-4 text-center shadow-[0_0_20px_rgba(244,140,37,0.1)] flex items-center justify-center gap-3">
                <span class="material-symbols-outlined text-primary text-2xl">emoji_events</span>
                <span class="text-lg text-primary">Winner: <strong class="font-black uppercase tracking-wide">{{ $matchResult->winning_team_id == $schedule->team1->id ? $schedule->team1->name : $schedule->team2->name }}</strong></span>
            </div>
            @endif

        </div>
    </div>

    <!-- Quarter Selector -->
    @if($matchResult && $quarterResults->count())
    <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-xl mb-8">
        <div class="p-6 md:p-8 relative z-10 w-full flex flex-col items-center">
            
            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6">View Quarter Statistics</h4>
            
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button type="button" @click="activeQuarter = 'all'" 
                        :class="activeQuarter === 'all' ? 'bg-primary/10 border-primary text-primary shadow-[0_0_15px_rgba(244,140,37,0.1)]' : 'bg-transparent border-[#393028] text-slate-500 hover:text-white hover:bg-[#221914]'"
                        class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all duration-300 border focus:outline-none">
                    All Results
                </button>
                @foreach ($quarterResults->sortBy('quarter_number') as $qr)
                    <button type="button" @click="activeQuarter = {{ $qr->quarter_number }}" 
                            :class="activeQuarter === {{ $qr->quarter_number }} ? 'bg-primary/10 border-primary text-primary shadow-[0_0_15px_rgba(244,140,37,0.1)]' : 'bg-transparent border-[#393028] text-slate-500 hover:text-white hover:bg-[#221914]'"
                            class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all duration-300 border focus:outline-none">
                        Quarter {{ $qr->quarter_number }}
                    </button>
                @endforeach
            </div>

            <div class="w-full max-w-2xl text-center bg-[#1c1613] border border-[#393028] rounded-2xl px-8 py-5 flex items-center justify-center shadow-inner">
                <p class="mb-0 text-slate-300 text-lg flex items-center gap-4">
                    <span class="font-black text-white italic uppercase tracking-tight">{{ $schedule->team1->name }}</span> 
                    <span class="text-primary font-black text-3xl tabular-nums" x-text="activeQuarter === 'all' ? finalScore.t1 : (quarterScores[activeQuarter] ? quarterScores[activeQuarter].t1 : '0')"></span>
                    <span class="text-slate-600 font-bold px-1 text-xl">-</span>
                    <span class="text-primary font-black text-3xl tabular-nums" x-text="activeQuarter === 'all' ? finalScore.t2 : (quarterScores[activeQuarter] ? quarterScores[activeQuarter].t2 : '0')"></span>
                    <span class="font-black text-white italic uppercase tracking-tight">{{ $schedule->team2->name }}</span>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Player Statistics Section -->
    @if ($matchResult)
    <div class="mb-4">
        <h3 class="text-2xl font-black text-white italic tracking-tight flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-[28px]">analytics</span>
            Detailed Statistics
        </h3>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Team 1 Stats -->
        <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-2xl relative flex flex-col">
            <div class="bg-gradient-to-r from-primary/20 via-primary/5 to-transparent px-6 py-4 border-b border-[#393028]">
                <h4 class="text-xl font-black text-white uppercase tracking-tight">{{ $schedule->team1->name }}</h4>
            </div>
            
            <div class="p-4 relative z-10 w-full flex-grow overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar pb-2">
                    <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                        <thead>
                            <tr class="border-b border-[#393028]">
                                <th class="px-3 py-3 font-black text-slate-500 uppercase tracking-widest bg-[#181411] sticky left-0 z-20">Player</th>
                                <th class="px-2 py-3 font-black text-primary uppercase bg-[#1c1613] text-center">PTS</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase text-center">FGM</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase text-center">FGA</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase bg-[#1c1613] text-center">FTM</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase bg-[#1c1613] text-center">FTA</th>
                                <th class="px-2 py-3 font-black text-blue-400 uppercase text-center">ORB</th>
                                <th class="px-2 py-3 font-black text-sky-400 uppercase text-center">DRB</th>
                                <th class="px-2 py-3 font-black text-teal-400 uppercase bg-[#1c1613] text-center">REB</th>
                                <th class="px-2 py-3 font-black text-indigo-400 uppercase text-center">AST</th>
                                <th class="px-2 py-3 font-black text-emerald-400 uppercase bg-[#1c1613] text-center">STL</th>
                                <th class="px-2 py-3 font-black text-rose-400 uppercase text-center">BLK</th>
                                <th class="px-2 py-3 font-black text-orange-400 uppercase bg-[#1c1613] text-center">TO</th>
                                <th class="px-2 py-3 font-black text-red-500 uppercase text-center">PF</th>
                                <th class="px-2 py-3 font-black text-amber-400 uppercase bg-amber-500/10 text-center">PER</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#393028]/30">
                            <template x-for="player in team1Stats" :key="player.id">
                                <tr class="hover:bg-white/5 transition-colors group" x-show="activeQuarter === 'all' || player.quarters[activeQuarter]">
                                    <td class="px-3 py-3 font-bold text-white sticky left-0 bg-[#181411] group-hover:bg-[#201b17] z-10 transition-colors" x-text="player.name"></td>
                                    
                                    <!-- All Quarters View -->
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-primary" x-text="player.all.pts"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fgm"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fga"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.ftm"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fta"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-blue-400/80" x-text="player.all.orb"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-sky-400/80" x-text="player.all.drb"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-teal-400" x-text="(player.all.orb || 0) + (player.all.drb || 0)"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-indigo-400" x-text="player.all.ast"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-emerald-400" x-text="player.all.stl"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-rose-400" x-text="player.all.blk"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-orange-400/80" x-text="player.all.to"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-red-500/80" x-text="player.all.pf"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-black text-amber-500 bg-amber-500/5" x-text="player.all.per"></td>
                                    </template>

                                    <!-- Single Quarter View -->
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-primary" x-text="player.quarters[activeQuarter].pts || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fgm || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fga || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].ftm || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fta || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-blue-400/80" x-text="player.quarters[activeQuarter].orb || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-sky-400/80" x-text="player.quarters[activeQuarter].drb || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-teal-400" x-text="(player.quarters[activeQuarter].orb || 0) + (player.quarters[activeQuarter].drb || 0)"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-indigo-400" x-text="player.quarters[activeQuarter].ast || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-emerald-400" x-text="player.quarters[activeQuarter].stl || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-rose-400" x-text="player.quarters[activeQuarter].blk || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-orange-400/80" x-text="player.quarters[activeQuarter].to || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-red-500/80" x-text="player.quarters[activeQuarter].pf || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-black text-amber-500 bg-amber-500/5" x-text="player.quarters[activeQuarter].per || 0"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Team 2 Stats -->
        <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-2xl relative flex flex-col">
            <div class="bg-gradient-to-r from-primary/20 via-primary/5 to-transparent px-6 py-4 border-b border-[#393028]">
                <h4 class="text-xl font-black text-white uppercase tracking-tight">{{ $schedule->team2->name }}</h4>
            </div>
            
            <div class="p-4 relative z-10 w-full flex-grow overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar pb-2">
                    <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                        <thead>
                            <tr class="border-b border-[#393028]">
                                <th class="px-3 py-3 font-black text-slate-500 uppercase tracking-widest bg-[#181411] sticky left-0 z-20">Player</th>
                                <th class="px-2 py-3 font-black text-primary uppercase bg-[#1c1613] text-center">PTS</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase text-center">FGM</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase text-center">FGA</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase bg-[#1c1613] text-center">FTM</th>
                                <th class="px-2 py-3 font-black text-slate-500 uppercase bg-[#1c1613] text-center">FTA</th>
                                <th class="px-2 py-3 font-black text-blue-400 uppercase text-center">ORB</th>
                                <th class="px-2 py-3 font-black text-sky-400 uppercase text-center">DRB</th>
                                <th class="px-2 py-3 font-black text-teal-400 uppercase bg-[#1c1613] text-center">REB</th>
                                <th class="px-2 py-3 font-black text-indigo-400 uppercase text-center">AST</th>
                                <th class="px-2 py-3 font-black text-emerald-400 uppercase bg-[#1c1613] text-center">STL</th>
                                <th class="px-2 py-3 font-black text-rose-400 uppercase text-center">BLK</th>
                                <th class="px-2 py-3 font-black text-orange-400 uppercase bg-[#1c1613] text-center">TO</th>
                                <th class="px-2 py-3 font-black text-red-500 uppercase text-center">PF</th>
                                <th class="px-2 py-3 font-black text-amber-400 uppercase bg-amber-500/10 text-center">PER</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#393028]/30">
                            <template x-for="player in team2Stats" :key="player.id">
                                <tr class="hover:bg-white/5 transition-colors group" x-show="activeQuarter === 'all' || player.quarters[activeQuarter]">
                                    <td class="px-3 py-3 font-bold text-white sticky left-0 bg-[#181411] group-hover:bg-[#201b17] z-10 transition-colors" x-text="player.name"></td>
                                    
                                    <!-- All Quarters View -->
                                    <!-- All Quarters View -->
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-primary" x-text="player.all.pts"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fgm"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fga"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.ftm"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.all.fta"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-blue-400/80" x-text="player.all.orb"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-sky-400/80" x-text="player.all.drb"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-teal-400" x-text="(player.all.orb || 0) + (player.all.drb || 0)"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-indigo-400" x-text="player.all.ast"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-emerald-400" x-text="player.all.stl"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-bold text-rose-400" x-text="player.all.blk"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-orange-400/80" x-text="player.all.to"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center text-red-500/80" x-text="player.all.pf"></td>
                                    </template>
                                    <template x-if="activeQuarter === 'all'">
                                        <td class="px-2 py-3 text-center font-black text-amber-500 bg-amber-500/5" x-text="player.all.per"></td>
                                    </template>

                                    <!-- Single Quarter View -->
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-primary" x-text="player.quarters[activeQuarter].pts || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fgm || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fga || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].ftm || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-slate-400" x-text="player.quarters[activeQuarter].fta || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-blue-400/80" x-text="player.quarters[activeQuarter].orb || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-sky-400/80" x-text="player.quarters[activeQuarter].drb || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-teal-400" x-text="(player.quarters[activeQuarter].orb || 0) + (player.quarters[activeQuarter].drb || 0)"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-indigo-400" x-text="player.quarters[activeQuarter].ast || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-emerald-400" x-text="player.quarters[activeQuarter].stl || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-bold text-rose-400" x-text="player.quarters[activeQuarter].blk || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-orange-400/80" x-text="player.quarters[activeQuarter].to || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center text-red-500/80" x-text="player.quarters[activeQuarter].pf || 0"></td>
                                    </template>
                                    <template x-if="activeQuarter !== 'all' && player.quarters[activeQuarter]">
                                        <td class="px-2 py-3 text-center font-black text-amber-500 bg-amber-500/5" x-text="player.quarters[activeQuarter].per || 0"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    @else
    <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-xl mt-8">
        <div class="py-16 flex flex-col items-center justify-center text-center">
            <div class="size-20 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-slate-600">analytics</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No Statistics Available</h3>
            <p class="text-slate-500 max-w-sm">Detailed player statistics have not been recorded or published for this match yet.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(24, 20, 17, 0.8); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #393028; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b4237; }

    /* Sticky column shading */
    th.sticky, td.sticky {
        box-shadow: 10px 0 15px -10px rgba(0,0,0,0.5);
    }
</style>
@endpush
