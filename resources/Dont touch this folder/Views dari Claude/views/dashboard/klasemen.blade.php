@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-amber-500 text-2xl">emoji_events</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Klasemen / Standings</h1>
        <p class="text-slate-400 text-sm">{{ $tournament->name }}</p>
    </div>
</div>

{{-- Standings Table --}}
<div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table id="klasemenTable" class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-[#393028] bg-gradient-to-r from-primary/10 to-transparent">
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap">No</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap">Tim</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center">Permainan</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center">Kemenangan</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center">Kekalahan</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center">Total Poin</th>
                    <th class="p-4 text-xs font-bold text-primary uppercase tracking-wider whitespace-nowrap text-center">Avg Poin/Game</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#393028]">
                @php $no = 1; @endphp
                @foreach($teams as $team)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-4">
                            <div class="size-7 rounded-lg {{ $no <= 3 ? 'bg-primary/10 text-primary border border-primary/30' : 'bg-[#221914] text-slate-400 border border-[#393028]' }} flex items-center justify-center text-xs font-black">
                                {{ $no++ }}
                            </div>
                        </td>
                        <td class="p-4 text-white font-bold text-sm">{{ $team->name }}</td>
                        <td class="p-4 text-slate-300 text-sm text-center font-semibold">{{ $team->matches_played ?? 0 }}</td>
                        <td class="p-4 text-emerald-400 text-sm text-center font-bold">{{ $team->wins ?? 0 }}</td>
                        <td class="p-4 text-red-400 text-sm text-center font-bold">{{ $team->losses ?? 0 }}</td>
                        <td class="p-4 text-white text-sm text-center font-black">{{ $team->total_points ?? 0 }}</td>
                        <td class="p-4 text-primary text-sm text-center font-bold">{{ number_format($team->avg_points_per_game ?? 0, 2) }}</td>
                    </tr>
                @endforeach
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
    table.dataTable thead th, table.dataTable thead td { border-bottom: 1px solid #393028 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { background: #221914 !important; border: 1px solid #393028 !important; color: #94a3b8 !important; border-radius: 0.5rem; padding: 0.25rem 0.75rem; margin: 0 0.2rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #2c221c !important; color: #f1f5f9 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: #f48c25 !important; color: #181411 !important; border-color: #f48c25 !important; font-weight: bold; }
    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after { color: #f48c25 !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#klasemenTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true
        });
    });
</script>
@endpush
@endsection