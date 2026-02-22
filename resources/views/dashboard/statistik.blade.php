@extends('layouts.app2')

@section('title', 'Statistics - ' . $tournament->name)

@section('content')
@php
    $playersData = $players->map(function($player) {
        return [
            'name' => $player->name,
            'photo_url' => $player->photo ? asset('images/profiles/' . $player->photo) : null,
            'per' => (float)($player->total_stats['per'] ?? 0),
            'pts' => (int)($player->total_stats['point'] ?? 0),
            'fgm' => (int)($player->total_stats['fgm'] ?? 0),
            'fga' => (int)($player->total_stats['fga'] ?? 0),
            'fta' => (int)($player->total_stats['fta'] ?? 0),
            'ftm' => (int)($player->total_stats['ftm'] ?? 0),
            'orb' => (int)($player->total_stats['orb'] ?? 0),
            'drb' => (int)($player->total_stats['drb'] ?? 0),
            'stl' => (int)($player->total_stats['stl'] ?? 0),
            'ast' => (int)($player->total_stats['ast'] ?? 0),
            'blk' => (int)($player->total_stats['blk'] ?? 0),
            'pf' => (int)($player->total_stats['pf'] ?? 0),
            'to' => (int)($player->total_stats['to'] ?? 0),
        ];
    });
@endphp

