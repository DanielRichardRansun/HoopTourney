@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-indigo-500 text-2xl">bar_chart</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Statistik Pemain</h1>
        <p class="text-slate-400 text-sm">{{ $tournament->name }}</p>
    </div>
</div>

{{-- Team Filter --}}
<div class="glass-panel rounded-xl p-4 mb-6 border border-[#393028] flex flex-col sm:flex-row items-center gap-4">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-slate-400 text-[18px]">filter_list</span>
        <label class="text-slate-300 text-sm font-bold uppercase tracking-wider">Filter Tim:</label>
    </div>
    <form method="GET" class="flex-1 max-w-xs">
        <select name="team_id" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-2.5 outline-none focus:border-primary transition-colors text-sm font-semibold" onchange="this.form.submit()">
            <option value="">-- Pilih Tim --</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Stats Table --}}
<div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table id="statsTable" class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-[#393028] bg-gradient-to-r from-indigo-500/10 to-transparent">
                    <th class="p-4 text-xs font-bold text-indigo-400 uppercase tracking-wider whitespace-nowrap">Nama</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center bg-primary/5">PER</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">Point</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">FGM</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">FGA</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">FTA</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">FTM</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">ORB</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">DRB</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">STL</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">AST</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">BLK</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">PF</th>
                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap text-center">TO</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#393028]">
                @forelse($players as $player)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 text-white font-bold text-sm whitespace-nowrap">{{ $player->name }}</td>
                    <td class="p-4 text-primary font-black text-sm text-center bg-primary/5">{{ $player->total_stats['per'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['point'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['fgm'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['fga'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['fta'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['ftm'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['orb'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['drb'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['stl'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['ast'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['blk'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['pf'] ?? 0 }}</td>
                    <td class="p-4 text-slate-300 text-sm text-center">{{ $player->total_stats['to'] ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td class="p-4 text-slate-500 text-center" colspan="14">No statistics available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1rem; padding: 0 1rem; }
    .dataTables_wrapper .dataTables_filter input { background-color: #221914; border: 1px solid #393028; color: #f1f5f9; border-radius: 9999px; padding: 0.5rem 1rem; margin-left: 0.5rem; outline: none; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #f48c25; }
    .dataTables_wrapper .dataTables_length select { background-color: #221914; border: 1px solid #393028; color: #f1f5f9; border-radius: 0.5rem; padding: 0.25rem; }
    table.dataTable.no-footer { border-bottom: 1px solid #393028; }
    table.dataTable thead th { border-bottom: 1px solid #393028 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { background: #221914 !important; border: 1px solid #393028 !important; color: #94a3b8 !important; border-radius: 0.5rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #2c221c !important; color: #f1f5f9 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #f48c25 !important; color: #181411 !important; border-color: #f48c25 !important; font-weight: bold; }
    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after { color: #f48c25 !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#statsTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "order": [[1, 'desc']],
        });
    });
</script>
@endpush
@endsection
