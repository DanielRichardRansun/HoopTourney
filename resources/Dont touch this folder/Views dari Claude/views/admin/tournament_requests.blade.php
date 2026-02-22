@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20">

    <!-- Header Section -->
    <section class="relative py-20 bg-gradient-to-b from-[#221914] to-[#181411] border-b border-[#393028]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay"></div>
        <div class="container mx-auto px-6 lg:px-10 relative z-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-100 italic uppercase tracking-tight mb-2">
                        Tournament <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Requests</span>
                    </h1>
                    <p class="text-lg text-slate-400 font-medium">Manage joining requests from teams for your tournaments.</p>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <!-- Tournament Filter -->
                    <div class="relative group">
                        <button class="w-full sm:w-auto px-5 py-2.5 bg-[#221914] border border-[#393028] rounded-xl text-slate-300 font-medium flex items-center justify-between gap-3 hover:border-[#f48c25] hover:text-white transition-colors">
                            <span class="truncate max-w-[150px]">
                                @if(request('tournament', 'all') == 'all')
                                    All Tournaments
                                @else
                                    {{ $tournaments->firstWhere('id', request('tournament'))->name ?? 'Select Tournament' }}
                                @endif
                            </span>
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </button>
                        <ul class="absolute z-50 right-0 mt-2 w-56 text-sm bg-[#221914] border border-[#393028] rounded-xl shadow-2xl overflow-hidden invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all origin-top">
                            <li>
                                <a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-[#f48c25] transition-colors" href="?tournament=all&status={{ request('status', 'all') }}">All My Tournaments</a>
                            </li>
                            @foreach($tournaments as $tournament)
                            <li>
                                <a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-[#f48c25] transition-colors border-t border-[#393028]" href="?tournament={{ $tournament->id }}&status={{ request('status', 'all') }}">{{ $tournament->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative group">
                        <button class="w-full sm:w-auto px-5 py-2.5 bg-[#221914] border border-[#393028] rounded-xl text-slate-300 font-medium flex items-center justify-between gap-3 hover:border-[#f48c25] hover:text-white transition-colors">
                            <span class="truncate">
                                @if(request('status', 'all') == 'all')
                                    All Statuses
                                @else
                                    {{ ucfirst(request('status', 'all')) }}
                                @endif
                            </span>
                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                        </button>
                        <ul class="absolute z-50 right-0 mt-2 w-48 text-sm bg-[#221914] border border-[#393028] rounded-xl shadow-2xl overflow-hidden invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all origin-top">
                            <li><a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-[#f48c25] transition-colors" href="?status=all&tournament={{ request('tournament', 'all') }}">All Requests</a></li>
                            <li><a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-yellow-500 transition-colors border-t border-[#393028]" href="?status=pending&tournament={{ request('tournament', 'all') }}">Pending</a></li>
                            <li><a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-emerald-500 transition-colors border-t border-[#393028]" href="?status=approved&tournament={{ request('tournament', 'all') }}">Approved</a></li>
                            <li><a class="block px-4 py-3 text-slate-300 hover:bg-[#2c221c] hover:text-red-500 transition-colors border-t border-[#393028]" href="?status=rejected&tournament={{ request('tournament', 'all') }}">Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="container mx-auto px-6 lg:px-10 py-12">
        <div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden">
            @if($tournaments->isEmpty())
                <div class="p-8 text-center flex flex-col items-center justify-center">
                    <div class="size-16 rounded-full bg-[#221914] flex items-center justify-center mb-4 border border-[#393028]">
                        <span class="material-symbols-outlined text-slate-500 text-3xl">sports_basketball</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No Tournaments Yet</h3>
                    <p class="text-slate-400 text-sm max-w-sm mx-auto">You haven't created any tournaments yet. Create a tournament to start receiving join requests.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#221914] border-b border-[#393028]">
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Team</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Tournament</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Request Date</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#393028]">
                            @forelse($requests as $request)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-white text-sm flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-[#221914] border border-[#393028] flex items-center justify-center overflow-hidden">
                                            @if($request->team->logo)
                                                <img src="{{ asset('images/logos/' . $request->team->logo) }}" alt="Logo" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-600 text-[16px]">groups</span>
                                            @endif
                                        </div>
                                        {{ $request->team->name }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-300 text-sm font-medium">{{ $request->tournament->name }}</div>
                                </td>
                                <td class="p-4">
                                    @php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch($request->status) {
                                            case 'pending': 
                                                $statusClass = 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/30'; 
                                                $statusIcon = 'hourglass_empty';
                                                break;
                                            case 'approved': 
                                                $statusClass = 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30'; 
                                                $statusIcon = 'verified';
                                                break;
                                            case 'rejected': 
                                                $statusClass = 'bg-red-500/10 text-red-500 border border-red-500/30'; 
                                                $statusIcon = 'block';
                                                break;
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusClass }}">
                                        @if($statusIcon)<span class="material-symbols-outlined text-[12px]">{{ $statusIcon }}</span>@endif
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-400 text-xs font-medium">{{ $request->created_at->format('M d, Y H:i') }}</div>
                                </td>
                                <td class="p-4 text-right">
                                    @if($request->status == 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.tournament.approve', $request->id) }}" method="POST" class="inline-block m-0">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white border border-emerald-500/30 hover:border-emerald-500 rounded text-[11px] font-bold uppercase tracking-wider transition-colors flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.tournament.reject', $request->id) }}" method="POST" class="inline-block m-0">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/30 hover:border-red-500 rounded text-[11px] font-bold uppercase tracking-wider transition-colors flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">close</span>
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider bg-[#221914] px-3 py-1 rounded border border-[#393028]">Action Completed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 text-sm">
                                    <span class="material-symbols-outlined block text-3xl mb-2 opacity-50">inbox</span>
                                    No requests found for your tournaments matching this criteria.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection