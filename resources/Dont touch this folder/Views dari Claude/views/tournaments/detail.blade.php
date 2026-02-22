@extends('layouts.app2')

@section('title', 'Detail Lomba')

@section('content')

{{-- Flash Message --}}
@if (session('success'))
    <div class="glass-panel rounded-xl p-4 mb-6 border-l-4 border-emerald-500 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <p class="text-emerald-400 text-sm font-semibold">{{ session('success') }}</p>
    </div>
@endif

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-primary/10 border border-primary/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-primary text-2xl">sports_basketball</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Tournament Detail</h1>
        <p class="text-slate-400 text-sm">Manage and view your tournament information</p>
    </div>
</div>

{{-- Tournament Info Card --}}
<div class="glass-panel rounded-2xl p-6 md:p-8 border border-[#393028] shadow-xl relative overflow-hidden mb-8">
    <div class="absolute -top-20 -right-20 size-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
        {{-- Left: Name & Description --}}
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-white mb-3 leading-tight">{{ $tournament->name }}</h2>
            <p class="text-slate-400 text-sm leading-relaxed">{{ $tournament->description }}</p>
        </div>

        {{-- Right: Details --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-[18px]">person</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Organizer</p>
                    <p class="text-white font-semibold text-sm">{{ $tournament->organizer }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-emerald-500 text-[18px]">play_circle</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Start Date</p>
                    <p class="text-white font-semibold text-sm">{{ \Carbon\Carbon::parse($tournament->start_date)->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-red-500 text-[18px]">stop_circle</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">End Date</p>
                    <p class="text-white font-semibold text-sm">{{ \Carbon\Carbon::parse($tournament->end_date)->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-500 text-[18px]">flag</span>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Status</p>
                    @if($tournament->status === 'ongoing')
                        <span class="bg-red-500/20 text-red-400 border border-red-500/30 text-[10px] font-black px-2.5 py-1 rounded uppercase inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">radio_button_checked</span> Ongoing
                        </span>
                    @elseif($tournament->status === 'upcoming')
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-black px-2.5 py-1 rounded uppercase inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">event_upcoming</span> Upcoming
                        </span>
                    @elseif($tournament->status === 'scheduled')
                        <span class="bg-blue-500/20 text-blue-400 border border-blue-500/30 text-[10px] font-black px-2.5 py-1 rounded uppercase inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">calendar_month</span> Scheduled
                        </span>
                    @else
                        <span class="bg-slate-800 text-slate-400 border border-slate-700 text-[10px] font-black px-2.5 py-1 rounded uppercase inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">check_circle</span> Completed
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center justify-center gap-4 mt-8 pt-6 border-t border-[#393028] relative z-10">
        @if(auth()->id() === $tournament->users_id)
            <a href="{{ route('tournament.edit', $tournament->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-orange-400 text-[#181411] rounded-xl font-bold uppercase tracking-wider text-xs transition-all hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] hover:scale-105">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit Detail Tournament
            </a>

            @if($tournament->status === 'scheduled')
                <form action="{{ route('generate.schedule', $tournament->id) }}" method="POST" id="generateForm" class="inline">
                    @csrf
                    <button type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 rounded-xl font-bold uppercase tracking-wider text-xs transition-all hover:bg-emerald-500 hover:text-white hover:scale-105" onclick="openCustomModal()">
                        <span class="material-symbols-outlined text-[18px]">bolt</span> Generate Bracket & Jadwal
                    </button>
                </form>
            @elseif($tournament->status === 'upcoming')
                <button disabled class="inline-flex items-center gap-2 px-6 py-3 bg-[#221914]/50 border border-[#393028] text-slate-500 rounded-xl font-bold uppercase tracking-wider text-xs cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">lock</span> Generate Bracket & Jadwal
                </button>
                <p class="w-full text-center text-slate-500 text-xs mt-2">Untuk Generate Bracket dan Jadwal, pastikan data daftar tim sudah fix dan ubah status tournament anda ke "Scheduled".</p>
            @endif
        @endif
    </div>
</div>

{{-- Generate Confirmation Modal --}}
<div id="customModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="glass-panel rounded-2xl p-8 border border-[#393028] w-full max-w-md shadow-2xl mx-4">
        <div class="text-center mb-6">
            <div class="size-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-emerald-500 text-3xl">bolt</span>
            </div>
            <h3 class="text-xl font-black text-white uppercase tracking-tight">Generate Bracket & Jadwal</h3>
            <p class="text-slate-400 text-sm mt-2">Apakah Anda yakin ingin menggenerate bracket dan jadwal?</p>
            <p class="text-red-400 text-xs mt-3 bg-red-500/10 border border-red-500/20 rounded-lg p-3">Jika Anda sudah pernah melakukan generate sebelumnya, data sebelumnya akan dihapus dan dibuat ulang.</p>
        </div>

        <label class="flex items-center gap-3 glass-panel rounded-xl p-4 cursor-pointer mb-6 hover:bg-white/5 transition-colors">
            <input type="checkbox" id="randomizeTeams" class="w-4 h-4 accent-primary rounded">
            <span class="text-white text-sm font-semibold">Randomize Team Order</span>
        </label>

        <div class="flex gap-3">
            <button onclick="closeCustomModal()" class="flex-1 py-3 rounded-xl bg-[#221914] border border-[#393028] text-slate-400 font-bold uppercase tracking-wider text-xs hover:bg-[#2c221c] transition-all">Cancel</button>
            <button onclick="submitGenerate()" class="flex-1 py-3 rounded-xl bg-emerald-500 text-white font-bold uppercase tracking-wider text-xs hover:bg-emerald-400 transition-all">Generate</button>
        </div>
    </div>
</div>

<script>
    function openCustomModal() {
        const modal = document.getElementById('customModal');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('randomizeTeams').checked = false;
        }
    }

    function closeCustomModal() {
        const modal = document.getElementById('customModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function submitGenerate() {
        const form = document.getElementById('generateForm');
        const randomizeInput = document.createElement('input');
        randomizeInput.type = 'hidden';
        randomizeInput.name = 'randomize_teams';
        randomizeInput.value = document.getElementById('randomizeTeams').checked ? '1' : '0';
        form.appendChild(randomizeInput);
        form.submit();
    }
</script>
@endsection
