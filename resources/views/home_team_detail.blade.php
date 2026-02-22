@extends('layouts.app2')

@section('title', 'My Team - ' . $team->name)

@section('content')
@php
    $playersData = $players->map(function($player, $index) {
        return [
            'original_index' => $index + 1,
            'id' => $player->id,
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
    players: {{ json_encode($playersData) }},
    
    get filteredPlayers() {
        let filtered = this.players.filter(p => 
            p.name.toLowerCase().includes(this.search.toLowerCase()) ||
            p.position.toLowerCase().includes(this.search.toLowerCase())
        );
        
        filtered.sort((a, b) => {
            let modifier = this.sortDirection === 'asc' ? 1 : -1;
            let valA = a[this.sortField];
            let valB = b[this.sortField];
            
            if (typeof valA === 'string') {
                return valA.localeCompare(valB) * modifier;
            }
            return (valA - valB) * modifier;
        });
        
        return filtered;
    },

    get paginatedPlayers() {
        let start = (this.currentPage - 1) * this.perPage;
        return this.filteredPlayers.slice(start, start + this.perPage);
    },

    get totalPages() {
        return Math.ceil(this.filteredPlayers.length / this.perPage);
    },

    sortBy(field) {
        if (this.sortField === field) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortField = field;
            this.sortDirection = 'asc';
        }
        this.currentPage = 1;
    }
}">

    <!-- Flash Messages -->
    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="glass-panel border-l-4 border-green-500 p-4 mb-6 flex justify-between items-center animate-fade-in-down">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-green-500">check_circle</span>
            <p class="text-white font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-slate-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    @endif

    <!-- Team Info Card -->
    <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-2xl relative">
        <div class="absolute top-0 right-0 p-8 pt-12 -mr-16 -mt-16 opacity-10">
            <span class="material-symbols-outlined text-[160px] text-primary rotate-12">groups</span>
        </div>
        
        <div class="p-8 md:p-10 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h4 class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-3">Team Information</h4>
                    <h1 class="text-4xl md:text-5xl font-black text-white italic uppercase tracking-tight mb-2">
                        {{ $team->name }}
                    </h1>
                    <div class="flex flex-wrap gap-6 mt-6">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-xl">person</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Coach</p>
                                <p class="text-white font-black">{{ $team->coach ?: 'Not assigned' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-400 text-xl">manage_accounts</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Manager</p>
                                <p class="text-white font-black">{{ $team->manager ?: 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <button onclick="showEditTeamForm()" 
                            class="px-8 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-black uppercase tracking-widest transition-all shadow-lg shadow-amber-500/20 active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">edit</span>
                        Edit Team
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roster Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pt-4">
        <div>
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tight">Team Roster</h2>
            <p class="text-slate-500 text-sm mt-1">Manage your players and their details</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 group-focus-within:text-primary transition-colors">search</span>
                <input type="text" x-model="search" placeholder="Search roster..." 
                       class="bg-[#1c1613] border border-[#393028] text-white pl-12 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-primary transition-all w-64">
            </div>
            <button onclick="showAddPlayerForm()" 
                    class="bg-green-500 hover:bg-green-600 text-black font-black uppercase tracking-widest px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-green-500/20 active:scale-95 flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined text-xl">person_add</span>
                Add Player
            </button>
        </div>
    </div>

    <!-- Players Table -->
    <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#393028] bg-[#1c1613]/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">#</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest cursor-pointer group" @click="sortBy('name')">
                            <div class="flex items-center gap-2">
                                Name
                                <span class="material-symbols-outlined text-sm text-slate-600 transition-transform" :class="sortField === 'name' ? (sortDirection === 'asc' ? '' : 'rotate-180') : ''">expand_more</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest cursor-pointer group text-center" @click="sortBy('jersey_number')">
                            <div class="flex items-center justify-center gap-2">
                                Jersey
                                <span class="material-symbols-outlined text-sm text-slate-600 transition-transform" :class="sortField === 'jersey_number' ? (sortDirection === 'asc' ? '' : 'rotate-180') : ''">expand_more</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Position</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#393028]/50">
                    <template x-for="(player, index) in paginatedPlayers" :key="player.id">
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs" x-text="(currentPage - 1) * perPage + index + 1"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-12 rounded-2xl bg-[#1c1613] border border-[#393028] flex items-center justify-center shrink-0 overflow-hidden relative group-hover:border-primary/50 transition-all shadow-lg">
                                        <template x-if="player.photo">
                                            <img :src="'/images/profiles/' + player.photo" :alt="player.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!player.photo">
                                            <span class="material-symbols-outlined text-slate-700 text-2xl group-hover:text-primary/50 transition-colors">person</span>
                                        </template>
                                    </div>
                                    <span class="text-white font-bold group-hover:text-primary transition-colors" x-text="player.name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg bg-[#221914] border border-[#393028] text-primary font-black tabular-nums" x-text="player.jersey_display"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400 bg-white/5 px-3 py-1 rounded-full border border-white/5" x-text="player.position"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="showEditForm(player.id, player.name, player.jersey_number, player.position)" 
                                            class="p-2 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-black transition-all">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </button>
                                    <button @click="showDeleteConfirm(player.id, player.name)" 
                                            class="p-2 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-black transition-all">
                                        <span class="material-symbols-outlined text-xl">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 bg-[#181411]/50 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-[#393028]">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">
                Showing <span class="text-white" x-text="Math.min(filteredPlayers.length, (currentPage-1)*perPage + 1)"></span> to 
                <span class="text-white" x-text="Math.min(filteredPlayers.length, currentPage * perPage)"></span> of 
                <span class="text-white" x-text="filteredPlayers.length"></span> players
            </p>
            <div class="flex items-center gap-2">
                <button @click="currentPage--" :disabled="currentPage === 1" 
                        class="p-2 rounded-lg bg-[#221914] border border-[#393028] text-slate-400 hover:text-white disabled:opacity-30 disabled:hover:text-slate-400 transition-all">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in totalPages" :key="page">
                        <button @click="currentPage = page" 
                                :class="currentPage === page ? 'bg-primary text-black' : 'bg-[#221914] text-slate-400 hover:text-white border border-[#393028]'"
                                class="size-8 rounded-lg text-xs font-black transition-all" x-text="page"></button>
                    </template>
                </div>
                <button @click="currentPage++" :disabled="currentPage === totalPages" 
                        class="p-2 rounded-lg bg-[#221914] border border-[#393028] text-slate-400 hover:text-white disabled:opacity-30 disabled:hover:text-slate-400 transition-all">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

    @if($players->isEmpty())
    <div class="glass-panel border border-[#393028] rounded-2xl overflow-hidden shadow-xl mt-8">
        <div class="py-16 flex flex-col items-center justify-center text-center">
            <div class="size-20 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-slate-600">person_off</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No Players Found</h3>
            <p class="text-slate-500 max-w-sm">No players have been added to this team yet. Click "Add Player" to get started.</p>
        </div>
    </div>
    @endif

    <!-- Modals Overlay -->
    <div id="formOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[999]" style="display: none;"></div>

    <!-- Floating Forms -->
    @include('partials.edit-team-form')
    @include('partials.add-player-form')
    @include('partials.edit-player-form')
    @include('partials.delete-player-confirm')

</div>
@endsection

@push('styles')
<style>
    .glass-panel {
        background: rgba(24, 20, 17, 0.7);
        backdrop-filter: blur(12px);
    }
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.5s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Bridge functions from Alpine to legacy partials if needed, 
    // though the partials should ideally be updated too.
    // For now, keeping the global functions they expect.

    function showEditTeamForm() {
        document.getElementById('editTeamForm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }
    
    function showAddPlayerForm() {
        document.getElementById('addPlayerForm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }

    function showEditForm(id, name, jersey, position) {
        document.getElementById('edit_player_id').value = id;
        document.getElementById('edit_player_name').value = name;
        document.getElementById('edit_jersey_number').value = jersey;
        document.getElementById('edit_position').value = position;
        
        // Update form action URL
        const form = document.getElementById('editPlayerFormAction');
        form.action = `/players/${id}`;
        form.dataset.playerId = id;
        
        document.getElementById('editPlayerForm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }

    function showDeleteConfirm(id, name) {
        document.getElementById('delete_player_name').textContent = name;
        
        // Update form action URL
        const form = document.getElementById('deletePlayerForm');
        form.action = `/players/${id}`;
        form.dataset.playerId = id;
        
        document.getElementById('deletePlayerConfirm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }

    function closeAllForms() {
        document.querySelectorAll('.floating-form').forEach(form => {
            form.style.display = 'none';
        });
        document.getElementById('formOverlay').style.display = 'none';
    }

    // Modal close listeners
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('formOverlay');
        if (overlay) overlay.addEventListener('click', closeAllForms);
    });

    // Handle AJAX responses (legacy logic preservation)
    function handleResponse(response) {
        return response.json().then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        });
    }

    function handleError(error) {
        console.error('Error:', error);
        alert('An error occurred during the request.');
    }
</script>
@endpush
