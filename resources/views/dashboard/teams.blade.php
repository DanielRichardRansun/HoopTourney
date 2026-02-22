@extends('layouts.app2')

@section('title', 'Teams - ' . $tournament->name)

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Participating Teams</h1>
        <p class="text-slate-400 text-sm mt-1">Teams competing in <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
    </div>

    @if($teams->isEmpty())
        <div class="py-16 flex flex-col items-center justify-center text-center glass-panel rounded-2xl border border-[#393028]">
            <div class="size-20 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-slate-600">groups</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No Teams Found</h3>
            <p class="text-slate-500 max-w-sm">There are currently no teams registered for this tournament.</p>
        </div>
    @else
        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($teams as $index => $team)
                <div onclick="window.location='{{ route('teams.show', ['tournament_id' => $tournament->id, 'id' => $team->id]) }}'" 
                     class="glass-panel border border-[#393028] rounded-2xl overflow-hidden hover:border-primary/50 transition-all duration-300 hover:scale-[1.02] hover:-translate-y-1 hover:shadow-[0_10px_30px_-10px_rgba(244,140,37,0.3)] cursor-pointer group flex flex-col h-full relative">
                    
                    <!-- Team Number Badge (Absolute) -->
                    <div class="absolute top-4 right-4 bg-[#181411] border border-[#393028] size-8 rounded-full flex items-center justify-center z-10">
                        <span class="text-xs font-black text-slate-400">#{{ $index + 1 }}</span>
                    </div>

                    <!-- Header/Logo Area -->
                    <div class="h-32 bg-[#1c1613] relative border-b border-[#393028] flex items-center justify-center overflow-hidden">
                        <!-- Abstract Background Pattern -->
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent"></div>
                        
                        <!-- Logo -->
                        <div class="size-20 rounded-full bg-[#181411] border-2 border-[#393028] flex items-center justify-center overflow-hidden relative z-10 group-hover:border-primary/50 transition-colors shadow-lg shadow-black/50">
                            @if(isset($team->logo) && $team->logo)
                                <img src="{{ asset('images/logos/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-slate-600 text-3xl">sports_basketball</span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-xl font-black text-white uppercase tracking-tight mb-4 text-center group-hover:text-primary transition-colors line-clamp-1" title="{{ $team->name }}">
                            {{ $team->name }}
                        </h3>

                        <div class="space-y-3 mt-auto">
                            <!-- Coach -->
                            <div class="flex items-center gap-3 py-2 border-t border-[#393028]/50 overflow-hidden">
                                <div class="size-8 rounded-lg bg-[#221914] flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-sm text-slate-400">sports</span>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Coach</span>
                                    <span class="text-sm font-semibold text-slate-300 truncate">{{ $team->coach ?: 'Not specified' }}</span>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="flex items-center gap-3 py-2 border-t border-[#393028]/50 overflow-hidden">
                                <div class="size-8 rounded-lg bg-[#221914] flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-sm text-slate-400">assignment_ind</span>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Manager</span>
                                    <span class="text-sm font-semibold text-slate-300 truncate">{{ $team->manager ?: 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
