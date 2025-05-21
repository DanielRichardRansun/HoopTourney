@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
    }
    .match-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        text-transform: uppercase;
        font-weight: bold;
    }
    
    .status-completed {
        background-color: #28a745;
        color: white;
    }
    
    .status-upcoming {
        background-color: #ffc107;
        color: #1e3c72;
    }
    
    .match-card {
        border-radius: 10px;
        box-shadow: #1e3c72;
        margin-bottom: 2rem;
        border: none;
    }
    
    .match-card-header {
        background:#1e3c72;
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }
    
    .team-display {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .team-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .team-score {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    .score-winner {
        color: #28a745;
    }
    
    .score-loser {
        color: #dc3545;
    }
    
    .winner-banner {
        text-align: center;
        margin: 1.5rem 0;
        padding: 1rem;
        border-radius: 5px;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .winner-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .match-info {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .match-info i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }
    
    .stats-section {
        margin-bottom: 2rem;
    }
    
    .stats-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #1e3c72;
    }
    
    .stats-table {
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    #stat_team {
        font-size: 12px;
    }

    #stat_team2 {
        font-size: 12px;
    }
    
    .stats-table thead {
        background-color: #1e3c72;
        color: white;
    }
    
    .stats-table th {
        padding: 0.75rem;
        text-align: left;
    }
    
    .stats-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .stats-table tr:hover {
        /* background-color: #f8f9fa; */
    }
    
    .no-stats {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }
    
    .vs-separator {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0 1rem;
        align-self: center;
    }
    
    .teams-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .team-container {
        flex: 1;
        min-width: 200px;
        text-align: center;
    }
</style>

<div class="container">
    <!-- Match Summary Card -->
    <div class="card match-card">
        <div class="card-header match-card-header">
            <h4 style="text-align: center;">Match Summary</h4>
        </div>
        <div class="card-body">
            <div class="teams-container">
                <div class="team-container">
                    <div class="team-display">
                        <h5 class="team-name">{{ $schedule->team1->name ?? 'TBD' }}</h5>
                        @if($matchResult)
                        <div class="team-score {{ $matchResult->winning_team_id == $schedule->team1->id ? 'score-winner' : 'score-loser' }}">
                            {{ $matchResult->team1_score }}
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="vs-separator">VS</div>
                
                <div class="team-container">
                    <div class="team-display">
                        <h5 class="team-name">{{ $schedule->team2->name ?? 'TBD' }}</h5>
                        @if($matchResult)
                        <div class="team-score {{ $matchResult->winning_team_id == $schedule->team2->id ? 'score-winner' : 'score-loser' }}">
                            {{ $matchResult->team2_score }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($matchResult)
            <div class="winner-banner winner-success">
                Winner: <strong>{{ $matchResult->winning_team_id == $schedule->team1->id ? $schedule->team1->name : $schedule->team2->name }}</strong>
            </div>
            @endif
            
            <div style="margin-top: 2rem;">
                <div class="match-info">
                    <i class="far fa-calendar-alt"></i>
                    <span>{{ \Carbon\Carbon::parse($schedule->date)->format('l, F j, Y') }}</span>
                </div>
                <div class="match-info">
                    <i class="far fa-clock"></i>
                    <span>{{ \Carbon\Carbon::parse($schedule->date)->format('g:i A') }}</span>
                </div>
                <div class="match-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $schedule->location }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Statistics Section -->
@if ($playerStats->count())
<div class="card match-card">
    <div class="card-header match-card-header">
        <h4 style="text-align: center;">Player Statistics</h4>
    </div>
    <div class="card-body">
        <!-- Team 1 Stats -->
        <div class="stats-section">
            <h5 class="stats-title">{{ $schedule->team1->name }}</h5>
            <table class="stats-table table table-bordered table-hover" id="stat_team">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>PTS</th>
                        <th>FGM</th>
                        <th>FGA</th>
                        <th>FTM</th>
                        <th>FTA</th>
                        <th>ORB</th>
                        <th>DRB</th>
                        <th>REB</th>
                        <th>AST</th>
                        <th>STL</th>
                        <th>BLK</th>
                        <th>TO</th>
                        <th>PF</th>
                        <th>PER</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($playerStats->where('player.teams_id', $schedule->team1->id) as $stat)
                        <tr>
                            <td>{{ $stat->player->name }}</td>
                            <td>{{ $stat->point }}</td>
                            <td>{{ $stat->fgm }}</td>
                            <td>{{ $stat->fga }}</td>
                            <td>{{ $stat->ftm }}</td>
                            <td>{{ $stat->fta }}</td>
                            <td>{{ $stat->orb }}</td>
                            <td>{{ $stat->drb }}</td>
                            <td>{{ $stat->orb + $stat->drb }}</td>
                            <td>{{ $stat->ast }}</td>
                            <td>{{ $stat->stl }}</td>
                            <td>{{ $stat->blk }}</td>
                            <td>{{ $stat->to }}</td>
                            <td>{{ $stat->pf }}</td>
                            <td style="background-color: #e0e0e0; font-weight: bold;">{{ $stat->per }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Team 2 Stats -->
        <div class="stats-section">
            <h5 class="stats-title">{{ $schedule->team2->name }}</h5>
            <table class="stats-table table table-bordered table-hover" id="stat_team2">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>PTS</th>
                        <th>FGM</th>
                        <th>FGA</th>
                        <th>FTM</th>
                        <th>FTA</th>
                        <th>ORB</th>
                        <th>DRB</th>
                        <th>REB</th>
                        <th>AST</th>
                        <th>STL</th>
                        <th>BLK</th>
                        <th>TO</th>
                        <th>PF</th>
                        <th>PER</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($playerStats->where('player.teams_id', $schedule->team2->id) as $stat)
                        <tr>
                            <td>{{ $stat->player->name }}</td>
                            <td>{{ $stat->point }}</td>
                            <td>{{ $stat->fgm }}</td>
                            <td>{{ $stat->fga }}</td>
                            <td>{{ $stat->ftm }}</td>
                            <td>{{ $stat->fta }}</td>
                            <td>{{ $stat->orb }}</td>
                            <td>{{ $stat->drb }}</td>
                            <td>{{ $stat->orb + $stat->drb }}</td>
                            <td>{{ $stat->ast }}</td>
                            <td>{{ $stat->stl }}</td>
                            <td>{{ $stat->blk }}</td>
                            <td>{{ $stat->to }}</td>
                            <td>{{ $stat->pf }}</td>
                            <td style="background-color: #e0e0e0; font-weight: bold;">{{ $stat->per }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card match-card">
    <div class="card-body no-stats">
        <p>No player statistics available for this match.</p>
    </div>
</div>
@endif
@endsection

<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#stat_team').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "order": [[1, 'desc']],
        });
    });

    $(document).ready(function() {
        $('#stat_team2').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "order": [[1, 'desc']],
        });
    });
    
</script>