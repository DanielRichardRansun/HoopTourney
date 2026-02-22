@extends('layouts.app2')

@section('title', 'Jadwal - ' . $tournament->name)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Match Schedule</h1>
            <p class="text-slate-400 text-sm mt-1">Schedules and results for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
        </div>
        
        <!-- Filter Status -->
        <div class="glass-panel px-4 py-2 rounded-xl border border-[#393028] flex items-center gap-3">
            <span class="material-symbols-outlined text-slate-500 text-[18px]">filter_list</span>
            <select id="filterStatus" onchange="filterSchedule()" class="bg-transparent text-white text-sm font-bold outline-none cursor-pointer border-none focus:ring-0 [&>option]:bg-[#1c1613]">
                <option value="all">All Matches</option>
                <option value="scheduled">Scheduled</option>
                <option value="postponed">Postponed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>
            <span class="material-symbols-outlined text-slate-500 text-[18px] pointer-events-none -ml-2">expand_more</span>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-400/50 hover:text-emerald-400 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-500 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <span class="font-semibold text-sm">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-400/50 hover:text-red-400 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Schedule Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @forelse($schedules as $index => $schedule)
            @php
                // Score formatting logic
                $team1Class = 'text-white border-transparent';
                $team2Class = 'text-white border-transparent';
                $score1 = '-';
                $score2 = '-';
                
                if ($schedule->matchResult) {
                    $score1 = $schedule->matchResult->team1_score ?? '-';
                    $score2 = $schedule->matchResult->team2_score ?? '-';

                    if ($score1 !== '-' && $score2 !== '-') {
                        if ($score1 == $score2) {
                            $team1Class = 'text-slate-400 border-slate-600 bg-slate-800/50';
                            $team2Class = 'text-slate-400 border-slate-600 bg-slate-800/50';
                        } elseif ($schedule->matchResult->winning_team_id == $schedule->team1->id) {
                            $team1Class = 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.15)]';
                            $team2Class = 'text-red-400 border-red-500/30 bg-red-500/10';
                        } elseif ($schedule->matchResult->winning_team_id == $schedule->team2->id) {
                            $team1Class = 'text-red-400 border-red-500/30 bg-red-500/10';
                            $team2Class = 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.15)]';
                        }
                    }
                }

                // Status Badge logic
                $statusClass = 'bg-slate-700/20 text-slate-400 border-slate-700/50';
                $statusIcon = 'help';
                if($schedule->status == 'Scheduled') { $statusClass = 'bg-blue-500/20 text-blue-400 border-blue-500/50'; $statusIcon = 'event'; }
                elseif($schedule->status == 'Postponed') { $statusClass = 'bg-amber-500/20 text-amber-500 border-amber-500/50'; $statusIcon = 'schedule'; }
                elseif($schedule->status == 'Cancelled') { $statusClass = 'bg-red-500/20 text-red-500 border-red-500/50'; $statusIcon = 'cancel'; }
                elseif($schedule->status == 'Completed') { $statusClass = 'bg-emerald-500/20 text-emerald-500 border-emerald-500/50'; $statusIcon = 'check_circle'; }
            @endphp
            
            <div class="schedule-card glass-panel rounded-2xl border border-[#393028] overflow-hidden group hover:border-primary/50 transition-all duration-300 relative flex flex-col" data-status="{{ strtolower($schedule->status) }}">
                
                <!-- Game Header -->
                <div class="flex items-center justify-between p-4 bg-[#1c1613]/80 border-b border-[#393028]">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded bg-primary/20 text-primary text-[10px] font-black uppercase tracking-widest border border-primary/30">Match {{ $index + 1 }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full border {{ $statusClass }} text-[10px] uppercase font-bold tracking-wider">
                        <span class="material-symbols-outlined text-[12px]">{{ $statusIcon }}</span>
                        {{ $schedule->status }}
                    </div>
                </div>

                <!-- Match Details (Clickable via JS if needed, but keeping links on buttons to ensure no rogue clicks) -->
                <div class="p-6 flex-1 flex flex-col justify-center">
                    
                    <!-- Team Matchup -->
                    <div class="flex items-center justify-between gap-4 mb-8">
                        
                        <!-- Team 1 -->
                        <div class="flex-1 min-w-0 flex flex-col items-center gap-3 text-center">
                            <div class="size-16 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden shrink-0 group-hover:border-primary/30 transition-colors shadow-lg">
                                @if($schedule->team1 && $schedule->team1->logo)
                                    <img src="{{ asset('images/logos/' . $schedule->team1->logo) }}" alt="{{ $schedule->team1->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-700 text-3xl">sports_basketball</span>
                                @endif
                            </div>
                            <h3 class="text-sm md:text-lg font-black text-white uppercase tracking-tight truncate w-full px-1">{{ $schedule->team1 ? $schedule->team1->name : 'TBD' }}</h3>
                        </div>

                        <!-- Score Box -->
                        <div class="flex items-center gap-1 md:gap-2 shrink-0 self-center mb-6 px-1">
                            <div class="w-12 h-14 md:w-16 md:h-20 rounded-xl border-2 flex items-center justify-center text-xl md:text-3xl font-black {{ $team1Class }} transition-all">
                                {{ $score1 }}
                            </div>
                            <span class="text-slate-600 font-bold text-[10px] md:text-sm">VS</span>
                            <div class="w-12 h-14 md:w-16 md:h-20 rounded-xl border-2 flex items-center justify-center text-xl md:text-3xl font-black {{ $team2Class }} transition-all">
                                {{ $score2 }}
                            </div>
                        </div>

                        <!-- Team 2 -->
                        <div class="flex-1 min-w-0 flex flex-col items-center gap-3 text-center">
                            <div class="size-16 rounded-2xl bg-[#181411] border border-[#393028] flex items-center justify-center overflow-hidden shrink-0 group-hover:border-primary/30 transition-colors shadow-lg">
                                @if($schedule->team2 && $schedule->team2->logo)
                                    <img src="{{ asset('images/logos/' . $schedule->team2->logo) }}" alt="{{ $schedule->team2->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-700 text-3xl">sports_basketball</span>
                                @endif
                            </div>
                            <h3 class="text-sm md:text-lg font-black text-white uppercase tracking-tight truncate w-full px-1">{{ $schedule->team2 ? $schedule->team2->name : 'TBD' }}</h3>
                        </div>

                    </div>

                    <!-- Match Info -->
                    <div class="grid grid-cols-2 gap-4 text-center mt-auto">
                        <div class="bg-[#181411] border border-[#393028] rounded-xl p-3 flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-[20px] mb-1">calendar_clock</span>
                            <span class="text-white text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase mt-0.5">{{ \Carbon\Carbon::parse($schedule->date)->format('H:i') }}</span>
                        </div>
                        <div class="bg-[#181411] border border-[#393028] rounded-xl p-3 flex flex-col items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-[20px] mb-1">location_on</span>
                            <span class="text-white text-xs font-bold truncate w-full px-2">{{ $schedule->location ?: 'TBA' }}</span>
                            <span class="text-slate-500 text-[10px] font-bold uppercase mt-0.5">Venue</span>
                        </div>
                    </div>

                </div>

                <!-- Admin Action Buttons Base -->
                <div class="p-4 bg-[#1c1613]/80 border-t border-[#393028] flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-[#393028] text-slate-300 hover:text-white hover:bg-white/5 transition-colors font-bold text-xs uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        View Result
                    </a>

                    @if ($isAdmin)
                        {{-- Edit Jadwal Button --}}
                        <button type="button" onclick="openModal('{{ $schedule->id }}')" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-amber-500/30 text-amber-500 hover:bg-amber-500/10 transition-colors font-bold text-xs uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[16px]">edit_calendar</span>
                            Edit Match
                        </button>

                        {{-- Update Score Button --}}
                        @if ($schedule->matchResult)
                            <a href="{{ route('matchResults.edit', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-[0_2px_10px_rgba(79,70,229,0.3)] hover:scale-[1.02] transition-all font-bold text-xs uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[16px]">sports_score</span>
                                Edit Score
                            </a>
                        @else
                            <a href="{{ route('matchResults.create', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-[0_2px_10px_rgba(16,185,129,0.3)] hover:scale-[1.02] transition-all font-bold text-xs uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[16px]">add_box</span>
                                Add Score
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Edit Schedule Modal -->
            @if($isAdmin)
                <div id="editScheduleModal-{{ $schedule->id }}" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('{{ $schedule->id }}')"></div>
                    
                    <!-- Modal Content -->
                    <div class="glass-panel border border-[#393028] rounded-3xl w-full max-w-md relative z-10 overflow-hidden shadow-2xl animate-[fadeIn_0.2s_ease-out]">
                        
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-6 border-b border-[#393028] bg-[#1c1613]">
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Edit Match Schedule</h3>
                            <button onclick="closeModal('{{ $schedule->id }}')" class="text-slate-500 hover:text-white transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('schedule.update', $schedule->id) }}" method="POST" class="p-6 space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <!-- Team 1 -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Team 1 (Home)</label>
                                    @if(is_null($schedule->team1_id) || session('edit_team1')) 
                                        <select name="team1_id" class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors outline-none [&>option]:bg-[#181411]">
                                            <option value="">Pilih Tim</option>
                                            @foreach($tournamentTeams as $team)
                                                <option value="{{ $team->id }}" {{ old('team1_id', $schedule->team1_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="team1_id" value="{{ $schedule->team1_id }}">
                                        <div class="w-full bg-[#181411]/50 border border-[#393028]/50 text-slate-300 rounded-xl py-2.5 px-4 cursor-not-allowed">
                                            {{ $schedule->team1->name }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Team 2 -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Team 2 (Away)</label>
                                    @if(is_null($schedule->team2_id) || session('edit_team2')) 
                                        <select name="team2_id" class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors outline-none [&>option]:bg-[#181411]">
                                            <option value="">Pilih Tim</option>
                                            @foreach($tournamentTeams as $team)
                                                <option value="{{ $team->id }}" {{ old('team2_id', $schedule->team2_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="team2_id" value="{{ $schedule->team2_id }}">
                                        <div class="w-full bg-[#181411]/50 border border-[#393028]/50 text-slate-300 rounded-xl py-2.5 px-4 cursor-not-allowed">
                                            {{ $schedule->team2->name }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Date -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Match Date & Time</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">calendar_clock</span>
                                        <input type="datetime-local" name="date" value="{{ \Carbon\Carbon::parse($schedule->date)->format('Y-m-d\TH:i') }}" 
                                            class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-2.5 pl-11 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors outline-none [color-scheme:dark]">
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Venue / Location</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">location_on</span>
                                        <input type="text" name="location" value="{{ $schedule->location }}" placeholder="e.g. Main Court A"
                                            class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-2.5 pl-11 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors outline-none">
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Match Status</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[18px]">flag</span>
                                        <select name="status" required class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-2.5 pl-11 pr-10 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors outline-none appearance-none [&>option]:bg-[#181411]">
                                            <option value="Scheduled" {{ $schedule->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="Postponed" {{ $schedule->status == 'Postponed' ? 'selected' : '' }}>Postponed</option>
                                            <option value="Cancelled" {{ $schedule->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="Completed" {{ $schedule->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actons -->
                            <div class="pt-4 mt-6 border-t border-[#393028] flex gap-3">
                                <button type="button" onclick="closeModal('{{ $schedule->id }}')" class="flex-1 px-4 py-3 rounded-xl border border-[#393028] text-slate-400 hover:text-white hover:bg-[#221914] font-bold text-sm uppercase tracking-wider transition-all">Cancel</button>
                                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-primary to-orange-600 text-white shadow-[0_4px_15px_-5px_rgba(244,140,37,0.5)] hover:scale-[1.02] font-bold text-sm uppercase tracking-wider transition-all">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center glass-panel rounded-2xl border border-[#393028]">
                <div class="size-20 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-slate-600">event_busy</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Matches Scheduled</h3>
                <p class="text-slate-500 max-w-sm">There are currently no matches generated or available for this tournament.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
<script>
    function openModal(id) {
        const modal = document.getElementById('editScheduleModal-' + id);
        if(modal) {
            modal.style.display = 'flex';
            // Slight delay to ensure flex is applied before opacity transition
            setTimeout(() => { modal.classList.remove('hidden'); }, 10);
        }
    }

    function closeModal(id) {
        const modal = document.getElementById('editScheduleModal-' + id);
        if(modal) {
            modal.classList.add('hidden');
            setTimeout(() => { modal.style.display = 'none'; }, 300); // Wait for transition
        }
    }

    // Filter Logic
    function filterSchedule() {
        const selectedStatus = document.getElementById('filterStatus').value.toLowerCase();
        const cards = document.querySelectorAll('.schedule-card');
        
        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            if (selectedStatus === 'all' || status === selectedStatus) {
                card.parentElement.style.display = 'block'; // Or card.style.display depending on wrapper structure
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    }
</script>
@endpush
