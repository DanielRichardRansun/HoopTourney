@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-teal-500 text-2xl">badge</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Informasi Tim</h1>
        <p class="text-slate-400 text-sm">Team roster and details</p>
    </div>
</div>

{{-- Team Info Card --}}
<div class="glass-panel rounded-2xl p-6 md:p-8 border border-[#393028] shadow-xl mb-8 relative overflow-hidden">
    <div class="absolute -top-16 -right-16 size-40 bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex items-center gap-6 relative z-10">
        <div class="size-20 rounded-2xl bg-gradient-to-br from-primary/20 to-orange-600/10 border border-primary/30 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-primary text-4xl">sports_basketball</span>
        </div>
        <div>
            <h2 class="text-2xl font-black text-white mb-2">{{ $team->name }}</h2>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-slate-500 text-[16px]">sports</span>
                    <span class="text-slate-500 font-bold">Coach:</span>
                    <span class="text-slate-300 font-semibold">{{ $team->coach }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-slate-500 text-[16px]">manage_accounts</span>
                    <span class="text-slate-500 font-bold">Manager:</span>
                    <span class="text-slate-300 font-semibold">{{ $team->manager }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Players Table --}}
<h3 class="text-lg font-black text-white uppercase tracking-tight mb-4 flex items-center gap-2">
    <span class="material-symbols-outlined text-primary">people</span> Daftar Pemain
</h3>

@if($players->isEmpty())
    <div class="glass-panel rounded-2xl p-12 text-center border border-[#393028]">
        <p class="text-slate-500 text-sm">Belum ada pemain dalam tim ini.</p>
    </div>
@else
    <div class="glass-panel rounded-2xl border border-[#393028] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table id="playersTable" class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#393028] bg-gradient-to-r from-teal-500/10 to-transparent">
                        <th class="p-4 text-xs font-bold text-teal-400 uppercase tracking-wider whitespace-nowrap text-center w-16">#</th>
                        <th class="p-4 text-xs font-bold text-teal-400 uppercase tracking-wider whitespace-nowrap">Nama</th>
                        <th class="p-4 text-xs font-bold text-teal-400 uppercase tracking-wider whitespace-nowrap text-center">No. Punggung</th>
                        <th class="p-4 text-xs font-bold text-teal-400 uppercase tracking-wider whitespace-nowrap text-center">Posisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#393028]">
                    @foreach($players as $index => $player)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4 text-center">
                                <div class="size-7 rounded-lg bg-[#221914] border border-[#393028] flex items-center justify-center text-xs font-bold text-slate-400 mx-auto">{{ $index + 1 }}</div>
                            </td>
                            <td class="p-4 text-white font-bold text-sm">{{ $player->name }}</td>
                            <td class="p-4 text-center">
                                <span class="bg-primary/10 text-primary font-black text-sm px-3 py-1 rounded-lg border border-primary/30">{{ $player->jersey_number }}</span>
                            </td>
                            <td class="p-4 text-slate-300 text-sm text-center font-semibold">{{ $player->position }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

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
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #f48c25 !important; color: #181411 !important; border-color: #f48c25 !important; }
    table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after { color: #f48c25 !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#playersTable').DataTable({ "paging": true, "ordering": true, "info": true, "searching": true });
    });
</script>
@endpush
@endsection
