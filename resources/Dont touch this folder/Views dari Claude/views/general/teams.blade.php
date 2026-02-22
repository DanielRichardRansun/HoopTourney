@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-black text-slate-100 italic uppercase tracking-tight mb-6">
                All <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Teams</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium">
                Discover all the teams competing in the Hoop Tourney league. View coaches, managers, and roster sizes.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-16">
        
        <!-- Search -->
        <div class="flex flex-wrap items-center justify-end gap-6 mb-12">
            <form id="searchForm" action="{{ route('teams.global') }}" method="GET" class="relative w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search teams or coaches..." 
                       class="w-full md:w-80 bg-[#221914] border border-[#393028] text-slate-200 text-sm rounded-full pl-12 pr-4 py-3 focus:outline-none focus:border-[#f48c25] focus:ring-1 focus:ring-[#f48c25] transition-all placeholder:text-slate-500"
                       oninput="clearTimeout(this.delay); this.delay = setTimeout(() => document.getElementById('searchForm').submit(), 500);">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">search</span>
            </form>
        </div>

        <!-- Teams Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($teams as $team)
                <!-- Team Card -->
                <div class="group relative rounded-2xl bg-[#221914]/80 backdrop-blur-md border border-[#393028] p-6 transition-all hover:-translate-y-2 hover:shadow-[0_10px_30px_-10px_rgba(244,140,37,0.2)] hover:border-[#f48c25]/30 flex flex-col items-center text-center overflow-hidden">
                    
                    <!-- Decorative glowing orb behind logo -->
                    <div class="absolute -top-10 -right-10 size-32 bg-[#f48c25]/10 rounded-full blur-3xl group-hover:bg-[#f48c25]/20 transition-all"></div>
                    
                    <!-- Team Logo Placeholder -->
                    <div class="relative z-10 size-24 bg-gradient-to-tr from-[#181411] to-[#2c221c] rounded-full flex items-center justify-center border-2 border-[#393028] mb-6 shadow-inner group-hover:border-[#f48c25] transition-colors overflow-hidden shrink-0">
                        @if($team->logo)
                            <img src="{{ asset('images/logos/' . $team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-[40px] text-slate-600 group-hover:text-[#f48c25] transition-colors">groups</span>
                        @endif
                    </div>
                    
                    <h3 class="relative z-10 text-xl font-black text-slate-100 mb-1 truncate w-full group-hover:text-[#f48c25] transition-colors uppercase italic">{{ $team->name }}</h3>
                    <p class="relative z-10 text-sm text-slate-500 mb-6 w-full">Est {{ \Carbon\Carbon::parse($team->created_at)->format('Y') }}</p>
                    
                    <div class="relative z-10 w-full space-y-3 p-4 rounded-xl bg-[#181411]/50 border border-[#393028]/50">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Coach</span>
                            <span class="text-slate-200 font-medium truncate max-w-[120px]">{{ $team->coach }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Manager</span>
                            <span class="text-slate-200 font-medium truncate max-w-[120px]">{{ $team->manager }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm pt-3 mt-3 border-t border-[#393028]/50">
                            <span class="text-slate-500">Roster</span>
                            <span class="text-[#f48c25] font-black text-base">{{ $team->players_count }} <span class="text-xs text-slate-500 font-normal">Players</span></span>
                        </div>
                    </div>

                    <!-- Optional: Link wrapper if you want the card to be clickable in the future -->
                    <!-- <a href="#" class="absolute inset-0 z-20"></a> -->
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 text-center py-20 rounded-2xl bg-[#221914]/80 backdrop-blur-md border border-[#393028]">
                    <div class="size-16 bg-[#181411] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#393028]">
                        <span class="material-symbols-outlined text-3xl text-slate-500">group_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-100 mb-2">No Teams Found</h3>
                    <p class="text-slate-500">There are currently no teams matching your search.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12 w-full custom-pagination">
            {{ $teams->appends(request()->query())->links() }}
        </div>
    </section>
</main>
@endsection
