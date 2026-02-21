@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-black text-slate-100 italic uppercase tracking-tight mb-6">
                All <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Tournaments</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-medium">
                Browse our complete list of basketball tournaments. Filter by status to find ongoing, upcoming, or completed events.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-16">
        
        <!-- Filters -->
        <div class="flex flex-wrap items-center justify-between gap-6 mb-12">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('tournaments.global', ['status' => 'all']) }}" 
                   class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ request('status', 'all') == 'all' ? 'bg-[#f48c25] text-[#181411] shadow-[0_0_15px_rgba(244,140,37,0.4)]' : 'bg-[#221914] text-slate-300 border border-[#393028] hover:bg-[#2c221c]' }}">
                    All Leagues
                </a>
                <a href="{{ route('tournaments.global', ['status' => 'ongoing']) }}" 
                   class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ request('status') == 'ongoing' ? 'bg-[#f48c25] text-[#181411] shadow-[0_0_15px_rgba(244,140,37,0.4)]' : 'bg-[#221914] text-slate-300 border border-[#393028] hover:bg-[#2c221c]' }}">
                    Ongoing
                </a>
                <a href="{{ route('tournaments.global', ['status' => 'upcoming']) }}" 
                   class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ request('status') == 'upcoming' ? 'bg-[#f48c25] text-[#181411] shadow-[0_0_15px_rgba(244,140,37,0.4)]' : 'bg-[#221914] text-slate-300 border border-[#393028] hover:bg-[#2c221c]' }}">
                    Upcoming
                </a>
                <a href="{{ route('tournaments.global', ['status' => 'completed']) }}" 
                   class="px-6 py-2.5 rounded-full text-sm font-bold transition-all {{ request('status') == 'completed' ? 'bg-[#f48c25] text-[#181411] shadow-[0_0_15px_rgba(244,140,37,0.4)]' : 'bg-[#221914] text-slate-300 border border-[#393028] hover:bg-[#2c221c]' }}">
                    Completed
                </a>
            </div>

            <!-- Search -->
            <form action="{{ route('tournaments.global') }}" method="GET" class="relative w-full md:w-auto">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tournaments..." 
                       class="w-full md:w-72 bg-[#221914] border border-[#393028] text-slate-200 text-sm rounded-full pl-12 pr-4 py-3 focus:outline-none focus:border-[#f48c25] focus:ring-1 focus:ring-[#f48c25] transition-all placeholder:text-slate-500">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">search</span>
            </form>
        </div>

        <!-- Tournaments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($tournaments as $tournament)
                <!-- Tournament Card -->
                <a href="{{ route('tournament.detail', $tournament->id) }}" class="group block rounded-2xl bg-[#221914]/80 backdrop-blur-md border border-[#393028] p-6 transition-all hover:-translate-y-2 hover:shadow-[0_10px_30px_-10px_rgba(244,140,37,0.2)] hover:border-[#f48c25]/30">
                    <div class="flex items-start justify-between mb-6">
                        <div class="bg-[#181411] text-[#f48c25] size-14 rounded-xl flex items-center justify-center border border-[#f48c25]/20 group-hover:border-[#f48c25] transition-colors overflow-hidden shrink-0">
                            @if($tournament->logo)
                                <img src="{{ asset('images/logos/' . $tournament->logo) }}" alt="{{ $tournament->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined !text-[32px]">sports_basketball</span>
                            @endif
                        </div>
                        @if ($tournament->status == 'ongoing')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-xs font-bold border border-red-500/20">
                                <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                                LIVE
                            </span>
                        @elseif ($tournament->status == 'upcoming')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#f48c25]/10 text-[#f48c25] text-xs font-bold border border-[#f48c25]/20">
                                UPCOMING
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 text-xs font-bold border border-slate-500/20">
                                COMPLETED
                            </span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-100 mb-2 truncate group-hover:text-[#f48c25] transition-colors">{{ $tournament->name }}</h3>
                    <p class="text-sm border-l-2 border-[#393028] pl-3 text-slate-400 mb-6 group-hover:border-[#f48c25]/50 transition-colors">
                        Org: <span class="text-slate-300 font-medium">{{ $tournament->organizer }}</span>
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-[#393028] pt-6">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Start Date</p>
                            <p class="text-sm font-semibold text-slate-200">
                                {{ \Carbon\Carbon::parse($tournament->start_date)->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">End Date</p>
                            <p class="text-sm font-semibold text-slate-200">
                                {{ \Carbon\Carbon::parse($tournament->end_date)->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-20 rounded-2xl bg-[#221914]/80 backdrop-blur-md border border-[#393028]">
                    <div class="size-16 bg-[#181411] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#393028]">
                        <span class="material-symbols-outlined text-3xl text-slate-500">sports_basketball</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-100 mb-2">No Tournaments Found</h3>
                    <p class="text-slate-500">There are currently no tournaments matching your criteria.</p>
                </div>
            @endforelse
        </div>
    </section>
</main>
@endsection
