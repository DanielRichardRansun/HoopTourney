@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-100 italic uppercase tracking-tight mb-2">
                        My <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Tournaments</span>
                    </h1>
                    <p class="text-lg text-slate-400 font-medium">Manage and view the tournaments you have created.</p>
                </div>
                
                @auth
                    @if ($user->role == 1)
                        <a href="{{ route('tournament.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#f48c25] text-[#181411] rounded-full font-bold uppercase tracking-wider transition-all hover:-translate-y-1 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)]">
                            <span class="material-symbols-outlined">add_circle</span>
                            Buat Tourney
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-12">
        @if (session('success'))
            <div class="mb-8 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 flex items-center gap-3 text-emerald-400 font-medium">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @forelse($tournaments as $tournament)
                <div class="flex flex-col glass-panel rounded-2xl p-5 hover:bg-[#2c221c] transition-all hover:-translate-y-1 cursor-pointer group border-t-4 {{ $tournament->status == 'ongoing' ? 'border-t-red-500' : ($tournament->status == 'upcoming' ? 'border-t-emerald-500' : ($tournament->status == 'scheduled' ? 'border-t-blue-500' : 'border-t-slate-500')) }} shadow-xl relative overflow-hidden"
                     onclick="window.location='{{ route('tournament.detail', $tournament->id) }}'">
                    
                    <!-- Background aesthetic for role -->
                    @if ($user->role == 1)
                        <div class="absolute -top-10 -right-10 size-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                    @else
                        <div class="absolute -top-10 -right-10 size-32 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    @endif
                    
                    <div class="flex items-start gap-4 mb-4 relative z-10">
                        <!-- Tournament Logo -->
                        <div class="size-16 rounded-xl bg-[#181411] overflow-hidden border border-[#393028] flex-shrink-0 group-hover:border-primary/50 transition-colors shadow-inner">
                            @if(isset($tournament->logo) && $tournament->logo)
                                <img src="{{ asset('images/logos/' . $tournament->logo) }}" alt="{{ $tournament->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-primary bg-primary/10">
                                    <span class="material-symbols-outlined text-[32px]">sports_basketball</span>
                                </div>
                            @endif
                        </div>

                        <!-- Title and Status -->
                        <div class="flex flex-col flex-grow overflow-hidden">
                            <h4 class="text-white font-bold text-lg leading-tight mb-1 group-hover:text-primary transition-colors line-clamp-2" title="{{ $tournament->name }}">{{ $tournament->name }}</h4>
                            <div class="flex items-center flex-wrap gap-2 mt-1">
                                @php
                                    $statusClass = 'bg-slate-700/50 text-slate-300';
                                    $statusIcon = 'radio_button_unchecked';
                                    if($tournament->status == 'ongoing') { $statusClass = 'bg-red-500/20 text-red-500 border border-red-500/30'; $statusIcon = 'radio_button_checked'; }
                                    elseif($tournament->status == 'upcoming') { $statusClass = 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30'; $statusIcon = 'event_upcoming'; }
                                    elseif($tournament->status == 'scheduled') { $statusClass = 'bg-blue-500/20 text-blue-500 border border-blue-500/30'; $statusIcon = 'calendar_month'; }
                                    elseif($tournament->status == 'completed') { $statusClass = 'bg-slate-800 text-slate-400 border border-slate-700'; $statusIcon = 'check_circle'; }
                                @endphp
                                <span class="{{ $statusClass }} text-[10px] font-black px-2.5 py-1 rounded uppercase flex items-center gap-1 w-fit shadow-sm">
                                    <span class="material-symbols-outlined text-[12px]">{{ $statusIcon }}</span>
                                    {{ ucfirst($tournament->status) }}
                                </span>
                                
                                @if ($user->role == 1)
                                    <span class="bg-primary/10 border border-primary/30 text-primary text-[10px] font-black px-2.5 py-1 rounded uppercase flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-[12px]">admin_panel_settings</span>
                                        Organizer
                                    </span>
                                @else
                                    <span class="bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-[10px] font-black px-2.5 py-1 rounded uppercase flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-[12px]">sports_kabaddi</span>
                                        Participant
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dates and Details -->
                    <div class="flex flex-col gap-2 pt-4 border-t border-[#393028] flex-grow relative z-10">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 uppercase font-bold tracking-wider">Start Date</span>
                            <span class="text-slate-200 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-slate-400">play_circle</span> {{ \Carbon\Carbon::parse($tournament->start_date)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 uppercase font-bold tracking-wider">End Date</span>
                            <span class="text-slate-200 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[14px] text-slate-400">stop_circle</span> {{ \Carbon\Carbon::parse($tournament->end_date)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 uppercase font-bold tracking-wider">Organizer</span>
                            <span class="text-slate-200 font-semibold truncate max-w-[150px] text-right" title="{{ $tournament->organizer }}">{{ $tournament->organizer }}</span>
                        </div>
                    </div>

                    <!-- Actions (For Organizer) -->
                    @if ($user->role == 1)
                        <div class="mt-5 pt-4 border-t border-[#393028] flex items-center justify-end gap-3 relative z-10" onclick="event.stopPropagation()">
                            <a href="{{ route('tournament.edit', $tournament->id) }}" class="flex-1 text-center py-2.5 rounded-xl bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 border border-yellow-500/30 text-yellow-500 hover:bg-yellow-500 hover:text-[#181411] hover:border-yellow-500 font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                            </a>
                            @if ($tournament->status == "upcoming")
                                <form action="{{ route('tournament.destroy', $tournament->id) }}" method="POST" class="flex-1 m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus turnamen ini?')" class="w-full text-center py-2.5 rounded-xl bg-gradient-to-br from-red-500/20 to-red-600/10 border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                        <span class="material-symbols-outlined text-[16px]">delete</span> Delete
                                    </button>
                                </form>
                            @else
                                <div class="flex-1 text-center py-2.5 rounded-xl bg-[#221914]/50 border border-[#393028] text-slate-500 font-bold text-xs uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-1.5" title="Cannot delete after tournament has started">
                                    <span class="material-symbols-outlined text-[16px]">lock</span> Locked
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full glass-panel rounded-2xl p-16 text-center text-slate-400 border border-[#393028] flex flex-col items-center justify-center gap-5 shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#221914] to-transparent pointer-events-none"></div>
                    <div class="size-24 rounded-full bg-[#181411] flex items-center justify-center border border-[#393028] shadow-inner relative z-10">
                        <span class="material-symbols-outlined text-5xl text-slate-600">sports_basketball</span>
                    </div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-bold text-white mb-2">No Tournaments Yet</h4>
                        <p class="text-sm font-medium text-slate-500 max-w-sm mx-auto">It looks like you haven't {{ $user->role == 1 ? 'created' : 'joined' }} any tournaments. The court is waiting!</p>
                    </div>
                    @if ($user->role == 1)
                        <a href="{{ route('tournament.create') }}" class="mt-4 inline-flex items-center gap-2 px-8 py-3.5 bg-[#f48c25] text-[#181411] rounded-full font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:scale-105 shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] relative z-10">
                            <span class="material-symbols-outlined">add_circle</span>
                            Host Your First Tourney
                        </a>
                    @else
                        <a href="{{ route('tournaments.global') }}" class="mt-4 inline-flex items-center gap-2 px-8 py-3.5 bg-indigo-500 text-white rounded-full font-black uppercase tracking-wider transition-all hover:bg-indigo-400 hover:scale-105 shadow-[0_10px_20px_-10px_rgba(99,102,241,0.5)] relative z-10">
                            <span class="material-symbols-outlined">search</span>
                            Find Tournaments
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </section>
</main>
@endsection