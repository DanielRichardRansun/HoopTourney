@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.95) 0%, rgba(42, 82, 152, 0.95) 100%);
            color: white;
            padding: 40px 0;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header img {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        .table-title {
            color: #2a5298;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table thead th {
            background-color: #2a5298;
            color: white;
            border: none;
            font-weight: 500;
        }
        .table tbody tr {
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: #f1f5fd;
            transform: translateY(-1px);
        }
        .badge {
            font-weight: 500;
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        .btn-primary {
            background-color: #2a5298;
            border-color: #2a5298;
        }
        .btn-primary:hover {
            background-color: #1e3c72;
            border-color: #1e3c72;
        }
        .stat-highlight {
            color: #2a5298;
            font-weight: 600;
        }
        .player-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .player-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .player-card-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px;
        }
        .player-card-body {
            padding: 15px;
            background: white;
        }
        .stat-badge {
            background-color: #e9f0fb;
            color: #2a5298;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .gold-row {
        background-color: #fff9e6;
        border-left: 4px solid #ffd700;
    }
    .silver-row {
        background-color: #f5f5f5;
        border-left: 4px solid #c0c0c0;
    }
    .bronze-row {
        background-color: #f8f1e6;
        border-left: 4px solid #cd7f32;
    }
    .gold-icon {
        color: #ffd700;
        margin-right: 5px;
    }
    .silver-icon {
        color: #c0c0c0;
        margin-right: 5px;
    }
    .bronze-icon {
        color: #cd7f32;
        margin-right: 5px;
    }
    .table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }


    /* Untuk membuat tabel transparan */
    .table {
        background-color: transparent !important;
    }
    
    /* Header tabel TIDAK transparan */
    .table thead tr {
        background-color: #2a5298 !important;
    }
    
    /* Untuk membuat sel-sel td transparan */
    .table td {
        background-color: transparent !important;
        border-color: rgba(0, 0, 0, 0.1) !important; /* Garis lebih subtle */
        color: #000 !important; /* Teks hitam */
    }
    
    /* Header cells */
    .table th {
        background-color: #2a5298 !important; /* Warna default Bootstrap */
        border-color: #2a5298 !important; /* Warna border default */
        color: #ffffff !important; /* Teks gelap */
    }
    
    /* Untuk hover effect */
    .table-hover tbody tr:hover td {
        background-color: rgba(0, 0, 0, 0.05) !important; /* Hover lebih subtle */
    }
    
    /* Warna khusus untuk baris peringkat */
    .gold-row td {
        background-color: rgba(255, 215, 0, 0.1) !important;
    }
    .silver-row td {
        background-color: rgba(192, 192, 192, 0.1) !important;
    }
    .bronze-row td {
        background-color: rgba(205, 127, 50, 0.1) !important;
    }
    
    /* Warna ikon trofi */
    .gold-icon {
        color: gold !important;
    }
    .silver-icon {
        color: silver !important;
    }
    .bronze-icon {
        color: #cd7f32 !important; /* Warna bronze */
    }
    
    /* Gaya khusus header */
    .table thead th {
        border-bottom-width: 2px !important;
        border-top: none !important;
        font-weight: 600 !important; /* Tebalkan font header */
    }
    </style>

    <div class="container">
        <div class="header text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="Hoop Tourney Logo" style="width: 120px;">
            <h2 class="mt-2">Welcome to the Hoop Tourney</h2>
        </div>

        @guest
            <div class="alert alert-primary text-center" role="alert">
                <strong>Buat Akun dan Kelola Tournament Anda Sendiri!</strong>
                <a href="{{ route('register') }}" class="btn btn-sm btn-light ml-2">Register</a>
                <a href="{{ route('login') }}" class="btn btn-sm btn-light ml-2">Login</a>
            </div>
        @endguest

        @auth
        <div class="mb-2">
            <a href="{{ route('tournament.create') }}" class="btn btn-primary">Buat Tourney</a>
        </div>
        @endauth

        <div class="table-responsive">
            <table id="tournamentTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tournament Name</th>
                        <th>Organizer</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        @if(Auth::check() && Auth::user()->role == 2)
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($tournaments as $tournament)
                    <tr onclick="window.location='{{ route('tournament.detail', $tournament->id) }}'" style="cursor: pointer;">
                        <td>{{ $no++ }}</td>
                        <td>{{ $tournament->name }}</td>
                        <td>{{ $tournament->organizer }}</td>
                        <td>
                            <span class="badge 
                                @if($tournament->status == 'upcoming') badge-warning 
                                @elseif($tournament->status == 'scheduled') badge-primary 
                                @elseif($tournament->status == 'ongoing') badge-success 
                                @elseif($tournament->status == 'completed') badge-secondary 
                                @endif">
                                {{ ucfirst($tournament->status) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($tournament->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($tournament->end_date)->format('d M Y') }}</td>

                        @if(Auth::check() && Auth::user()->role == 2)
                        <td>
                            @php
                                $userTeamId = Auth::user()->team_id;
                                $isJoined = DB::table('team_tournament')
                                            ->where('team_id', $userTeamId)
                                            ->where('tournament_id', $tournament->id)
                                            ->exists();
                                
                                $requestStatus = DB::table('tournament_requests')
                                                ->where('team_id', $userTeamId)
                                                ->where('tournament_id', $tournament->id)
                                                ->value('status');
                            @endphp

                            @if($tournament->status == 'upcoming')
                                @if($isJoined)
                                    <span class="text-success">Joined!</span>
                                @else
                                    @switch($requestStatus)
                                        @case('pending')
                                            <span class="text-info">Sending Request...</span>
                                            @break
                                        @case('rejected')
                                            <span class="text-danger">Request Rejected!</span>
                                            @break
                                        @default
                                            <form action="{{ route('tournament.join', $tournament->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Join</button>
                                            </form>
                                    @endswitch
                                @endif
                            @else
                                <a href="{{ route('tournament.detail', $tournament->id) }}" class="btn btn-sm btn-secondary">Lihat</a>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="text-center" style="margin-top: 40px; margin-bottom: 15px;">
            <h2>Top 10 Hoop Tourney Players</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Player</th>
                        <th>Team</th>
                        <th>PPG</th>
                        <th>APG</th>
                        <th>RPG</th>
                        <th>BPG</th>
                        <th>SPG</th>
                        <th>PER</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topPlayers = DB::table('player_stats')
                            ->select(
                                'players.id',
                                'players.name as player_name',
                                'teams.name as team_name',
                                DB::raw('ROUND(AVG(player_stats.point), 1) as avg_points'),
                                DB::raw('ROUND(AVG(player_stats.ast), 1) as avg_assists'),
                                DB::raw('ROUND(AVG(player_stats.orb + player_stats.drb), 1) as avg_rebounds'),
                                DB::raw('ROUND(AVG(player_stats.blk), 1) as avg_blocks'),
                                DB::raw('ROUND(AVG(player_stats.stl), 1) as avg_steals'),
                                DB::raw('ROUND(AVG(player_stats.per), 1) as avg_per')
                            )
                            ->join('players', 'player_stats.players_id', '=', 'players.id')
                            ->join('teams', 'players.teams_id', '=', 'teams.id')
                            ->groupBy('players.id', 'players.name', 'teams.name')
                            ->orderBy('avg_per', 'DESC')
                            ->limit(10)
                            ->get();
                    @endphp
            
                    @foreach($topPlayers as $index => $player)
                    <tr class="@if($index == 0) gold-row @elseif($index == 1) silver-row @elseif($index == 2) bronze-row @endif">
                        <td>
                            @if($index == 0)
                                <i class="fas fa-trophy gold-icon"></i>
                            @elseif($index == 1)
                                <i class="fas fa-trophy silver-icon"></i>
                            @elseif($index == 2)
                                <i class="fas fa-trophy bronze-icon"></i>
                            @endif
                            {{ $index + 1 }}
                        </td>
                        <td>{{ $player->player_name }}</td>
                        <td>{{ $player->team_name }}</td>
                        <td>{{ $player->avg_points }}</td>
                        <td>{{ $player->avg_assists }}</td>
                        <td>{{ $player->avg_rebounds }}</td>
                        <td>{{ $player->avg_blocks }}</td>
                        <td>{{ $player->avg_steals }}</td>
                        <td class="font-weight-bold">{{ $player->avg_per }}</td>
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
            $('#tournamentTable').DataTable();
        });
    </script>
@endsection