<div class="space-y-6 max-w-[1400px] mx-auto" x-data="{
    search: '',
    sortField: 'per',
    sortDirection: 'desc',
    perPage: 10,
    currentPage: 1,
    items: {{ json_encode($playersData) }},
    get filteredItems() {
        let filtered = this.items.filter(item => 
            item.name.toLowerCase().includes(this.search.toLowerCase())
        );
        
        filtered.sort((a, b) => {
            let valA = a[this.sortField];
            let valB = b[this.sortField];
            
            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();

            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
        
        return filtered;
    },
    get paginatedItems() {
        let start = (this.currentPage - 1) * this.perPage;
        return this.filteredItems.slice(start, start + parseInt(this.perPage));
    },
    get totalPages() {
        return Math.ceil(this.filteredItems.length / parseInt(this.perPage));
    },
    setSort(field) {
        if (this.sortField === field) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortField = field;
            this.sortDirection = 'desc';
        }
    }
}">

    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Player Statistics</h1>
            <p class="text-slate-400 text-sm mt-1">Average player stats for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
        </div>

        <!-- Filter Team -->
        <div class="w-full md:w-72">
            <form method="GET" class="relative">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Filter by Team</label>
                <div class="relative">
                    <select name="team_id" class="w-full bg-[#181411] border border-[#393028] text-white text-sm rounded-2xl focus:ring-primary focus:border-primary block p-3 pr-10 appearance-none font-bold shadow-inner transition-colors hover:border-primary/40 cursor-pointer" onchange="this.form.submit()">
                        <option value="" class="bg-[#181411]">All Teams</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }} class="bg-[#181411]">
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <span class="material-symbols-outlined text-[20px]">filter_list</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden shadow-2xl relative">
        <!-- Subtle glow -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="p-6 relative z-10">
            <!-- Custom Table Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Show</label>
                    <div class="relative">
                        <select x-model="perPage" @change="currentPage = 1" class="bg-[#1c1613] border border-[#393028] text-primary font-bold rounded-xl px-4 py-2 text-xs outline-none focus:border-primary/50 transition-colors cursor-pointer appearance-none pr-10">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg pointer-events-none">expand_more</span>
                    </div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Entries</label>
                </div>
                
                <div class="relative w-full md:w-72">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xl">search</span>
                    <input type="text" x-model="search" @input="currentPage = 1" placeholder="Search players..." class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-2xl pl-12 pr-4 py-3 text-sm outline-none focus:border-primary/50 focus:shadow-[0_0_20px_rgba(244,140,37,0.1)] transition-all placeholder:text-slate-600 font-medium">
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar" @scroll.passive="scrollLeft = $event.target.scrollLeft">
                <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                    <thead>
                        <tr class="border-b border-[#393028]">
                            <th @click="setSort('name')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/50 sticky left-0 z-30 cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center gap-2">
                                    Player Name <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'name'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('per')" class="px-4 py-5 text-[10px] font-black text-amber-500/70 uppercase tracking-[0.2em] bg-amber-500/[0.03] text-center cursor-pointer hover:text-amber-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    PER <span class="material-symbols-outlined text-xs text-amber-400" x-show="sortField === 'per'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('pts')" class="px-4 py-5 text-[10px] font-black text-primary/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-primary transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    PTS <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'pts'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('fgm')" class="px-4 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    FGM <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'fgm'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('fga')" class="px-4 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    FGA <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'fga'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('ftm')" class="px-4 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    FTM <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'ftm'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('fta')" class="px-4 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    FTA <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'fta'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('orb')" class="px-4 py-5 text-[10px] font-black text-blue-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-blue-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    ORB <span class="material-symbols-outlined text-xs text-blue-400" x-show="sortField === 'orb'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('drb')" class="px-4 py-5 text-[10px] font-black text-sky-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-sky-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    DRB <span class="material-symbols-outlined text-xs text-sky-400" x-show="sortField === 'drb'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('stl')" class="px-4 py-5 text-[10px] font-black text-emerald-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-emerald-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    STL <span class="material-symbols-outlined text-xs text-emerald-400" x-show="sortField === 'stl'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('ast')" class="px-4 py-5 text-[10px] font-black text-indigo-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-indigo-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    AST <span class="material-symbols-outlined text-xs text-indigo-400" x-show="sortField === 'ast'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('blk')" class="px-4 py-5 text-[10px] font-black text-rose-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-rose-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    BLK <span class="material-symbols-outlined text-xs text-rose-400" x-show="sortField === 'blk'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('pf')" class="px-4 py-5 text-[10px] font-black text-red-500/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-red-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    PF <span class="material-symbols-outlined text-xs text-red-400" x-show="sortField === 'pf'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('to')" class="px-4 py-5 text-[10px] font-black text-orange-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-orange-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    TO <span class="material-symbols-outlined text-xs text-orange-400" x-show="sortField === 'to'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#393028]/30">
                        <template x-for="player in paginatedItems" :key="player.name">
                            <tr class="hover:bg-white/[0.02] transition-colors group text-xs">
                                <td class="px-6 py-4 font-bold text-white tracking-wide sticky left-0 z-20 bg-[#181411] transition-colors shadow-[5px_0_10px_-5px_rgba(0,0,0,0.5)]">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-[#221914] border border-[#393028] flex items-center justify-center overflow-hidden shrink-0 group-hover:border-primary/40 transition-all">
                                            <template x-if="player.photo_url">
                                                <img :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!player.photo_url">
                                                <span class="material-symbols-outlined text-slate-700 text-[20px]">person</span>
                                            </template>
                                        </div>
                                        <span class="truncate max-w-[150px] group-hover:text-primary transition-colors text-sm" x-text="player.name"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center font-black text-amber-400 bg-amber-500/[0.03]" x-text="player.per"></td>
                                <td class="px-4 py-4 text-center font-bold text-primary" x-text="player.pts"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-400" x-text="player.fgm"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-400" x-text="player.fga"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-400" x-text="player.ftm"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-400" x-text="player.fta"></td>
                                <td class="px-4 py-4 text-center font-bold text-blue-400/80" x-text="player.orb"></td>
                                <td class="px-4 py-4 text-center font-bold text-sky-400" x-text="player.drb"></td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-400" x-text="player.stl"></td>
                                <td class="px-4 py-4 text-center font-bold text-indigo-400" x-text="player.ast"></td>
                                <td class="px-4 py-4 text-center font-bold text-rose-400" x-text="player.blk"></td>
                                <td class="px-4 py-4 text-center font-semibold text-red-500/80" x-text="player.pf"></td>
                                <td class="px-4 py-4 text-center font-semibold text-orange-400/80" x-text="player.to"></td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <template x-if="filteredItems.length === 0">
                            <tr>
                                <td colspan="14" class="px-6 py-20 text-center sticky left-0">
                                    <div class="flex flex-col items-center gap-3 opacity-40">
                                        <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                        <p class="text-sm font-bold uppercase tracking-widest text-slate-500">No players found matching your search</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 mt-10" x-show="filteredItems.length > 0">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">
                    Showing <span class="text-slate-200" x-text="((currentPage - 1) * perPage) + 1"></span> 
                    to <span class="text-slate-200" x-text="Math.min(currentPage * perPage, filteredItems.length)"></span> 
                    of <span class="text-slate-200" x-text="filteredItems.length"></span> players
                </div>
                
                <div class="flex items-center gap-1.5">
                    <button @click="currentPage--" :disabled="currentPage === 1" 
                            class="size-10 flex items-center justify-center rounded-2xl border border-[#393028] bg-[#1c1613] text-slate-500 disabled:opacity-20 disabled:cursor-not-allowed hover:bg-[#221914] hover:text-white transition-all group">
                        <span class="material-symbols-outlined text-xl group-hover:-translate-x-0.5 transition-transform">chevron_left</span>
                    </button>
                    
                    <div class="flex items-center gap-1.5 px-2">
                        <template x-for="p in totalPages" :key="p">
                            <button @click="currentPage = p" 
                                    :class="currentPage === p ? 'bg-primary/10 border-primary text-primary shadow-[0_0_15px_rgba(244,140,37,0.15)]' : 'bg-[#1c1613] border-[#393028] text-slate-500 hover:text-slate-200 hover:border-[#4b4237]'" 
                                    class="size-10 flex items-center justify-center rounded-2xl border font-black text-[11px] transition-all" 
                                    x-text="p"></button>
                        </template>
                    </div>

                    <button @click="currentPage++" :disabled="currentPage === totalPages" 
                            class="size-10 flex items-center justify-center rounded-2xl border border-[#393028] bg-[#1c1613] text-slate-500 disabled:opacity-20 disabled:cursor-not-allowed hover:bg-[#221914] hover:text-white transition-all group">
                        <span class="material-symbols-outlined text-xl group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(24, 20, 17, 0.8); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #393028; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b4237; }

    /* Sticky Header fix */
    thead th.sticky {
        top: 0;
        z-index: 40;
    }
</style>
@endpush

