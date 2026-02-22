@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-purple-500 text-2xl">account_tree</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Tournament Bracket</h1>
        <p class="text-slate-400 text-sm">{{ $tournament->name }}</p>
    </div>
</div>

@if(session('message'))
    <div class="glass-panel rounded-xl p-4 mb-6 border-l-4 border-yellow-500 flex items-center gap-3">
        <span class="material-symbols-outlined text-yellow-500">warning</span>
        <p class="text-yellow-400 text-sm font-semibold">{{ session('message') }}</p>
    </div>
@elseif(empty($bracket))
    <div class="glass-panel rounded-2xl p-16 text-center border border-[#393028] flex flex-col items-center gap-4">
        <div class="size-20 rounded-full bg-[#181411] flex items-center justify-center border border-[#393028]">
            <span class="material-symbols-outlined text-4xl text-slate-600">account_tree</span>
        </div>
        <h4 class="text-lg font-bold text-white">No Teams Available</h4>
        <p class="text-slate-500 text-sm">No teams available in this tournament.</p>
    </div>
@else
    @php
        $roundCount = count($bracket) + 1;
        $roundLabels = [];
        $stages = ['Final', 'Semi Final', 'Quarter Final', 'Penyisihan'];
        for ($i = 0; $i < $roundCount; $i++) {
            $roundLabels[$roundCount - $i - 1] = $stages[$i] ?? 'Penyisihan';
        }
        $gameCounter = 1;
    @endphp

    <div class="glass-panel rounded-2xl border border-[#393028] p-6 overflow-hidden">
        <div class="overflow-x-auto pb-4">
            <div class="flex flex-nowrap gap-6 min-w-max">
                @foreach ($bracket as $index => $round)
                    <div class="flex flex-col items-center min-w-[200px]">
                        <div class="bg-gradient-to-r from-primary/30 to-orange-600/20 border border-primary/30 text-primary font-black text-xs uppercase tracking-widest px-4 py-2 rounded-lg mb-4 w-full text-center">
                            {{ is_numeric($index) ? ($roundLabels[$index] ?? 'Round ' . ($index + 1)) : $index }}
                        </div>
                        <div class="flex flex-col justify-center items-center flex-grow gap-4">
                            @foreach ($round as $matchup)
                                @if ($matchup[0]['name'] !== '-' && $matchup[1]['name'] !== '-')
                                    <div class="glass-panel rounded-xl p-3 w-[200px] cursor-pointer hover:border-primary/50 hover:shadow-[0_0_15px_rgba(244,140,37,0.15)] transition-all border border-[#393028]"
                                         onclick="window.location='{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $matchup[2]]) }}'">
                                        <div class="text-center mb-2">
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Game {{ $gameCounter }}</span>
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-center text-sm font-bold border mb-1.5 {{ $matchup[0]['is_winner'] ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-400' : ($matchup[0]['name'] === 'TBD' ? 'bg-[#221914] border-[#393028] text-slate-500 italic' : 'bg-[#221914] border-[#393028] text-white') }}">
                                            {{ $matchup[0]['name'] }}
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-center text-sm font-bold border {{ $matchup[1]['is_winner'] ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-400' : ($matchup[1]['name'] === 'TBD' ? 'bg-[#221914] border-[#393028] text-slate-500 italic' : 'bg-[#221914] border-[#393028] text-white') }}">
                                            {{ $matchup[1]['name'] }}
                                        </div>
                                    </div>
                                    @php $gameCounter++; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Champion Column --}}
                <div class="flex flex-col items-center min-w-[200px]">
                    <div class="bg-gradient-to-r from-yellow-500/30 to-amber-600/20 border border-yellow-500/30 text-yellow-400 font-black text-xs uppercase tracking-widest px-4 py-2 rounded-lg mb-4 w-full text-center">
                        🏆 Champion
                    </div>
                    <div class="flex flex-col justify-center items-center flex-grow">
                        <div class="bg-gradient-to-br from-yellow-500/20 to-amber-600/10 border-2 border-yellow-500/40 rounded-xl p-5 w-[200px] text-center shadow-[0_0_25px_rgba(234,179,8,0.15)]">
                            <span class="text-[10px] text-yellow-500/70 font-bold uppercase tracking-wider block mb-2">Winner Final</span>
                            <p class="text-yellow-400 font-black text-lg">{{ $champion }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
