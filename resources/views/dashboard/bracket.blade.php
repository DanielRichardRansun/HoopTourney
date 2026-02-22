@extends('layouts.app2')

@section('title', 'Bracket - ' . $tournament->name)

@section('content')
<div class="space-y-6 max-w-[1400px] mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Tournament Bracket</h1>
        <p class="text-slate-400 text-sm mt-1">Live bracket for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
    </div>

    @if(session('message'))
        <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-500 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">warning</span>
                <span class="font-semibold text-sm">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-amber-400/50 hover:text-amber-400 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @elseif(empty($bracket))
        <div class="py-16 flex flex-col items-center justify-center text-center glass-panel rounded-2xl border border-[#393028]">
            <div class="size-20 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-slate-600">account_tree</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No Bracket Data</h3>
            <p class="text-slate-500 max-w-sm">There are no teams or generated bracket available for this tournament yet.</p>
        </div>
    @else
        @php
            $gameCounter = 1;
            $totalRounds = count($bracket);

            // Dynamic round title based on position from the end
            $getRoundTitle = function($roundIndex) use ($totalRounds) {
                $fromEnd = $totalRounds - 1 - $roundIndex; // 0 = last round, 1 = second to last, etc.
                
                switch ($fromEnd) {
                    case 0: return 'Final';
                    case 1: return 'Semi Final';
                    case 2: return 'Quarter Final';
                    default:
                        // For rounds earlier than QF, calculate "Round of X"
                        $matchesInStandardRound = pow(2, $fromEnd); // 8, 16, 32...
                        if ($matchesInStandardRound >= 8) {
                            return 'Round of ' . ($matchesInStandardRound * 2);
                        }
                        return 'Play-In';
                }
            };
        @endphp

        <!-- Bracket Container -->
        <div class="glass-panel rounded-2xl border border-[#393028] p-4 md:p-8 overflow-x-auto relative custom-scrollbar">
            
            <!-- Subtle background glow -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-3xl bg-primary/5 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="flex flex-nowrap gap-8 md:gap-16 min-w-max relative z-10 py-6">
                
                @php $roundNum = 0; @endphp
                @foreach ($bracket as $roundName => $round)
                    <div class="flex flex-col items-center min-w-[220px] md:min-w-[260px] relative">
                        <!-- Round Title -->
                        @php
                            $roundTitle = $getRoundTitle($roundNum++);
                        @endphp
                        <div class="text-center mb-8 px-6 py-2 rounded-xl bg-[#221914] border border-primary/30 w-full shadow-[0_0_15px_rgba(244,140,37,0.1)]">
                            <h3 class="font-black text-primary uppercase tracking-widest text-sm">
                                {{ $roundTitle }}
                            </h3>
                        </div>

                        <!-- Matchups wrapper to distribute vertically -->
                        <div class="flex flex-col justify-around flex-grow w-full gap-6">
                            @foreach ($round as $matchup)
                                @if ($matchup[0]['name'] !== '-' && $matchup[1]['name'] !== '-')
                                    <div class="glass-panel border border-[#393028] rounded-xl overflow-hidden hover:border-primary/50 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_25px_-5px_rgba(0,0,0,0.5)] cursor-pointer group"
                                         onclick="window.location='{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $matchup[2]]) }}'">
                                        
                                        <!-- Header -->
                                        <div class="bg-[#1c1613] px-3 py-1.5 border-b border-[#393028] flex items-center justify-between">
                                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest group-hover:text-primary transition-colors">Game {{ $gameCounter }}</span>
                                            <span class="material-symbols-outlined text-[14px] text-slate-600 opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                                        </div>

                                        <!-- Teams -->
                                        <div class="flex flex-col">
                                            <!-- Team 1 -->
                                            @php
                                                $t1Bg = $matchup[0]['is_winner'] ? 'bg-emerald-500/10' : ($matchup[0]['name'] === 'TBD' ? 'bg-[#181411]/50' : 'bg-[#181411]');
                                                $t1Text = $matchup[0]['is_winner'] ? 'text-emerald-400 font-bold' : ($matchup[0]['name'] === 'TBD' ? 'text-slate-600 italic' : 'text-slate-300');
                                                $t1Border = $matchup[0]['is_winner'] ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-transparent';
                                            @endphp
                                            <div class="px-4 py-3 {{ $t1Bg }} {{ $t1Text }} {{ $t1Border }} border-b border-[#393028]/50 flex justify-between items-center transition-colors">
                                                <div class="flex items-center gap-2 truncate pr-2 w-full">
                                                    @if(isset($matchup[0]['logo']) && $matchup[0]['logo'])
                                                        <img src="{{ asset('images/logos/' . $matchup[0]['logo']) }}" alt="{{ $matchup[0]['name'] }}" class="w-5 h-5 object-contain rounded-full bg-[#181411] shrink-0">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full bg-[#221914] flex items-center justify-center border border-[#393028] shrink-0">
                                                            <span class="material-symbols-outlined text-[10px] text-slate-500">sports_basketball</span>
                                                        </div>
                                                    @endif
                                                    <span class="text-sm truncate">{{ $matchup[0]['name'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    @if($matchup[0]['score'] !== null)
                                                        <span class="font-bold text-sm min-w-[20px] text-right font-mono">{{ $matchup[0]['score'] }}</span>
                                                    @elseif($matchup[0]['name'] !== 'TBD')
                                                        <span class="text-slate-600 font-bold text-sm min-w-[20px] text-right">-</span>
                                                    @endif
                                                    
                                                    @if($matchup[0]['is_winner'])
                                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                    @else
                                                        <span class="w-[16px] inline-block shrink-0"></span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Team 2 -->
                                            @php
                                                $t2Bg = $matchup[1]['is_winner'] ? 'bg-emerald-500/10' : ($matchup[1]['name'] === 'TBD' ? 'bg-[#181411]/50' : 'bg-[#181411]');
                                                $t2Text = $matchup[1]['is_winner'] ? 'text-emerald-400 font-bold' : ($matchup[1]['name'] === 'TBD' ? 'text-slate-600 italic' : 'text-slate-300');
                                                $t2Border = $matchup[1]['is_winner'] ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-transparent';
                                            @endphp
                                            <div class="px-4 py-3 {{ $t2Bg }} {{ $t2Text }} {{ $t2Border }} flex justify-between items-center transition-colors">
                                                <div class="flex items-center gap-2 truncate pr-2 w-full">
                                                    @if(isset($matchup[1]['logo']) && $matchup[1]['logo'])
                                                        <img src="{{ asset('images/logos/' . $matchup[1]['logo']) }}" alt="{{ $matchup[1]['name'] }}" class="w-5 h-5 object-contain rounded-full bg-[#181411] shrink-0">
                                                    @else
                                                        <div class="w-5 h-5 rounded-full bg-[#221914] flex items-center justify-center border border-[#393028] shrink-0">
                                                            <span class="material-symbols-outlined text-[10px] text-slate-500">sports_basketball</span>
                                                        </div>
                                                    @endif
                                                    <span class="text-sm truncate">{{ $matchup[1]['name'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    @if($matchup[1]['score'] !== null)
                                                        <span class="font-bold text-sm min-w-[20px] text-right font-mono">{{ $matchup[1]['score'] }}</span>
                                                    @elseif($matchup[1]['name'] !== 'TBD')
                                                        <span class="text-slate-600 font-bold text-sm min-w-[20px] text-right">-</span>
                                                    @endif
                                                    
                                                    @if($matchup[1]['is_winner'])
                                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                    @else
                                                        <span class="w-[16px] inline-block shrink-0"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $gameCounter++; @endphp
                                @endif                        
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Champion Column -->
                <div class="flex flex-col items-center min-w-[220px] md:min-w-[260px] relative justify-center">
                    
                    <div class="text-center mb-8 px-6 py-2 rounded-xl bg-amber-500/10 border border-amber-500/30 w-full shadow-[0_0_20px_rgba(245,158,11,0.2)]">
                        <h3 class="font-black text-amber-500 uppercase tracking-widest text-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">emoji_events</span>
                            Champion
                        </h3>
                    </div>

                    <div class="flex flex-col justify-center flex-grow w-full">
                        <div class="glass-panel border-2 border-amber-500/50 rounded-xl overflow-hidden shadow-[0_0_30px_rgba(245,158,11,0.15)] relative group cursor-default">
                            
                            <!-- Dynamic win animation bg -->
                            @if($champion !== 'TBD')
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-amber-500/20 to-transparent"></div>
                            @endif

                            <div class="bg-amber-500/20 px-3 py-2 border-b border-amber-500/30 text-center relative z-10">
                                <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Tournament Winner</span>
                            </div>

                            <div class="px-4 py-8 text-center bg-[#1c1613] relative z-10 flex flex-col items-center justify-center min-h-[160px]">
                                @if(is_object($champion))
                                    <div class="size-20 rounded-full bg-[#181411] border-2 border-amber-500 shadow-[0_0_20px_rgba(244,140,37,0.3)] flex items-center justify-center mb-4 overflow-hidden relative">
                                        @if($champion->logo)
                                            <img src="{{ asset('images/logos/' . $champion->logo) }}" alt="{{ $champion->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-4xl text-amber-500">security</span>
                                        @endif
                                    </div>
                                    <span class="text-xl font-black text-white uppercase tracking-tight break-words w-full px-2">{{ $champion->name }}</span>
                                @else
                                    <span class="material-symbols-outlined text-4xl text-slate-600 mb-2">hourglass_empty</span>
                                    <span class="text-lg font-bold text-slate-500 italic uppercase">TBD</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Custom Scrollbar for bracket to look seamless */
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(24, 20, 17, 0.8);
        border-radius: 4px;
        border: 1px solid #393028;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #f48c25;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #e67e22;
    }
</style>
@endpush
@endsection
