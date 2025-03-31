@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 900px;
    }
    .team-card {
        border: 2px solid #1e3c72;
        border-radius: 10px;
        padding: 20px;
        background-color: #f8f9fa;
        text-align: center;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
    .team-title {
        font-weight: bold;
        background-color: #1e3c72;
        color: white;
        padding: 12px;
        border-radius: 5px;
        font-size: 1.5em;
        margin-bottom: 15px;
    }
    .team-info {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .player-table {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .table thead {
        background-color: #1e3c72;
        color: white;
    }
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        padding: 12px;
    }
    .dataTables_length label,
    .dataTables_filter label {
    font-weight: normal !important;
}
</style>

<div class="container mt-5">
    <div class="team-card">
        <div class="team-title">Informasi Tim</div>
        <p class="team-info"><strong>Nama:</strong> {{ $team->name }}</p>
        <p class="team-info"><strong>Coach:</strong> {{ $team->coach }}</p>
        <p class="team-info"><strong>Manager:</strong> {{ $team->manager }}</p>
    </div>

    <h3 class="mt-5 text-center">Daftar Pemain</h3>
<div class="table-responsive player-table mt-3">
    <table class="table table-bordered" id="playersTable">
        <thead> 
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>No. Punggung</th>
                <th>Posisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $index => $player)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $player->name }}</td>
                    <td>{{ $player->jersey_number }}</td>
                    <td>{{ $player->position }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($players->isEmpty())
    <p class="text-center text-muted mt-4">Belum ada pemain dalam tim ini.</p>
@endif
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#playersTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true
        });
    });
</script>

@endsection


