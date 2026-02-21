@extends('layouts.general')

@section('content')
<!-- Page Background and Base Font -->
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-black text-slate-100 italic uppercase tracking-tight mb-6">
                All <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Players</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium">
                Meet the athletes commanding the court. Search for your favorite players across all teams in the Hoop Tourney.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-16">
        
        <!-- Search and Filters -->
        <div class="flex flex-wrap items-center justify-between gap-6 mb-12">
            <!-- Position Filters (Optional feature for future, currently visual only like the team view) -->
            <div class="flex flex-wrap gap-3">
                <span class="px-6 py-2.5 rounded-full text-sm font-bold bg-[#f48c25]/10 text-[#f48c25] border border-[#f48c25]/30">
                    All Positions
                </span>
            </div>

            <!-- Search Form -->
            <form action="{{ route('players.global') }}" method="GET" class="relative w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search player or position..." 
                       class="w-full md:w-80 bg-[#221914] border border-[#393028] text-slate-200 text-sm rounded-full pl-12 pr-4 py-3 focus:outline-none focus:border-[#f48c25] focus:ring-1 focus:ring-[#f48c25] transition-all placeholder:text-slate-500">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">search</span>
            </form>
        </div>

        <!-- Players Grid (Smaller Cards) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
            @forelse ($players as $player)
                <!-- Player Card -->
                <div class="group relative rounded-xl bg-[#221914]/80 backdrop-blur-md border border-[#393028] p-4 transition-all hover:-translate-y-1.5 hover:shadow-[0_8px_25px_-8px_rgba(244,140,37,0.3)] hover:border-[#f48c25]/40 flex flex-col items-center text-center overflow-hidden">
                    
                    <!-- Jersey Number Watermark -->
                    <div class="absolute -right-4 -top-8 text-[80px] font-black italic text-[#393028]/30 group-hover:text-[#f48c25]/10 transition-colors pointer-events-none select-none">
                        {{ str_pad($player->jersey_number, 2, '0', STR_PAD_LEFT) }}
                    </div>

                    <!-- Player Photo Placeholder -->
                    <div class="relative z-10 size-20 md:size-24 rounded-full bg-[#181411] border-2 border-[#393028] mb-4 flex items-center justify-center overflow-hidden group-hover:border-[#f48c25] transition-colors shadow-inner shrink-0">
                        @if($player->photo)
                            <img src="{{ asset('images/profiles/' . $player->photo) }}" alt="{{ $player->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-[40px] text-slate-600 group-hover:text-[#f48c25] transition-colors">person</span>
                        @endif
                    </div>

                    <!-- Player Info -->
                    <h3 class="relative z-10 text-base md:text-lg font-bold text-slate-100 mb-1 w-full truncate group-hover:text-[#f48c25] transition-colors">
                        {{ $player->name }}
                    </h3>
                    
                    <div class="relative z-10 flex items-center justify-center gap-2 w-full mt-1 mb-4">
                        <span class="px-2 py-0.5 rounded text-[10px] md:text-xs font-black bg-[#f48c25] text-[#181411] tracking-wider uppercase">
                            {{ $player->position }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400 border border-[#393028] px-2 py-0.5 rounded bg-[#181411]">
                            #{{ $player->jersey_number }}
                        </span>
                    </div>

                    <!-- Team Banner -->
                    <div class="relative z-10 w-full bg-[#181411] border-t border-[#393028] -mx-4 -mb-4 mt-auto p-3 flex flex-col items-center justify-center transition-colors group-hover:bg-[#f48c25]/5 group-hover:border-t-[#f48c25]/20">
                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-0.5">Team</span>
                        <span class="text-xs md:text-sm text-slate-300 font-semibold truncate w-full text-center group-hover:text-amber-400">
                            {{ $player->team ? $player->team->name : 'Free Agent' }}
                        </span>
                    </div>

                </div>
            @empty
                <div class="col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-5 text-center py-20 rounded-2xl bg-[#221914]/80 backdrop-blur-md border border-[#393028]">
                    <div class="size-16 bg-[#181411] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#393028]">
                        <span class="material-symbols-outlined text-3xl text-slate-500">person_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-100 mb-2">No Players Found</h3>
                    <p class="text-slate-500">There are currently no players matching your search criteria.</p>
                </div>
            @endforelse
        </div>
    </section>
</main>
@endsection
