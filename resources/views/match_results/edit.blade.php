@extends('layouts.app2')

@section('content')
<div class="space-y-6 max-w-[1400px] mx-auto">
    <!-- Header & Back Button -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[32px]">edit_note</span>
                Edit Match Result
            </h1>
            <p class="text-slate-400 text-sm mt-1">Update quarter scores and detailed player statistics</p>
        </div>
        <a href="{{ route('dashboard.jadwal', $tournament->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-[#221914] border border-[#393028] text-slate-300 hover:text-white hover:border-slate-500 transition-colors tooltip" data-tip="Back to Schedule">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span class="text-sm font-semibold hidden md:inline">Back</span>
        </a>
    </div>

    <!-- Match Context Banner -->
    <div class="glass-panel border border-[#393028] rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-6 bg-gradient-to-r from-[#1c1613] to-[#181411]">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                @if(isset($schedule->team1->logo) && $schedule->team1->logo)
                    <img src="{{ asset('images/teams/' . $schedule->team1->logo) }}" alt="{{ $team1->name }}" class="size-12 rounded-full border-2 border-[#393028] object-cover bg-[#181411]">
                @else
                    <div class="size-12 rounded-full border-2 border-[#393028] bg-[#181411] flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl text-slate-600">security</span>
                    </div>
                @endif
                <span class="text-xl font-black text-white uppercase">{{ $team1->name }}</span>
            </div>
            <div class="text-xl font-black text-slate-600 italic px-2">VS</div>
            <div class="flex items-center gap-4">
                <span class="text-xl font-black text-white uppercase">{{ $team2->name }}</span>
                @if(isset($schedule->team2->logo) && $schedule->team2->logo)
                    <img src="{{ asset('images/teams/' . $schedule->team2->logo) }}" alt="{{ $team2->name }}" class="size-12 rounded-full border-2 border-[#393028] object-cover bg-[#181411]">
                @else
                    <div class="size-12 rounded-full border-2 border-[#393028] bg-[#181411] flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl text-slate-600">security</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="text-sm font-semibold text-slate-400 flex flex-col items-end gap-1">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">event</span>
                {{ \Carbon\Carbon::parse($schedule->date)->format('M d, Y') }}
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-amber-500">schedule</span>
                {{ \Carbon\Carbon::parse($schedule->date)->format('g:i A') }}
            </div>
        </div>
    </div>

    <form action="{{ route('matchResults.update', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" method="POST" id="matchResultForm">
        @csrf
        @method('PUT') {{-- Penting untuk metode UPDATE --}}

        <!-- Quarter Scores Section -->
        <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden relative shadow-2xl mb-8">
            <div class="bg-gradient-to-r from-primary/20 via-primary/5 to-transparent px-8 py-5 border-b border-[#393028] flex justify-between items-center">
                <h4 class="text-xl font-black text-white uppercase tracking-tight">Edit Quarter Scores</h4>
                <button type="button" id="add-quarter-button" class="btn-primary flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-primary hover:bg-[#e67e22] text-[#181411] transition-colors shadow-[0_0_15px_rgba(244,140,37,0.3)]">
                    <span class="material-symbols-outlined text-sm">add</span> Add Quarter
                </button>
            </div>
            
            <div class="p-8 relative z-10 w-full">
                <div id="quarter-scores-container" class="space-y-4">
                    @php
                        $maxQuarter = $quarterResults->keys()->max() ?: 0;
                    @endphp
                    @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                        @php
                            $quarterData = $quarterResults->get($qNum);
                        @endphp
                        <div class="quarter-input-group glass-panel bg-[#181411]/50 border border-[#393028] p-4 rounded-2xl flex flex-col lg:flex-row items-center gap-6" data-quarter-number="{{ $qNum }}">
                            <div class="w-full lg:w-auto font-black text-slate-400 uppercase tracking-widest text-sm text-center">Quarter {{ $qNum }}</div>
                            <div class="flex-1 flex w-full items-center justify-center lg:justify-start gap-4 mx-auto max-w-lg lg:max-w-none">
                                <div class="relative flex-1">
                                    <label class="text-xs font-bold text-slate-500 mb-1 block truncate text-center lg:text-left" title="{{ $team1->name }}">{{ $team1->name }}</label>
                                    <input type="number" name="quarter_scores[{{ $qNum }}][team1_score]" class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none transition-colors text-center font-bold text-xl" required min="0" placeholder="0" value="{{ $quarterData->team1_score ?? 0 }}">
                                </div>
                                <div class="text-xl font-black text-slate-600 italic px-2 mt-4">VS</div>
                                <div class="relative flex-1">
                                    <label class="text-xs font-bold text-slate-500 mb-1 block truncate text-center lg:text-left" title="{{ $team2->name }}">{{ $team2->name }}</label>
                                    <input type="number" name="quarter_scores[{{ $qNum }}][team2_score]" class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none transition-colors text-center font-bold text-xl" required min="0" placeholder="0" value="{{ $quarterData->team2_score ?? 0 }}">
                                </div>
                            </div>
                            @if ($qNum > 1) {{-- Jangan izinkan menghapus kuarter pertama secara default --}}
                                <button type="button" class="remove-quarter-btn absolute right-4 top-4 lg:top-1/2 lg:-translate-y-1/2 size-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors border border-red-500/20 tooltip" data-tip="Remove Quarter">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Player Statistics Section Header -->
        <div class="mb-4 mt-12">
            <h3 class="text-2xl font-black text-white italic tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[28px]">analytics</span>
                Player Statistics per Quarter
            </h3>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            
            <!-- Team 1 Stats -->
            <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden relative shadow-2xl flex flex-col">
                <div class="bg-gradient-to-r from-blue-500/20 via-blue-500/5 to-transparent px-6 py-4 border-b border-[#393028]">
                    <h4 class="text-xl font-black text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="size-3 rounded-full bg-blue-500 flex-shrink-0"></span>
                        <span class="truncate">{{ $team1->name }}</span>
                    </h4>
                </div>
                
                <div class="p-4 md:p-6 relative z-10 w-full flex-grow space-y-6">
                    @foreach($players1 as $player)
                        <div class="player-quarter-stats bg-[#181411]/50 border border-[#393028] rounded-2xl overflow-hidden">
                            <div class="bg-[#1c1613] px-4 py-3 border-b border-[#393028]">
                                <h6 class="font-bold text-white text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-500 text-sm">person</span>
                                    {{ $player->name }}
                                </h6>
                            </div>
                            <div id="player-stats-{{ $player->id }}-container" class="p-3 md:p-4 space-y-4">
                                @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                                    @php
                                        $stats = $playerStats[$player->id][$qNum] ?? new \stdClass(); // Get existing stats or empty object
                                    @endphp
                                    <div class="player-stats bg-[#221914] p-3 rounded-xl border border-[#393028]/50" data-quarter-number="{{ $qNum }}">
                                        <label class="block font-black text-slate-500 text-xs uppercase tracking-widest mb-3 border-b border-[#393028] pb-1">Quarter {{ $qNum }}</label>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-x-2 gap-y-4 items-center">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-primary text-center mb-1">PTS</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][point]" min="0" value="{{ $stats->point ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FGM</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fgm]" min="0" value="{{ $stats->fgm ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FGA</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fga]" min="0" value="{{ $stats->fga ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FTA</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fta]" min="0" value="{{ $stats->fta ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FTM</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ftm]" min="0" value="{{ $stats->ftm ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-blue-400 text-center mb-1">ORB</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][orb]" min="0" value="{{ $stats->orb ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-sky-400 text-center mb-1">DRB</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][drb]" min="0" value="{{ $stats->drb ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-emerald-400 text-center mb-1">STL</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][stl]" min="0" value="{{ $stats->stl ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-indigo-400 text-center mb-1">AST</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ast]" min="0" value="{{ $stats->ast ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-rose-400 text-center mb-1">BLK</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][blk]" min="0" value="{{ $stats->blk ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-red-500 text-center mb-1">PF</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][pf]" min="0" value="{{ $stats->pf ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-orange-400 text-center mb-1">TO</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][to]" min="0" value="{{ $stats->to ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Team 2 Stats -->
            <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden relative shadow-2xl flex flex-col">
                <div class="bg-gradient-to-r from-red-500/20 via-red-500/5 to-transparent px-6 py-4 border-b border-[#393028]">
                    <h4 class="text-xl font-black text-white uppercase tracking-tight flex items-center gap-2">
                        <span class="size-3 rounded-full bg-red-500 flex-shrink-0"></span>
                        <span class="truncate">{{ $team2->name }}</span>
                    </h4>
                </div>
                
                <div class="p-4 md:p-6 relative z-10 w-full flex-grow space-y-6">
                    @foreach($players2 as $player)
                        <div class="player-quarter-stats bg-[#181411]/50 border border-[#393028] rounded-2xl overflow-hidden">
                            <div class="bg-[#1c1613] px-4 py-3 border-b border-[#393028]">
                                <h6 class="font-bold text-white text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-500 text-sm">person</span>
                                    {{ $player->name }}
                                </h6>
                            </div>
                            <div id="player-stats-{{ $player->id }}-container" class="p-3 md:p-4 space-y-4">
                                @for ($qNum = 1; $qNum <= $maxQuarter; $qNum++)
                                    @php
                                        $stats = $playerStats[$player->id][$qNum] ?? new \stdClass();
                                    @endphp
                                    <div class="player-stats bg-[#221914] p-3 rounded-xl border border-[#393028]/50" data-quarter-number="{{ $qNum }}">
                                        <label class="block font-black text-slate-500 text-xs uppercase tracking-widest mb-3 border-b border-[#393028] pb-1">Quarter {{ $qNum }}</label>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-x-2 gap-y-4 items-center">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-primary text-center mb-1">PTS</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][point]" min="0" value="{{ $stats->point ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FGM</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fgm]" min="0" value="{{ $stats->fgm ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FGA</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fga]" min="0" value="{{ $stats->fga ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FTA</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][fta]" min="0" value="{{ $stats->fta ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-slate-400 text-center mb-1">FTM</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ftm]" min="0" value="{{ $stats->ftm ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-blue-400 text-center mb-1">ORB</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][orb]" min="0" value="{{ $stats->orb ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-sky-400 text-center mb-1">DRB</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][drb]" min="0" value="{{ $stats->drb ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-emerald-400 text-center mb-1">STL</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][stl]" min="0" value="{{ $stats->stl ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-indigo-400 text-center mb-1">AST</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][ast]" min="0" value="{{ $stats->ast ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-rose-400 text-center mb-1">BLK</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][blk]" min="0" value="{{ $stats->blk ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-red-500 text-center mb-1">PF</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][pf]" min="0" value="{{ $stats->pf ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-orange-400 text-center mb-1">TO</span>
                                                <input type="number" name="player_stats[{{ $player->id }}][{{ $qNum }}][to]" min="0" value="{{ $stats->to ?? 0 }}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>

        <div class="flex justify-end gap-4 mt-8 mb-4">
            <a href="{{ route('dashboard.jadwal', $tournament->id) }}" class="px-6 py-3 rounded-xl font-bold bg-[#221914] text-slate-300 hover:text-white border border-[#393028] hover:border-slate-500 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl font-black bg-primary text-[#181411] hover:bg-[#e67e22] transition-colors shadow-[0_0_20px_rgba(244,140,37,0.3)] border border-primary/50">
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let quarterCount = {{ $maxQuarter > 0 ? $maxQuarter : 1 }}; // Initialize with existing quarters or 1
        const addQuarterButton = document.getElementById('add-quarter-button');
        const quarterScoresContainer = document.getElementById('quarter-scores-container');
        
        // Pass team names dynamically to JS
        const team1Name = @json($team1->name);
        const team2Name = @json($team2->name);

        const allPlayerStatContainers = document.querySelectorAll('[id^="player-stats-"][id$="-container"]');

        // Add event listeners for initial "remove quarter" buttons
        document.querySelectorAll('.remove-quarter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const quarterDiv = this.closest('.quarter-input-group');
                const qNumToRemove = parseInt(quarterDiv.getAttribute('data-quarter-number'));
                removeQuarter(qNumToRemove, quarterDiv);
            });
        });

        addQuarterButton.addEventListener('click', function() {
            quarterCount++;
            addQuarterInput(quarterCount);
            addPlayerStatsInputsForNewQuarter(quarterCount);
        });

        function addQuarterInput(qNum) {
            const quarterDiv = document.createElement('div');
            quarterDiv.classList.add('quarter-input-group', 'glass-panel', 'bg-[#181411]/50', 'border', 'border-[#393028]', 'p-4', 'rounded-2xl', 'flex', 'flex-col', 'lg:flex-row', 'items-center', 'gap-6', 'relative', 'mt-4');
            quarterDiv.setAttribute('data-quarter-number', qNum);
            quarterDiv.innerHTML = `
                <div class="w-full lg:w-auto font-black text-slate-400 uppercase tracking-widest text-sm text-center">Quarter ${qNum}</div>
                <div class="flex-1 flex w-full items-center justify-center lg:justify-start gap-4 mx-auto max-w-lg lg:max-w-none pr-0 lg:pr-12">
                    <div class="relative flex-1">
                        <label class="text-xs font-bold text-slate-500 mb-1 block truncate text-center lg:text-left" title="${team1Name}">${team1Name}</label>
                        <input type="number" name="quarter_scores[${qNum}][team1_score]" class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none transition-colors text-center font-bold text-xl" required min="0" placeholder="0">
                    </div>
                    <div class="text-xl font-black text-slate-600 italic px-2 mt-4">VS</div>
                    <div class="relative flex-1">
                        <label class="text-xs font-bold text-slate-500 mb-1 block truncate text-center lg:text-left" title="${team2Name}">${team2Name}</label>
                        <input type="number" name="quarter_scores[${qNum}][team2_score]" class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none transition-colors text-center font-bold text-xl" required min="0" placeholder="0">
                    </div>
                </div>
                <button type="button" class="remove-quarter-btn absolute right-4 top-4 lg:top-1/2 lg:-translate-y-1/2 size-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors border border-red-500/20 tooltip" data-tip="Remove Quarter">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            `;
            quarterScoresContainer.appendChild(quarterDiv);

            quarterDiv.querySelector('.remove-quarter-btn').addEventListener('click', function() {
                removeQuarter(qNum, quarterDiv);
            });
        }

        function addPlayerStatsInputsForNewQuarter(qNum) {
            allPlayerStatContainers.forEach(container => {
                const playerId = container.id.split('-')[2]; // Extract player ID
                container.appendChild(createPlayerQuarterStatsHtml(playerId, qNum));
            });
        }

        function createPlayerQuarterStatsHtml(playerId, qNum, stats = {}) {
            const div = document.createElement('div');
            div.classList.add('player-stats', 'bg-[#221914]', 'p-3', 'rounded-xl', 'border', 'border-[#393028]/50', 'mt-4');
            div.setAttribute('data-quarter-number', qNum);
            
            const statsConfig = [
                { key: 'point', label: 'PTS', color: 'text-primary' },
                { key: 'fgm', label: 'FGM', color: 'text-slate-400' },
                { key: 'fga', label: 'FGA', color: 'text-slate-400' },
                { key: 'fta', label: 'FTA', color: 'text-slate-400' },
                { key: 'ftm', label: 'FTM', color: 'text-slate-400' },
                { key: 'orb', label: 'ORB', color: 'text-blue-400' },
                { key: 'drb', label: 'DRB', color: 'text-sky-400' },
                { key: 'stl', label: 'STL', color: 'text-emerald-400' },
                { key: 'ast', label: 'AST', color: 'text-indigo-400' },
                { key: 'blk', label: 'BLK', color: 'text-rose-400' },
                { key: 'pf', label: 'PF', color: 'text-red-500' },
                { key: 'to', label: 'TO', color: 'text-orange-400' }
            ];

            let inputsHtml = statsConfig.map(stat => `
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold ${stat.color} text-center mb-1">${stat.label}</span>
                    <input type="number" name="player_stats[${playerId}][${qNum}][${stat.key}]" min="0" value="${stats[stat.key] || ''}" class="stat-input w-full bg-[#181411] border border-[#393028] text-white rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-colors text-center text-sm" placeholder="0">
                </div>
            `).join('');

            div.innerHTML = `
                <label class="block font-black text-slate-500 text-xs uppercase tracking-widest mb-3 border-b border-[#393028] pb-1 flex justify-between items-center">
                    <span>Quarter ${qNum}</span>
                </label>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-x-2 gap-y-4 items-center">
                    ${inputsHtml}
                </div>
            `;
            return div;
        }

        function removeQuarter(qNumToRemove, quarterDiv) {
            quarterDiv.remove(); // Remove quarter score input

            // Remove corresponding player stats for this quarter
            allPlayerStatContainers.forEach(container => {
                const playerStatsForQuarter = container.querySelector(`.player-stats[data-quarter-number="${qNumToRemove}"]`);
                if (playerStatsForQuarter) {
                    playerStatsForQuarter.remove();
                }
            });

            // Re-index remaining quarters (important for form submission)
            let currentQuarter = 1;
            document.querySelectorAll('.quarter-input-group').forEach(qDiv => {
                const oldQuarterNumber = qDiv.getAttribute('data-quarter-number');
                if (oldQuarterNumber != currentQuarter) {
                    qDiv.setAttribute('data-quarter-number', currentQuarter);
                    
                    const labelDiv = qDiv.querySelector('.font-black.text-slate-400');
                    if (labelDiv) labelDiv.textContent = `Quarter ${currentQuarter}`;
                    
                    qDiv.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(`[${oldQuarterNumber}]`, `[${currentQuarter}]`);
                    });
                }
                // Update button for removing quarter
                let removeButton = qDiv.querySelector('.remove-quarter-btn');
                if (currentQuarter === 1) { // Don't allow removing quarter 1
                    if (removeButton) removeButton.remove();
                } else {
                    if (!removeButton) { // Add back if it was removed
                        removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'remove-quarter-btn absolute right-4 top-4 lg:top-1/2 lg:-translate-y-1/2 size-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors border border-red-500/20 tooltip';
                        removeButton.setAttribute('data-tip', 'Remove Quarter');
                        removeButton.innerHTML = `<span class="material-symbols-outlined text-sm">close</span>`;
                        qDiv.appendChild(removeButton);
                        removeButton.addEventListener('click', function() {
                            removeQuarter(currentQuarter, qDiv);
                        });
                    }
                }
                currentQuarter++;
            });

            // Re-index player stats accordingly
            allPlayerStatContainers.forEach(container => {
                let currentPlayerQuarter = 1;
                container.querySelectorAll('.player-stats').forEach(psDiv => {
                    const oldPsQuarterNumber = psDiv.getAttribute('data-quarter-number');
                    if (oldPsQuarterNumber != currentPlayerQuarter) {
                        psDiv.setAttribute('data-quarter-number', currentPlayerQuarter);
                        
                        const labelSpan = psDiv.querySelector('label span');
                        if (labelSpan) labelSpan.textContent = `Quarter ${currentPlayerQuarter}`;
                        
                        psDiv.querySelectorAll('input').forEach(input => {
                            const playerId = input.name.split('[')[1].replace(']', '');
                            input.name = `player_stats[${playerId}][${currentPlayerQuarter}]${input.name.split(']')[2]}`;
                        });
                    }
                    currentPlayerQuarter++;
                });
            });

            quarterCount = currentQuarter - 1; // Update quarterCount after re-indexing
        }
    });
</script>
@endsection