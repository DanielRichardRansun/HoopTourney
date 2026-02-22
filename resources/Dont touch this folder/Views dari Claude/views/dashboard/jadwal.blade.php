@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-blue-500 text-2xl">calendar_month</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Jadwal Lomba</h1>
        <p class="text-slate-400 text-sm">{{ $tournament->name }}</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="glass-panel rounded-xl p-4 mb-6 border-l-4 border-emerald-500 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <p class="text-emerald-400 text-sm font-semibold">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="glass-panel rounded-xl p-4 mb-6 border-l-4 border-red-500 flex items-center gap-3">
        <span class="material-symbols-outlined text-red-500">error</span>
        <p class="text-red-400 text-sm font-semibold">{{ session('error') }}</p>
    </div>
@endif

{{-- Filter --}}
<div class="glass-panel rounded-xl p-4 mb-6 border border-[#393028] flex flex-col sm:flex-row items-center gap-4">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-slate-400 text-[18px]">filter_list</span>
        <label class="text-slate-300 text-sm font-bold uppercase tracking-wider">Filter Status:</label>
    </div>
    <select id="filterStatus" class="bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm font-semibold min-w-[160px]" onchange="filterSchedule()">
        <option value="all">All</option>
        <option value="Scheduled">Scheduled</option>
        <option value="Postponed">Postponed</option>
        <option value="Cancelled">Cancelled</option>
        <option value="Completed">Completed</option>
    </select>
</div>

