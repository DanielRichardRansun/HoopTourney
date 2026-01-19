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
    }.d-none {
        display: none !important;
    }
    .active.btn-outline-primary {
        background-color: #007bff; /* Warna aktif untuk tombol */
        color: white;
    }
</style>

<div class="container">
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

    @if($matchResult && $quarterResults->count())
    <div class="card mt-4">
        <div class="card-body text-center">
            <h5>Pilih Quarter:</h5>
            <div class="btn-group mb-2" role="group">
                <button type="button" class="btn btn-outline-primary active" data-quarter-selector="all">All Result</button>
                @foreach ($quarterResults->sortBy('quarter_number') as $qr)
                    <button type="button" class="btn btn-outline-primary" data-quarter-selector="{{ $qr->quarter_number }}">
                        Quarter {{ $qr->quarter_number }}
                    </button>
                @endforeach
            </div>
            <div id="quarter-score-display" class="mt-3">
                </div>
        </div>
    </div>
    @endif

    {{-- Player Statistics Section --}}
    @if ($matchResult)
    <div class="card match-card mt-4">
        <div class="card-header match-card-header">
            <h4 style="text-align: center;">Player Statistics</h4>
        </div>
        <div class="card-body">
            {{-- Team 1 Stats --}}
            <div class="stats-section mt-3">
                <h5 class="stats-title">{{ $schedule->team1->name }}</h5>
                <div class="table-responsive">
                    <table class="stats-table table table-bordered table-hover" id="stat_team1_main">
                        <thead>
                            <tr>
                                <th>Player</th><th>PTS</th><th>FGM</th><th>FGA</th><th>FTM</th><th>FTA</th>
                                <th>ORB</th><th>DRB</th><th>REB</th><th>AST</th><th>STL</th><th>BLK</th><th>TO</th><th>PF</th><th>PER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Initialize player totals for Team 1
                                $team1PlayerTotals = [];
                                foreach ($schedule->team1->players as $player) {
                                    $team1PlayerTotals[$player->id] = [
                                        'player_name' => $player->name,
                                        'point' => 0, 'fgm' => 0, 'fga' => 0, 'ftm' => 0, 'fta' => 0,
                                        'orb' => 0, 'drb' => 0, 'stl' => 0, 'ast' => 0, 'blk' => 0,
                                        'pf' => 0, 'to' => 0, 'per' => 0, 'quarter_count' => 0
                                    ];
                                }
                            @endphp

                            {{-- Loop through all player stats to populate the table and calculate totals --}}
                            @foreach ($playerStatsPerQuarter as $quarterNumber => $stats)
                                @foreach ($stats->where('player.teams_id', $schedule->team1->id) as $stat)
                                    <tr class="player-stat-row" data-quarter-number="{{ $quarterNumber }}" data-team-id="{{ $schedule->team1->id }}">
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
                                        <td>{{ number_format($stat->per, 2) }}</td>
                                    </tr>
                                    @php
                                        // Accumulate totals for "All Result"
                                        $team1PlayerTotals[$stat->players_id]['point'] += $stat->point;
                                        $team1PlayerTotals[$stat->players_id]['fgm'] += $stat->fgm;
                                        $team1PlayerTotals[$stat->players_id]['fga'] += $stat->fga;
                                        $team1PlayerTotals[$stat->players_id]['ftm'] += $stat->ftm;
                                        $team1PlayerTotals[$stat->players_id]['fta'] += $stat->fta;
                                        $team1PlayerTotals[$stat->players_id]['orb'] += $stat->orb;
                                        $team1PlayerTotals[$stat->players_id]['drb'] += $stat->drb;
                                        $team1PlayerTotals[$stat->players_id]['stl'] += $stat->stl;
                                        $team1PlayerTotals[$stat->players_id]['ast'] += $stat->ast;
                                        $team1PlayerTotals[$stat->players_id]['blk'] += $stat->blk;
                                        $team1PlayerTotals[$stat->players_id]['pf'] += $stat->pf;
                                        $team1PlayerTotals[$stat->players_id]['to'] += $stat->to;
                                        $team1PlayerTotals[$stat->players_id]['per'] += $stat->per; // Sum PER, will average later
                                        $team1PlayerTotals[$stat->players_id]['quarter_count']++;
                                    @endphp
                                @endforeach
                            @endforeach
                            {{-- Rows for "All Result" (initially hidden, managed by JS) --}}
                            @foreach ($team1PlayerTotals as $playerId => $totalStat)
                                @php
                                    $avgPer = $totalStat['quarter_count'] > 0 ? $totalStat['per'] / $totalStat['quarter_count'] : 0;
                                @endphp
                                <tr class="player-stat-row all-result-row" data-quarter-number="all" data-player-id="{{ $playerId }}" data-team-id="{{ $schedule->team1->id }}">
                                    <td>{{ $totalStat['player_name'] }}</td>
                                    <td>{{ $totalStat['point'] }}</td>
                                    <td>{{ $totalStat['fgm'] }}</td>
                                    <td>{{ $totalStat['fga'] }}</td>
                                    <td>{{ $totalStat['ftm'] }}</td>
                                    <td>{{ $totalStat['fta'] }}</td>
                                    <td>{{ $totalStat['orb'] }}</td>
                                    <td>{{ $totalStat['drb'] }}</td>
                                    <td>{{ $totalStat['orb'] + $totalStat['drb'] }}</td>
                                    <td>{{ $totalStat['ast'] }}</td>
                                    <td>{{ $totalStat['stl'] }}</td>
                                    <td>{{ $totalStat['blk'] }}</td>
                                    <td>{{ $totalStat['to'] }}</td>
                                    <td>{{ $totalStat['pf'] }}</td>
                                    <td>{{ number_format($avgPer, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Team 2 Stats --}}
            <div class="stats-section mt-3">
                <h5 class="stats-title">{{ $schedule->team2->name }}</h5>
                <div class="table-responsive">
                    <table class="stats-table table table-bordered table-hover" id="stat_team2_main">
                        <thead>
                            <tr>
                                <th>Player</th><th>PTS</th><th>FGM</th><th>FGA</th><th>FTM</th><th>FTA</th>
                                <th>ORB</th><th>DRB</th><th>REB</th><th>AST</th><th>STL</th><th>BLK</th><th>TO</th><th>PF</th><th>PER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Initialize player totals for Team 2
                                $team2PlayerTotals = [];
                                foreach ($schedule->team2->players as $player) {
                                    $team2PlayerTotals[$player->id] = [
                                        'player_name' => $player->name,
                                        'point' => 0, 'fgm' => 0, 'fga' => 0, 'ftm' => 0, 'fta' => 0,
                                        'orb' => 0, 'drb' => 0, 'stl' => 0, 'ast' => 0, 'blk' => 0,
                                        'pf' => 0, 'to' => 0, 'per' => 0, 'quarter_count' => 0
                                    ];
                                }
                            @endphp

                            {{-- Loop through all player stats to populate the table and calculate totals --}}
                            @foreach ($playerStatsPerQuarter as $quarterNumber => $stats)
                                @foreach ($stats->where('player.teams_id', $schedule->team2->id) as $stat)
                                    <tr class="player-stat-row" data-quarter-number="{{ $quarterNumber }}" data-team-id="{{ $schedule->team2->id }}">
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
                                        <td>{{ number_format($stat->per, 2) }}</td>
                                    </tr>
                                    @php
                                        // Accumulate totals for "All Result"
                                        $team2PlayerTotals[$stat->players_id]['point'] += $stat->point;
                                        $team2PlayerTotals[$stat->players_id]['fgm'] += $stat->fgm;
                                        $team2PlayerTotals[$stat->players_id]['fga'] += $stat->fga;
                                        $team2PlayerTotals[$stat->players_id]['ftm'] += $stat->ftm;
                                        $team2PlayerTotals[$stat->players_id]['fta'] += $stat->fta;
                                        $team2PlayerTotals[$stat->players_id]['orb'] += $stat->orb;
                                        $team2PlayerTotals[$stat->players_id]['drb'] += $stat->drb;
                                        $team2PlayerTotals[$stat->players_id]['stl'] += $stat->stl;
                                        $team2PlayerTotals[$stat->players_id]['ast'] += $stat->ast;
                                        $team2PlayerTotals[$stat->players_id]['blk'] += $stat->blk;
                                        $team2PlayerTotals[$stat->players_id]['pf'] += $stat->pf;
                                        $team2PlayerTotals[$stat->players_id]['to'] += $stat->to;
                                        $team2PlayerTotals[$stat->players_id]['per'] += $stat->per; // Sum PER, will average later
                                        $team2PlayerTotals[$stat->players_id]['quarter_count']++;
                                    @endphp
                                @endforeach
                            @endforeach
                            {{-- Rows for "All Result" (initially hidden, managed by JS) --}}
                            @foreach ($team2PlayerTotals as $playerId => $totalStat)
                                @php
                                    $avgPer = $totalStat['quarter_count'] > 0 ? $totalStat['per'] / $totalStat['quarter_count'] : 0;
                                @endphp
                                <tr class="player-stat-row all-result-row" data-quarter-number="all" data-player-id="{{ $playerId }}" data-team-id="{{ $schedule->team2->id }}">
                                    <td>{{ $totalStat['player_name'] }}</td>
                                    <td>{{ $totalStat['point'] }}</td>
                                    <td>{{ $totalStat['fgm'] }}</td>
                                    <td>{{ $totalStat['fga'] }}</td>
                                    <td>{{ $totalStat['ftm'] }}</td>
                                    <td>{{ $totalStat['fta'] }}</td>
                                    <td>{{ $totalStat['orb'] }}</td>
                                    <td>{{ $totalStat['drb'] }}</td>
                                    <td>{{ $totalStat['orb'] + $totalStat['drb'] }}</td>
                                    <td>{{ $totalStat['ast'] }}</td>
                                    <td>{{ $totalStat['stl'] }}</td>
                                    <td>{{ $totalStat['blk'] }}</td>
                                    <td>{{ $totalStat['to'] }}</td>
                                    <td>{{ $totalStat['pf'] }}</td>
                                    <td>{{ number_format($avgPer, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
</div>

@endsection

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    const quarterResultsData = @json($quarterResults->keyBy('quarter_number'));
    const allMatchResult = @json($matchResult);

    $(document).ready(function() {
        // Initialize DataTables once for each main table
        var table1 = $('#stat_team1_main').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true,
            "order": [[1, 'desc']],
            "responsive": true,
            "autoWidth": false,
            "retrieve": true // Ensure DataTables can be retrieved if already initialized
        });

        var table2 = $('#stat_team2_main').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true,
            "order": [[1, 'desc']],
            "responsive": true,
            "autoWidth": false,
            "retrieve": true // Ensure DataTables can be retrieved if already initialized
        });

        // Function to filter DataTables based on selected quarter
        function filterTablesByQuarter(quarterNumber) {
            // Apply filter for Team 1 table
            table1.rows().every(function() {
                const rowQuarter = $(this.node()).data('quarter-number');
                if (quarterNumber === 'all') {
                    // Show "All Result" rows, hide individual quarter rows
                    if ($(this.node()).hasClass('all-result-row')) {
                        this.nodes().to$().show();
                    } else {
                        this.nodes().to$().hide();
                    }
                } else {
                    // Show specific quarter rows, hide "All Result" rows
                    if (rowQuarter == quarterNumber) {
                        this.nodes().to$().show();
                    } else {
                        this.nodes().to$().hide();
                    }
                }
            });
            table1.draw(false); // Draw false to retain current paging/ordering

            // Apply filter for Team 2 table
            table2.rows().every(function() {
                const rowQuarter = $(this.node()).data('quarter-number');
                if (quarterNumber === 'all') {
                    // Show "All Result" rows, hide individual quarter rows
                    if ($(this.node()).hasClass('all-result-row')) {
                        this.nodes().to$().show();
                    } else {
                        this.nodes().to$().hide();
                    }
                } else {
                    // Show specific quarter rows, hide "All Result" rows
                    if (rowQuarter == quarterNumber) {
                        this.nodes().to$().show();
                    } else {
                        this.nodes().to$().hide();
                    }
                }
            });
            table2.draw(false); // Draw false to retain current paging/ordering
        }

        // Function to display quarter scores
        function displayCurrentQuarterScore(quarterNumber) {
            const displayArea = $('#quarter-score-display');
            displayArea.empty(); // Clear previous content

            if (quarterNumber === 'all') {
                if (allMatchResult) {
                    displayArea.append(`
                        <p class="mb-0"><strong>Final Score:</strong> {{ $schedule->team1->name }} ${allMatchResult.team1_score} | ${allMatchResult.team2_score} {{ $schedule->team2->name }}</p>
                    `);
                } else {
                    displayArea.append('<p class="text-muted">Final scores not available.</p>');
                }
            } else {
                const quarter = quarterResultsData[quarterNumber];
                if (quarter) {
                    displayArea.append(`
                        <p class="mb-0"><strong>Quarter ${quarterNumber} Score:</strong> {{ $schedule->team1->name }} ${quarter.team1_score} | ${quarter.team2_score} {{ $schedule->team2->name }}</p>
                    `);
                } else {
                    displayArea.append('<p class="text-muted">No score recorded for Quarter ${quarterNumber}.</p>');
                }
            }
        }


        // Event listener for quarter selection buttons
        $('[data-quarter-selector]').on('click', function() {
            $('[data-quarter-selector]').removeClass('active');
            $(this).addClass('active');

            const selectedQuarter = $(this).data('quarter-selector');
            filterTablesByQuarter(selectedQuarter);
            displayCurrentQuarterScore(selectedQuarter);
        });

        // Initial display: Show "All Result" on page load
        filterTablesByQuarter('all');
        displayCurrentQuarterScore('all');
    });
</script>