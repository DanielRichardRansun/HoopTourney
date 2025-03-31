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
        padding: 20px 0;
        text-align: center;
        border-radius: 10px;
    }

    .table-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #1e3c72;
        color: white;
        text-align: center;
    }

    .table-hover tbody tr:hover {
        background-color: #e9ecef;
        cursor: pointer;
    }

    .table td {
        text-align: center;
        vertical-align: middle;
    }

    .highlight-per {
        background-color: #d7d7d7;
        font-weight: bold;
    }

    .dataTables_length label,
    .dataTables_filter label {
        font-weight: normal !important;
    }
</style>

<div class="container">
    <div class="header">
        <h2>Statistik Rata-Rata Pemain {{ $tournament->name }}</h2>
    </div>

    <!-- Filter Team -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <select name="team_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Pilih Tim --</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Tabel Statistik -->
    <div class="table-container mt-4">
        <div class="table-responsive">
            <table id="statsTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nama Pemain</th>
                        <th class="highlight-per">PER</th>
                        <th>Point</th>
                        <th>FGM</th>
                        <th>FGA</th>
                        <th>FTA</th>
                        <th>FTM</th>
                        <th>ORB</th>
                        <th>DRB</th>
                        <th>STL</th>
                        <th>AST</th>
                        <th>BLK</th>
                        <th>PF</th>
                        <th>TO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                    <tr>
                        <td>{{ $player->name }}</td>
                        <td class="highlight-per">{{ $player->total_stats['per'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['point'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['fgm'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['fga'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['fta'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['ftm'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['orb'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['drb'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['stl'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['ast'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['blk'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['pf'] ?? 0 }}</td>
                        <td>{{ $player->total_stats['to'] ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center">-</td>
                        @for ($i = 1; $i < 14; $i++)
                            <td>-</td>
                        @endfor
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#statsTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true
        });
    });
</script>
@endsection
