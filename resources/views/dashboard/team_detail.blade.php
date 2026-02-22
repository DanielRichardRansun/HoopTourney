@extends('layouts.app2')

@section('title', 'Team Detail - ' . $team->name)

@section('content')
@php
    $playersData = $players->map(function($player, $index) {
        return [
            'original_index' => $index + 1,
            'name' => $player->name,
            'jersey_number' => (int)($player->jersey_number ?: 0),
            'jersey_display' => $player->jersey_number ?: '-',
            'position' => $player->position ?: '-',
            'photo' => $player->photo,
        ];
    });
@endphp

<div class="space-y-8 max-w-5xl mx-auto" x-data="{
    search: '',
    sortField: 'jersey_number',
    sortDirection: 'asc',
    perPage: 10,
    currentPage: 1,
    items: {{ json_encode($playersData) }},
    get filteredItems() {
        let filtered = this.items.filter(item => 
            item.name.toLowerCase().includes(this.search.toLowerCase()) ||
            item.position.toLowerCase().includes(this.search.toLowerCase())
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
            this.sortDirection = 'asc';
        }
    }
}">
    
    <!-- Header/Team Info Card -->
    <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden relative shadow-2xl">
        <!-- Abstract Background Pattern -->
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-primary/50 via-[#1c1613] to-[#181411]"></div>
        
        <div class="p-8 relative z-10 flex flex-col md:flex-row items-center gap-8">
            <!-- Team Logo -->
            <div class="size-32 rounded-full bg-[#181411] border-4 border-[#393028] flex items-center justify-center overflow-hidden shrink-0 shadow-[0_0_30px_rgba(244,140,37,0.15)] relative">
                @if(isset($team->logo) && $team->logo)
                    <img src="{{ asset('images/logos/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-slate-600 text-[64px]">sports_basketball</span>
                @endif
            </div>

            <!-- Team Details -->
            <div class="flex-grow text-center md:text-left space-y-4">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-1">Team Information</h2>
                    <h1 class="text-4xl font-black text-white italic tracking-tight">{{ $team->name }}</h1>
                </div>

                <div class="flex flex-col sm:flex-row gap-6 justify-center md:justify-start">
                    <div class="flex items-center gap-3 bg-[#181411]/50 px-4 py-2 rounded-2xl border border-[#393028]">
                        <span class="material-symbols-outlined text-slate-500">sports</span>
                        <div class="flex flex-col text-left">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Coach</span>
                            <span class="text-sm font-bold text-slate-200">{{ $team->coach ?: 'Not specified' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-[#181411]/50 px-4 py-2 rounded-2xl border border-[#393028]">
                        <span class="material-symbols-outlined text-slate-500">assignment_ind</span>
                        <div class="flex flex-col text-left">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Manager</span>
                            <span class="text-sm font-bold text-slate-200">{{ $team->manager ?: 'Not specified' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="absolute top-6 right-6 md:static">
                <a href="{{ route('tournament.teams', $tournament->id) }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#221914] border border-[#393028] text-slate-300 hover:text-white hover:border-primary/40 hover:bg-primary/5 transition-all group shadow-lg">
                    <span class="material-symbols-outlined text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    <span class="text-xs font-black uppercase tracking-widest hidden md:inline">Back to Teams</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Players Roster -->
    <div class="space-y-6">
        <h3 class="text-2xl font-black text-white italic tracking-tight flex items-center gap-3 px-2">
            <span class="material-symbols-outlined text-primary text-[28px]">groups</span>
            Player Roster
        </h3>

        <div class="glass-panel border border-[#393028] rounded-3xl overflow-hidden shadow-2xl relative">
            <div class="p-6 relative z-10">
                @if($players->isEmpty())
                    <div class="py-20 flex flex-col items-center justify-center text-center">
                        <div class="size-20 rounded-3xl bg-[#221914] border border-[#393028] flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-4xl text-slate-700">person_off</span>
                        </div>
                        <h4 class="text-xl font-black text-white italic uppercase tracking-tight mb-2">No Players Listed</h4>
                        <p class="text-slate-500 text-sm font-medium">There are currently no players assigned to this team.</p>
                    </div>
                @else
                    <!-- Custom Table Controls -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Show</label>
                            <div class="relative">
                                <select x-model="perPage" @change="currentPage = 1" class="bg-[#1c1613] border border-[#393028] text-primary font-bold rounded-xl px-4 py-2 text-xs outline-none focus:border-primary/50 transition-colors cursor-pointer appearance-none pr-10">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg pointer-events-none">expand_more</span>
                            </div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Entries</label>
                        </div>
                        
                        <div class="relative w-full md:w-72">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xl">search</span>
                            <input type="text" x-model="search" @input="currentPage = 1" placeholder="Search roster..." class="w-full bg-[#1c1613] border border-[#393028] text-white rounded-2xl pl-12 pr-4 py-3 text-sm outline-none focus:border-primary/50 focus:shadow-[0_0_20px_rgba(244,140,37,0.1)] transition-all placeholder:text-slate-600 font-medium">
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                            <thead>
                                <tr class="border-b border-[#393028]">
                                    <th @click="setSort('original_index')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/50 w-16 text-center cursor-pointer hover:text-white transition-colors">
                                        <div class="flex items-center justify-center gap-2">
                                            # <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'original_index'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                        </div>
                                    </th>
                                    <th @click="setSort('name')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/50 cursor-pointer hover:text-white transition-colors">
                                        <div class="flex items-center gap-2">
                                            Player Name <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'name'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                        </div>
                                    </th>
                                    <th @click="setSort('jersey_number')" class="px-6 py-5 text-[10px] font-black text-primary/70 uppercase tracking-[0.2em] bg-[#1c1613]/50 text-center cursor-pointer hover:text-primary transition-colors">
                                        <div class="flex items-center justify-center gap-2">
                                            No. <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'jersey_number'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                        </div>
                                    </th>
                                    <th @click="setSort('position')" class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] bg-[#1c1613]/50 text-center cursor-pointer hover:text-white transition-colors">
                                        <div class="flex items-center justify-center gap-2">
                                            Position <span class="material-symbols-outlined text-xs text-primary" x-show="sortField === 'position'" x-text="sortDirection === 'asc' ? 'expand_less' : 'expand_more'"></span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#393028]/30">
                                <template x-for="player in paginatedItems" :key="player.name">
                                    <tr class="hover:bg-white/[0.02] transition-colors group">
                                        <td class="px-6 py-5 text-center">
                                            <span class="text-slate-600 font-bold text-xs" x-text="player.original_index"></span>
                                        </td>
                                        <td class="px-6 py-5 text-sm">
                                            <div class="flex items-center gap-4">
                                                <div class="size-10 rounded-xl bg-[#221914] border border-[#393028] flex items-center justify-center shrink-0 group-hover:border-primary/40 transition-all overflow-hidden">
                                                    <template x-if="player.photo">
                                                        <img :src="'/images/profiles/' + player.photo" :alt="player.name" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!player.photo">
                                                        <span class="material-symbols-outlined text-[20px] text-slate-700 group-hover:text-primary/70">person</span>
                                                    </template>
                                                </div>
                                                <span class="font-bold text-white tracking-wide group-hover:text-primary transition-colors font-sans" x-text="player.name"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <div class="inline-flex size-11 items-center justify-center rounded-2xl bg-[#181411] border border-primary/20 text-primary font-black text-lg transition-all group-hover:shadow-[0_0_15px_rgba(244,140,37,0.15)] group-hover:bg-primary/5" x-text="player.jersey_display"></div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <template x-if="player.position !== '-'">
                                                <span class="px-4 py-1.5 rounded-xl bg-indigo-500/[0.03] text-indigo-400 font-black border border-indigo-500/20 text-[10px] uppercase tracking-widest shadow-lg" x-text="player.position"></span>
                                            </template>
                                            <template x-if="player.position === '-'">
                                                <span class="text-slate-700 italic text-xs">-</span>
                                            </template>
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
                @endif
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

