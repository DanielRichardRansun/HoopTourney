@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-blue-500 text-2xl">scoreboard</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Match Summary</h1>
        <p class="text-slate-400 text-sm">{{ $schedule->team1->name ?? 'TBD' }} vs {{ $schedule->team2->name ?? 'TBD' }}</p>
    </div>
</div>

{{-- Match Summary Card --}}
<div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden shadow-xl mb-6">
    <div class="bg-gradient-to-r from-primary/20 to-orange-600/10 px-6 py-4 border-b border-[#393028]">
        <h4 class="text-primary font-black text-sm uppercase tracking-widest text-center">Match Summary</h4>
    </div>
    <div class="p-6 md:p-8">
        {{-- Teams & Scores --}}
        <div class="flex items-center justify-center gap-4 md:gap-8 mb-6">
            <div class="flex-1 text-center max-w-[200px]">
                <h5 class="text-white font-bold text-lg md:text-xl mb-2">{{ $schedule->team1->name ?? 'TBD' }}</h5>
                @if($matchResult)
                    <div class="text-4xl md:text-5xl font-black {{ $matchResult->winning_team_id == $schedule->team1->id ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $matchResult->team1_score }}
                    </div>
                @endif
            </div>
            <div class="flex flex-col items-center">
                <span class="text-slate-600 font-black text-2xl">VS</span>
            </div>
            <div class="flex-1 text-center max-w-[200px]">
                <h5 class="text-white font-bold text-lg md:text-xl mb-2">{{ $schedule->team2->name ?? 'TBD' }}</h5>
                @if($matchResult)
                    <div class="text-4xl md:text-5xl font-black {{ $matchResult->winning_team_id == $schedule->team2->id ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $matchResult->team2_score }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Winner Banner --}}
        @if($matchResult)
            <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-4 text-center mb-6">
                <p class="text-emerald-400 font-bold">
                    <span class="material-symbols-outlined text-[18px] align-middle mr-1">emoji_events</span>
                    Winner: <strong>{{ $matchResult->winning_team_id == $schedule->team1->id ? $schedule->team1->name : $schedule->team2->name }}</strong>
                </p>
            </div>
        @endif

        {{-- Match Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-[#393028]">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-400 text-[16px]">calendar_today</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Date</p>
                    <p class="text-white text-sm font-semibold">{{ \Carbon\Carbon::parse($schedule->date)->format('l, F j, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-purple-400 text-[16px]">schedule</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Time</p>
                    <p class="text-white text-sm font-semibold">{{ \Carbon\Carbon::parse($schedule->date)->format('g:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-emerald-400 text-[16px]">location_on</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Location</p>
                    <p class="text-white text-sm font-semibold">{{ $schedule->location }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quarter Selector --}}
@if($matchResult && $quarterResults->count())
    <div class="glass-panel rounded-2xl p-5 border border-[#393028] mb-6">
        <h5 class="text-white font-bold text-sm uppercase tracking-wider text-center mb-4">Pilih Quarter</h5>
        <div class="flex flex-wrap justify-center gap-2 mb-4">
            <button type="button" class="quarter-btn active px-4 py-2 rounded-lg bg-primary text-[#181411] font-bold text-xs uppercase tracking-wider transition-all" data-quarter-selector="all">All Result</button>
            @foreach ($quarterResults->sortBy('quarter_number') as $qr)
                <button type="button" class="quarter-btn px-4 py-2 rounded-lg bg-[#221914] border border-[#393028] text-slate-400 font-bold text-xs uppercase tracking-wider hover:bg-[#2c221c] hover:text-white transition-all" data-quarter-selector="{{ $qr->quarter_number }}">
                    Quarter {{ $qr->quarter_number }}
                </button>
            @endforeach
        </div>
        <div id="quarter-score-display" class="text-center text-white text-sm font-semibold"></div>
    </div>
@endif

{{-- Player Statistics --}}
@if ($matchResult)
    <div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden shadow-xl mb-6">
        <div class="bg-gradient-to-r from-indigo-500/10 to-transparent px-6 py-4 border-b border-[#393028]">
            <h4 class="text-indigo-400 font-black text-sm uppercase tracking-widest text-center">Player Statistics</h4>
        </div>
        <div class="p-6">
            {{-- Team 1 Stats --}}
            <h5 class="text-white font-black text-base mb-4 pb-2 border-b-2 border-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">groups</span>
                {{ $schedule->team1->name }}
            </h5>
            <div class="overflow-x-auto mb-8">
                <table class="w-full text-left border-collapse" id="stat_team1_main">
                    <thead>
                        <tr class="border-b border-[#393028]">
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Player</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">PTS</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FGM</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FGA</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FTM</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FTA</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">ORB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">DRB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">REB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">AST</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">STL</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">BLK</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">TO</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">PF</th>
                            <th class="p-3 text-xs font-bold text-primary uppercase tracking-wider text-center bg-primary/5">PER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#393028]">
                        @php
                            $team1PlayerTotals = [];
                            foreach ($schedule->team1->players as $player) {
                                $team1PlayerTotals[$player->id] = [
                                    'player_name' => $player->name,
                                    'point' => 0, 'fgm' => 0, 'fga' => 0, 'ftm' => 0, 'fta' => 0,
                                    'orb' => 0, 'drb' => 0, 'stl' => 0, 'ast' => 0, 'blk' => 0,
                                    'pf' => 0, 'to' => 0, 'per' => 0, 'quarter_count' => 0
                                ];
                            }
                        @endphp
                        @foreach ($playerStatsPerQuarter as $quarterNumber => $stats)
                            @foreach ($stats->where('player.teams_id', $schedule->team1->id) as $stat)
                                <tr class="player-stat-row hover:bg-white/5 transition-colors" data-quarter-number="{{ $quarterNumber }}" data-team-id="{{ $schedule->team1->id }}">
                                    <td class="p-3 text-white font-semibold text-sm whitespace-nowrap">{{ $stat->player->name }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->point }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fgm }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fga }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->ftm }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fta }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->orb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->drb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->orb + $stat->drb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->ast }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->stl }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->blk }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->to }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->pf }}</td>
                                    <td class="p-3 text-primary text-sm text-center font-bold bg-primary/5">{{ number_format($stat->per, 2) }}</td>
                                </tr>
                                @php
                                    $team1PlayerTotals[$stat->players_id]['point'] += $stat->point;
                                    $team1PlayerTotals[$stat->players_id]['fgm'] += $stat->fgm;
                                    $team1PlayerTotals[$stat->players_id]['fga'] += $stat->fga;
                                    $team1PlayerTotals[$stat->players_id]['ftm'] += $stat->ftm;
                                    $team1PlayerTotals[$stat->players_id]['fta'] += $stat->fta;
                                    $team1PlayerTotals[$stat->players_id]['orb'] += $stat->orb;
                                    $team1PlayerTotals[$stat->players_id]['drb'] += $stat->drb;
                                    $team1PlayerTotals[$stat->players_id]['stl'] += $stat->stl;
                                    $team1PlayerTotals[$stat->players_id]['ast'] += $stat->ast;
                                    $team1PlayerTotals[$stat->players_id]['blk'] += $stat->blk;
                                    $team1PlayerTotals[$stat->players_id]['pf'] += $stat->pf;
                                    $team1PlayerTotals[$stat->players_id]['to'] += $stat->to;
                                    $team1PlayerTotals[$stat->players_id]['per'] += $stat->per;
                                    $team1PlayerTotals[$stat->players_id]['quarter_count']++;
                                @endphp
                            @endforeach
                        @endforeach
                        @foreach ($team1PlayerTotals as $playerId => $totalStat)
                            @php $avgPer = $totalStat['quarter_count'] > 0 ? $totalStat['per'] / $totalStat['quarter_count'] : 0; @endphp
                            <tr class="player-stat-row all-result-row hover:bg-white/5 transition-colors" data-quarter-number="all" data-player-id="{{ $playerId }}" data-team-id="{{ $schedule->team1->id }}">
                                <td class="p-3 text-white font-semibold text-sm whitespace-nowrap">{{ $totalStat['player_name'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['point'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fgm'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fga'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['ftm'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fta'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['orb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['drb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['orb'] + $totalStat['drb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['ast'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['stl'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['blk'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['to'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['pf'] }}</td>
                                <td class="p-3 text-primary text-sm text-center font-bold bg-primary/5">{{ number_format($avgPer, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Team 2 Stats --}}
            <h5 class="text-white font-black text-base mb-4 pb-2 border-b-2 border-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">groups</span>
                {{ $schedule->team2->name }}
            </h5>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="stat_team2_main">
                    <thead>
                        <tr class="border-b border-[#393028]">
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Player</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">PTS</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FGM</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FGA</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FTM</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">FTA</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">ORB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">DRB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">REB</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">AST</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">STL</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">BLK</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">TO</th>
                            <th class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">PF</th>
                            <th class="p-3 text-xs font-bold text-primary uppercase tracking-wider text-center bg-primary/5">PER</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#393028]">
                        @php
                            $team2PlayerTotals = [];
                            foreach ($schedule->team2->players as $player) {
                                $team2PlayerTotals[$player->id] = [
                                    'player_name' => $player->name,
                                    'point' => 0, 'fgm' => 0, 'fga' => 0, 'ftm' => 0, 'fta' => 0,
                                    'orb' => 0, 'drb' => 0, 'stl' => 0, 'ast' => 0, 'blk' => 0,
                                    'pf' => 0, 'to' => 0, 'per' => 0, 'quarter_count' => 0
                                ];
                            }
                        @endphp
                        @foreach ($playerStatsPerQuarter as $quarterNumber => $stats)
                            @foreach ($stats->where('player.teams_id', $schedule->team2->id) as $stat)
                                <tr class="player-stat-row hover:bg-white/5 transition-colors" data-quarter-number="{{ $quarterNumber }}" data-team-id="{{ $schedule->team2->id }}">
                                    <td class="p-3 text-white font-semibold text-sm whitespace-nowrap">{{ $stat->player->name }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->point }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fgm }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fga }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->ftm }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->fta }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->orb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->drb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->orb + $stat->drb }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->ast }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->stl }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->blk }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->to }}</td>
                                    <td class="p-3 text-slate-300 text-sm text-center">{{ $stat->pf }}</td>
                                    <td class="p-3 text-primary text-sm text-center font-bold bg-primary/5">{{ number_format($stat->per, 2) }}</td>
                                </tr>
                                @php
                                    $team2PlayerTotals[$stat->players_id]['point'] += $stat->point;
                                    $team2PlayerTotals[$stat->players_id]['fgm'] += $stat->fgm;
                                    $team2PlayerTotals[$stat->players_id]['fga'] += $stat->fga;
                                    $team2PlayerTotals[$stat->players_id]['ftm'] += $stat->ftm;
                                    $team2PlayerTotals[$stat->players_id]['fta'] += $stat->fta;
                                    $team2PlayerTotals[$stat->players_id]['orb'] += $stat->orb;
                                    $team2PlayerTotals[$stat->players_id]['drb'] += $stat->drb;
                                    $team2PlayerTotals[$stat->players_id]['stl'] += $stat->stl;
                                    $team2PlayerTotals[$stat->players_id]['ast'] += $stat->ast;
                                    $team2PlayerTotals[$stat->players_id]['blk'] += $stat->blk;
                                    $team2PlayerTotals[$stat->players_id]['pf'] += $stat->pf;
                                    $team2PlayerTotals[$stat->players_id]['to'] += $stat->to;
                                    $team2PlayerTotals[$stat->players_id]['per'] += $stat->per;
                                    $team2PlayerTotals[$stat->players_id]['quarter_count']++;
                                @endphp
                            @endforeach
                        @endforeach
                        @foreach ($team2PlayerTotals as $playerId => $totalStat)
                            @php $avgPer = $totalStat['quarter_count'] > 0 ? $totalStat['per'] / $totalStat['quarter_count'] : 0; @endphp
                            <tr class="player-stat-row all-result-row hover:bg-white/5 transition-colors" data-quarter-number="all" data-player-id="{{ $playerId }}" data-team-id="{{ $schedule->team2->id }}">
                                <td class="p-3 text-white font-semibold text-sm whitespace-nowrap">{{ $totalStat['player_name'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['point'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fgm'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fga'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['ftm'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['fta'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['orb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['drb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['orb'] + $totalStat['drb'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['ast'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['stl'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['blk'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['to'] }}</td>
                                <td class="p-3 text-slate-300 text-sm text-center">{{ $totalStat['pf'] }}</td>
                                <td class="p-3 text-primary text-sm text-center font-bold bg-primary/5">{{ number_format($avgPer, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="glass-panel rounded-2xl p-12 text-center border border-[#393028]">
        <div class="size-16 rounded-full bg-[#181411] flex items-center justify-center mx-auto mb-4 border border-[#393028]">
            <span class="material-symbols-outlined text-3xl text-slate-600">query_stats</span>
        </div>
        <p class="text-slate-500 text-sm">No player statistics available for this match.</p>
    </div>
@endif

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1rem; padding: 0 1rem; }
    .dataTables_wrapper .dataTables_filter input { background-color: #221914; border: 1px solid #393028; color: #f1f5f9; border-radius: 9999px; padding: 0.5rem 1rem; margin-left: 0.5rem; outline: none; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #f48c25; }
    .dataTables_wrapper .dataTables_length select { background-color: #221914; border: 1px solid #393028; color: #f1f5f9; border-radius: 0.5rem; padding: 0.25rem; }
    table.dataTable.no-footer { border-bottom: 1px solid #393028; }
    table.dataTable thead th { border-bottom: 1px solid #393028 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { background: #221914 !important; border: 1px solid #393028 !important; color: #94a3b8 !important; border-radius: 0.5rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #2c221c !important; color: #f1f5f9 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #f48c25 !important; color: #181411 !important; border-color: #f48c25 !important; }
    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after { color: #f48c25 !important; }
    .quarter-btn.active { background: #f48c25 !important; color: #181411 !important; border-color: #f48c25 !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    const quarterResultsData = @json($quarterResults->keyBy('quarter_number'));
    const allMatchResult = @json($matchResult);

    $(document).ready(function() {
        var table1 = $('#stat_team1_main').DataTable({
            "paging": false, "ordering": true, "info": false, "searching": true,
            "order": [[1, 'desc']], "responsive": true, "autoWidth": false, "retrieve": true
        });

        var table2 = $('#stat_team2_main').DataTable({
            "paging": false, "ordering": true, "info": false, "searching": true,
            "order": [[1, 'desc']], "responsive": true, "autoWidth": false, "retrieve": true
        });

        function filterTablesByQuarter(quarterNumber) {
            table1.rows().every(function() {
                const rowQuarter = $(this.node()).data('quarter-number');
                if (quarterNumber === 'all') {
                    if ($(this.node()).hasClass('all-result-row')) { this.nodes().to$().show(); }
                    else { this.nodes().to$().hide(); }
                } else {
                    if (rowQuarter == quarterNumber) { this.nodes().to$().show(); }
                    else { this.nodes().to$().hide(); }
                }
            });
            table1.draw(false);

            table2.rows().every(function() {
                const rowQuarter = $(this.node()).data('quarter-number');
                if (quarterNumber === 'all') {
                    if ($(this.node()).hasClass('all-result-row')) { this.nodes().to$().show(); }
                    else { this.nodes().to$().hide(); }
                } else {
                    if (rowQuarter == quarterNumber) { this.nodes().to$().show(); }
                    else { this.nodes().to$().hide(); }
                }
            });
            table2.draw(false);
        }

        function displayCurrentQuarterScore(quarterNumber) {
            const displayArea = $('#quarter-score-display');
            displayArea.empty();
            if (quarterNumber === 'all') {
                if (allMatchResult) {
                    displayArea.append(`<p class="mb-0"><strong>Final Score:</strong> {{ $schedule->team1->name }} ${allMatchResult.team1_score} | ${allMatchResult.team2_score} {{ $schedule->team2->name }}</p>`);
                } else {
                    displayArea.append('<p class="text-slate-500">Final scores not available.</p>');
                }
            } else {
                const quarter = quarterResultsData[quarterNumber];
                if (quarter) {
                    displayArea.append(`<p class="mb-0"><strong>Quarter ${quarterNumber} Score:</strong> {{ $schedule->team1->name }} ${quarter.team1_score} | ${quarter.team2_score} {{ $schedule->team2->name }}</p>`);
                } else {
                    displayArea.append(`<p class="text-slate-500">No score recorded for Quarter ${quarterNumber}.</p>`);
                }
            }
        }

        $('[data-quarter-selector]').on('click', function() {
            $('.quarter-btn').removeClass('active');
            $(this).addClass('active');
            const selectedQuarter = $(this).data('quarter-selector');
            filterTablesByQuarter(selectedQuarter);
            displayCurrentQuarterScore(selectedQuarter);
        });

        filterTablesByQuarter('all');
        displayCurrentQuarterScore('all');
    });
</script>
@endpush