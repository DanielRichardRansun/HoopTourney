@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .section-title {
    font-size: 24px;
    text-align: center;
    background-color: #1e3c72;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.score-container {
    flex-direction: column;
    align-items: center;
    margin-bottom: 30px;
    background-color: #fff;
    border: 2px solid #1e3c72;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
}

.score-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
}

.score-container h4 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: bold;
}

.score-container label {
    font-size: 18px;
    font-weight: bold;
    color: #555;
}

.score-container input {
    width: 100px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
}
    .team-stats {
        border: 2px solid #1e3c72;
        margin-bottom: 40px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .team-stats h4 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }
    .team-stats h5 {
        font-weight: bold !important;
        text-align: center;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .player-stats {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f1f1f1;
        border-radius: 5px;
    }
    .player-stats label {
        width: 100%;
        font-size: 15px;
        font-weight: bold;
        color: #444;
        margin-bottom: 10px;
    }
    .player-stats input {
        width: 65px;
        /* margin-right: 10px; */
        margin-bottom: 10px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
    }
    .player-stats input::placeholder {
        color: #999;
    }
    .btn-submit {
        background-color: #1e3c72;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .btn-submit:hover {
        background-color: #0056b3;
    }
    .btn-submit:active {
        background-color: #004080;
    }
</style>    
<div class="container">
    <form action="{{ route('matchResults.update', ['id_tournament' => $tournament->id, 'id_schedule' => $schedule->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="score-container">
            <div class="section-title">Edit Hasil Pertandingan</div>
            <div class="score-content">
                <label>{{ $team1->name }}</label>
                <input type="number" name="team1_score" class="form-control" 
                       value="{{ old('team1_score', $matchResult->team1_score) }}" required min="0">
                <input type="number" name="team2_score" class="form-control" 
                       value="{{ old('team2_score', $matchResult->team2_score) }}" required min="0">
                <label>{{ $team2->name }}</label>
            </div>
        </div>        

        <div class="team-stats">
            <div class="section-title">Statistik Pemain</div>

            <h5>{{ $team1->name }}</h5>
            @foreach($players1 as $player)
            @php
                $stats = $playerStats[$player->id] ?? null;
            @endphp
            <div class="player-stats" style="display: flex; gap: 10px; align-items: center; text-align: center; flex-wrap: wrap;">
                <label style="display: flex; align-items: baseline;">{{ $player->name }}</label>
                <div style="display: flex; flex-direction: column;">
                    <span>Point</span>
                    <input type="number" name="player_stats[{{ $player->id }}][point]" 
                           value="{{ old("player_stats.$player->id.point", $stats->point ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGM</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fgm]" 
                           value="{{ old("player_stats.$player->id.fgm", $stats->fgm ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGA</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fga]" 
                           value="{{ old("player_stats.$player->id.fga", $stats->fga ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTA</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fta]" 
                           value="{{ old("player_stats.$player->id.fta", $stats->fta ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTM</span>
                    <input type="number" name="player_stats[{{ $player->id }}][ftm]" 
                           value="{{ old("player_stats.$player->id.ftm", $stats->ftm ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>ORB</span>
                    <input type="number" name="player_stats[{{ $player->id }}][orb]" 
                           value="{{ old("player_stats.$player->id.orb", $stats->orb ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>DRB</span>
                    <input type="number" name="player_stats[{{ $player->id }}][drb]" 
                           value="{{ old("player_stats.$player->id.drb", $stats->drb ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>STL</span>
                    <input type="number" name="player_stats[{{ $player->id }}][stl]" 
                           value="{{ old("player_stats.$player->id.stl", $stats->stl ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>AST</span>
                    <input type="number" name="player_stats[{{ $player->id }}][ast]" 
                           value="{{ old("player_stats.$player->id.ast", $stats->ast ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>BLK</span>
                    <input type="number" name="player_stats[{{ $player->id }}][blk]" 
                           value="{{ old("player_stats.$player->id.blk", $stats->blk ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>PF</span>
                    <input type="number" name="player_stats[{{ $player->id }}][pf]" 
                           value="{{ old("player_stats.$player->id.pf", $stats->pf ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>TO</span>
                    <input type="number" name="player_stats[{{ $player->id }}][to]" 
                           value="{{ old("player_stats.$player->id.to", $stats->to ?? 0) }}" min="0">
                </div>
            </div>
            @endforeach
        </div>

        <div class="team-stats">
            <h5>{{ $team2->name }}</h5>
            @foreach($players2 as $player)
            @php
                $stats = $playerStats[$player->id] ?? null;
            @endphp
            <div class="player-stats" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <label style="display: flex; align-items: baseline;">{{ $player->name }}</label>
                <div style="display: flex; flex-direction: column;">
                    <span>Point</span>
                    <input type="number" name="player_stats[{{ $player->id }}][point]" 
                           value="{{ old("player_stats.$player->id.point", $stats->point ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGM</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fgm]" 
                           value="{{ old("player_stats.$player->id.fgm", $stats->fgm ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FGA</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fga]" 
                           value="{{ old("player_stats.$player->id.fga", $stats->fga ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTA</span>
                    <input type="number" name="player_stats[{{ $player->id }}][fta]" 
                           value="{{ old("player_stats.$player->id.fta", $stats->fta ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>FTM</span>
                    <input type="number" name="player_stats[{{ $player->id }}][ftm]" 
                           value="{{ old("player_stats.$player->id.ftm", $stats->ftm ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>ORB</span>
                    <input type="number" name="player_stats[{{ $player->id }}][orb]" 
                           value="{{ old("player_stats.$player->id.orb", $stats->orb ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>DRB</span>
                    <input type="number" name="player_stats[{{ $player->id }}][drb]" 
                           value="{{ old("player_stats.$player->id.drb", $stats->drb ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>STL</span>
                    <input type="number" name="player_stats[{{ $player->id }}][stl]" 
                           value="{{ old("player_stats.$player->id.stl", $stats->stl ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>AST</span>
                    <input type="number" name="player_stats[{{ $player->id }}][ast]" 
                           value="{{ old("player_stats.$player->id.ast", $stats->ast ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>BLK</span>
                    <input type="number" name="player_stats[{{ $player->id }}][blk]" 
                           value="{{ old("player_stats.$player->id.blk", $stats->blk ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>PF</span>
                    <input type="number" name="player_stats[{{ $player->id }}][pf]" 
                           value="{{ old("player_stats.$player->id.pf", $stats->pf ?? 0) }}" min="0">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span>TO</span>
                    <input type="number" name="player_stats[{{ $player->id }}][to]" 
                           value="{{ old("player_stats.$player->id.to", $stats->to ?? 0) }}" min="0">
                </div>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn-submit">Simpan Perubahan</button>
    </form>
</div>
@endsection