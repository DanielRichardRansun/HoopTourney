@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
    }
    body {
        background-color: #f8f9fa;
    }
    .header {
        background: white;
        color: black;
        text-align: center;
        border-radius: 10px;
    }
    .table th {
        background-color: #1e3c72;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: #e9ecef;
        cursor: pointer;
    }
    .table td {
        text-align: center;
        vertical-align: middle;
    }
    .table-container {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .points {
        font-weight: bold;
        color: #6610f2;
    }
    .dataTables_length label,
    .dataTables_filter label {
    font-weight: normal !important;
}

</style>

<div class="container mt-5">
    <div class="header">
        <h2>Klasemen / Statistik Tim {{ $tournament->name }}</h2>
    </div>

<!-- Tabel Klasemen -->
<div class="table-container mt-4">
    <div class="table-responsive">
        <table id="klasemenTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tim</th>
                    <th>Permainan</th>
                    <th>Kemenangan</th>
                    <th>Kekalahan</th>
                    <th>Total Poin</th>
                    <th>Avg Poin/Game</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($teams as $team)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->matches_played ?? 0 }}</td>
                        <td>{{ $team->wins ?? 0 }}</td>
                        <td>{{ $team->losses ?? 0 }}</td>
                        <td>{{ $team->total_points ?? 0 }}</td>
                        <td>{{ number_format($team->avg_points_per_game ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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
@endsection