@extends('layouts.app2')

@section('title', 'Klasemen - ' . $tournament->name)

@section('content')
@php
    $teamsData = $teams->map(function($team, $index) {
        return [
            'rank' => $index + 1,
            'name' => $team->name,
            'logo_url' => $team->logo ? asset('images/logos/' . $team->logo) : null,
            'matches_played' => $team->matches_played ?? 0,
            'wins' => $team->wins ?? 0,
            'losses' => $team->losses ?? 0,
            'total_points' => $team->total_points ?? 0,
            'avg_points_per_game' => number_format($team->avg_points_per_game ?? 0, 1),
            'avg_raw' => (float)($team->avg_points_per_game ?? 0)
        ];
    });
@endphp

<div class="space-y-6 max-w-6xl mx-auto" x-data="{
    search: '',
    sortField: 'avg_raw',
    sortDirection: 'desc',
    perPage: 10,
    currentPage: 1,
    items: {{ json_encode($teamsData) }},
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

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Team Standings</h1>
        <p class="text-slate-400 text-sm mt-1">Current rankings and statistics for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
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
                    <input type="text" x-model="search" @input="currentPage = 1" placeholder="Search teams..." class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-2xl pl-12 pr-4 py-3 text-sm outline-none focus:border-primary/50 focus:shadow-[0_0_20px_rgba(244,140,37,0.1)] transition-all placeholder:text-slate-600 font-medium">
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-[#393028]">
                            <th @click="setSort('rank')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center gap-2">
                                    # <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'rank'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('name')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center gap-2">
                                    Team Name <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'name'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('matches_played')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-white transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    P <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'matches_played'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('wins')" class="px-6 py-5 text-[10px] font-black text-emerald-500/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-emerald-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    W <span class="material-symbols-outlined text-xs text-emerald-400" x-show="sortField === 'wins'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('losses')" class="px-6 py-5 text-[10px] font-black text-red-500/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-red-400 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    L <span class="material-symbols-outlined text-xs text-red-400" x-show="sortField === 'losses'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('total_points')" class="px-6 py-5 text-[10px] font-black text-primary/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-primary transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    Pts <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'total_points'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                            <th @click="setSort('avg_raw')" class="px-6 py-5 text-[10px] font-black text-indigo-400/70 uppercase tracking-[0.2em] bg-[#1c1613]/30 text-center cursor-pointer hover:text-indigo-300 transition-colors">
                                <div class="flex items-center justify-center gap-2">
                                    Avg <span class="material-symbols-outlined text-xs text-indigo-400" x-show="sortField === 'avg_raw'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#393028]/30">
                        <template x-for="(team, index) in paginatedItems" :key="team.name">
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="size-8 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center text-xs font-black" 
                                         :class="team.rank <= 3 ? 'text-primary shadow-[0_0_15px_rgba(244,140,37,0.2)] border-primary/40' : 'text-slate-500'" 
                                         x-text="team.rank">
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="size-12 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden shrink-0 group-hover:border-primary/40 transition-all group-hover:shadow-[0_0_20px_rgba(244,140,37,0.1)]">
                                            <template x-if="team.logo_url">
                                                <img :src="team.logo_url" :alt="team.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!team.logo_url">
                                                <span class="material-symbols-outlined text-slate-700 text-[24px]">groups</span>
                                            </template>
                                        </div>
                                        <span class="font-bold text-white text-sm tracking-wide group-hover:text-primary transition-colors" x-text="team.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center font-bold text-slate-400 text-sm" x-text="team.matches_played"></td>
                                <td class="px-6 py-5 text-center font-bold text-emerald-400 text-sm" x-text="team.wins"></td>
                                <td class="px-6 py-5 text-center font-bold text-red-500 text-sm" x-text="team.losses"></td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-4 py-1.5 rounded-xl bg-primary/5 text-primary font-black border border-primary/20 text-xs shadow-[0_0_15px_rgba(244,140,37,0.05)]" x-text="team.total_points"></span>
                                </td>
                                <td class="px-6 py-5 text-center font-bold text-indigo-300 bg-indigo-500/5 text-sm" x-text="team.avg_points_per_game"></td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <template x-if="filteredItems.length === 0">
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3 opacity-40">
                                        <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                        <p class="text-sm font-bold uppercase tracking-widest text-slate-500">No teams found matching your search</p>
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
                    of <span class="text-slate-200" x-text="filteredItems.length"></span> teams
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
</style>
@endpush