@if($schedules->isEmpty())
    <div class="glass-panel rounded-2xl p-16 text-center border border-[#393028] flex flex-col items-center gap-4">
        <div class="size-20 rounded-full bg-[#181411] flex items-center justify-center border border-[#393028]">
            <span class="material-symbols-outlined text-4xl text-slate-600">event_busy</span>
        </div>
        <h4 class="text-lg font-bold text-white">No Schedule Available</h4>
        <p class="text-slate-500 text-sm">No schedule available in this tournament.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($schedules as $index => $schedule)
            @php
                $team1Class = '';
                $team2Class = '';
                $team1Bg = 'bg-[#221914]';
                $team2Bg = 'bg-[#221914]';
                if ($schedule->matchResult) {
                    $score1 = $schedule->matchResult->team1_score;
                    $score2 = $schedule->matchResult->team2_score;
                    if ($score1 !== null && $score2 !== null) {
                        if ($score1 == $score2) {
                            $team1Bg = 'bg-slate-700/30'; $team2Bg = 'bg-slate-700/30';
                        } elseif ($schedule->matchResult->winning_team_id == $schedule->team1->id) {
                            $team1Bg = 'bg-emerald-500/10 border-emerald-500/30'; $team2Bg = 'bg-red-500/10 border-red-500/30';
                        } elseif ($schedule->matchResult->winning_team_id == $schedule->team2->id) {
                            $team1Bg = 'bg-red-500/10 border-red-500/30'; $team2Bg = 'bg-emerald-500/10 border-emerald-500/30';
                        }
                    }
                }
            @endphp

            <div class="schedule-card glass-panel rounded-2xl border border-[#393028] overflow-hidden hover:border-primary/30 transition-all hover:-translate-y-1 cursor-pointer shadow-lg group"
                 data-status="{{ strtolower($schedule->status) }}" onclick="window.location='{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}'">

                {{-- Game Header --}}
                <div class="bg-gradient-to-r from-primary/20 to-orange-600/10 px-5 py-3 border-b border-[#393028] flex items-center justify-between">
                    <span class="text-primary font-black text-xs uppercase tracking-widest">Game {{ $index + 1 }}</span>
                    @php
                        $statusClasses = [
                            'scheduled' => 'bg-slate-700/50 text-slate-300',
                            'postponed' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
                            'cancelled' => 'bg-red-500/20 text-red-400 border border-red-500/30',
                            'completed' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                        ];
                    @endphp
                    <span class="{{ $statusClasses[strtolower($schedule->status)] ?? 'bg-slate-700/50 text-slate-300' }} text-[9px] font-black px-2 py-0.5 rounded uppercase">
                        {{ ucfirst($schedule->status) }}
                    </span>
                </div>

                {{-- Teams & Score --}}
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex-1 text-center {{ $team1Bg }} rounded-xl p-3 border border-[#393028]">
                            <p class="text-white font-bold text-sm truncate">{{ $schedule->team1 ? $schedule->team1->name : 'TBD' }}</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $schedule->matchResult->team1_score ?? '-' }}</p>
                        </div>
                        <span class="text-slate-600 font-black text-xs uppercase">vs</span>
                        <div class="flex-1 text-center {{ $team2Bg }} rounded-xl p-3 border border-[#393028]">
                            <p class="text-white font-bold text-sm truncate">{{ $schedule->team2 ? $schedule->team2->name : 'TBD' }}</p>
                            <p class="text-2xl font-black text-white mt-1">{{ $schedule->matchResult->team2_score ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="space-y-2 pt-3 border-t border-[#393028]">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="material-symbols-outlined text-slate-500 text-[14px]">schedule</span>
                            <span class="text-slate-400">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y (H:i)') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="material-symbols-outlined text-slate-500 text-[14px]">location_on</span>
                            <span class="text-slate-400">{{ $schedule->location }}</span>
                        </div>
                    </div>

                    {{-- Admin Actions --}}
                    @if ($isAdmin)
                        <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-[#393028]" onclick="event.stopPropagation()">
                            <a href="javascript:void(0);" onclick="openModal({{ $schedule->id }})" class="flex-1 text-center py-2 rounded-lg bg-yellow-500/10 border border-yellow-500/30 text-yellow-500 hover:bg-yellow-500 hover:text-[#181411] font-bold text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">edit</span> Edit Jadwal
                            </a>
                            @if ($schedule->matchResult)
                                <a href="{{ route('matchResults.edit', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" class="flex-1 text-center py-2 rounded-lg bg-blue-500/10 border border-blue-500/30 text-blue-400 hover:bg-blue-500 hover:text-white font-bold text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">edit_note</span> Edit Hasil
                                </a>
                            @else
                                <a href="{{ route('matchResults.create', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" class="flex-1 text-center py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500 hover:text-white font-bold text-[10px] uppercase tracking-wider transition-all flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">add_circle</span> Insert Hasil
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Edit Schedule Modal --}}
            <div id="editScheduleModal-{{ $schedule->id }}" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
                <div class="glass-panel rounded-2xl p-6 border border-[#393028] w-full max-w-md shadow-2xl mx-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-white uppercase tracking-tight">Edit Detail Jadwal</h3>
                        <button onclick="closeModal({{ $schedule->id }})" class="size-8 rounded-lg bg-[#221914] border border-[#393028] flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    <form action="{{ route('schedule.update', $schedule->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-slate-400 text-xs font-bold mb-1.5 uppercase tracking-wider">Tim 1</label>
                            @if(is_null($schedule->team1_id) || session('edit_team1'))
                                <select name="team1_id" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm">
                                    <option value="">Pilih Tim</option>
                                    @foreach($tournamentTeams as $team)
                                        <option value="{{ $team->id }}" {{ old('team1_id', $schedule->team1_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @php session(['edit_team1' => true]); @endphp
                            @else
                                <input type="hidden" name="team1_id" value="{{ $schedule->team1_id }}">
                                <p class="text-white text-sm font-semibold bg-[#221914] border border-[#393028] rounded-xl px-4 py-2.5">{{ $schedule->team1->name }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-slate-400 text-xs font-bold mb-1.5 uppercase tracking-wider">Tim 2</label>
                            @if(is_null($schedule->team2_id) || session('edit_team2'))
                                <select name="team2_id" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm">
                                    <option value="">Pilih Tim</option>
                                    @foreach($tournamentTeams as $team)
                                        <option value="{{ $team->id }}" {{ old('team2_id', $schedule->team2_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                @php session(['edit_team2' => true]); @endphp
                            @else
                                <input type="hidden" name="team2_id" value="{{ $schedule->team2_id }}">
                                <p class="text-white text-sm font-semibold bg-[#221914] border border-[#393028] rounded-xl px-4 py-2.5">{{ $schedule->team2->name }}</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-slate-400 text-xs font-bold mb-1.5 uppercase tracking-wider">Tanggal</label>
                            <input type="datetime-local" name="date" value="{{ \Carbon\Carbon::parse($schedule->date)->format('Y-m-d\TH:i') }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm [color-scheme:dark]">
                        </div>

                        <div>
                            <label class="block text-slate-400 text-xs font-bold mb-1.5 uppercase tracking-wider">Lokasi</label>
                            <input type="text" name="location" value="{{ $schedule->location }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm">
                        </div>

                        <div>
                            <label class="block text-slate-400 text-xs font-bold mb-1.5 uppercase tracking-wider">Status</label>
                            <select name="status" required class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm">
                                <option value="Scheduled" {{ $schedule->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="Postponed" {{ $schedule->status == 'Postponed' ? 'selected' : '' }}>Postponed</option>
                                <option value="Cancelled" {{ $schedule->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="Completed" {{ $schedule->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary text-[#181411] rounded-xl font-bold uppercase tracking-wider text-sm hover:bg-orange-400 transition-all">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    function openModal(scheduleId) {
        document.getElementById('editScheduleModal-' + scheduleId).style.display = "flex";
    }

    function closeModal(scheduleId) {
        document.getElementById('editScheduleModal-' + scheduleId).style.display = "none";
    }

    window.onclick = function(event) {
        document.querySelectorAll('[id^="editScheduleModal-"]').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }

    function filterSchedule() {
        let selectedStatus = document.getElementById("filterStatus").value.toLowerCase();
        let scheduleCards = document.querySelectorAll(".schedule-card");
        scheduleCards.forEach(card => {
            let status = card.getAttribute("data-status");
            if (selectedStatus === "all" || status === selectedStatus) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }
</script>
@endsection
