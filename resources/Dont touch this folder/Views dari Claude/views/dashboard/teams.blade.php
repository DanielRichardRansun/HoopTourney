@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-cyan-500 text-2xl">groups</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Daftar Tim</h1>
        <p class="text-slate-400 text-sm">{{ $tournament->name }}</p>
    </div>
</div>

@if($teams->isEmpty())
    <div class="glass-panel rounded-2xl p-16 text-center border border-[#393028] flex flex-col items-center gap-4">
        <div class="size-20 rounded-full bg-[#181411] flex items-center justify-center border border-[#393028]">
            <span class="material-symbols-outlined text-4xl text-slate-600">group_off</span>
        </div>
        <h4 class="text-lg font-bold text-white">No Teams Available</h4>
        <p class="text-slate-500 text-sm">No teams available in this tournament.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($teams as $index => $team)
            <div class="glass-panel rounded-2xl p-5 border border-[#393028] hover:border-primary/30 transition-all hover:-translate-y-1 cursor-pointer group shadow-lg relative overflow-hidden"
                 onclick="window.location='{{ route('teams.show', ['tournament_id' => $tournament->id, 'id' => $team->id]) }}'">
                <div class="absolute -top-10 -right-10 size-28 bg-cyan-500/5 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start gap-4 relative z-10">
                    <div class="size-14 rounded-xl bg-gradient-to-br from-primary/20 to-orange-600/10 border border-primary/30 flex items-center justify-center flex-shrink-0">
                        <span class="text-primary font-black text-lg">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-white font-bold text-base group-hover:text-primary transition-colors truncate">{{ $team->name }}</h4>
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-slate-500 text-[14px]">sports</span>
                                <span class="text-slate-500 uppercase font-bold tracking-wider w-16">Coach</span>
                                <span class="text-slate-300 font-semibold truncate">{{ $team->coach }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="material-symbols-outlined text-slate-500 text-[14px]">manage_accounts</span>
                                <span class="text-slate-500 uppercase font-bold tracking-wider w-16">Manager</span>
                                <span class="text-slate-300 font-semibold truncate">{{ $team->manager }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
