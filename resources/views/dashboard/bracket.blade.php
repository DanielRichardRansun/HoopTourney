@extends('layouts.app2')

@section('content')
<div class="container">
    <h2 class="text-center">Tournament Bracket {{ $tournament->name }}</h2>

    @if(session('message'))
        <div class="alert alert-warning text-center">{{ session('message') }}</div>
    @elseif(empty($bracket))
        <div class="alert alert-warning text-center"> No teams available in this tournament.</div>
    @else
        @php
            $roundCount = count($bracket) + 1;
            $roundLabels = [];
            $stages = ['Final', 'Semi Final', 'Quarter Final', 'Penyisihan'];

            for ($i = 0; $i < $roundCount; $i++) {
                $roundLabels[$roundCount - $i - 1] = $stages[$i] ?? 'Penyisihan';
            }

            $gameCounter = 1;
        @endphp

        <div class="bracket-container">
            <div class="bracket">
                @foreach ($bracket as $index => $round)
                    <div class="round">
                        <div class="round-title">{{ $roundLabels[$index] ?? 'Round ' . ($index + 1) }}</div>
                        <div class="matchups">
                            @foreach ($round as $matchup)
                            @if ($matchup[0]['name'] !== '-' && $matchup[1]['name'] !== '-')
                            <div class="matchup" 
                                 onclick="window.location='{{ route('matchResults.show', ['id_tournament' => $tournament->id, 'id_schedule' => $matchup[2]]) }}'" 
                                 style="cursor: pointer;">
                                <div><strong>Game {{ $gameCounter }}</strong></div>
                                <div class="team {{ $matchup[0]['is_winner'] ? 'winner' : '' }} {{ $matchup[0]['name'] === 'TBD' ? 'tbd' : '' }}">
                                    {{ $matchup[0]['name'] }}
                                </div>
                                <div class="team {{ $matchup[1]['is_winner'] ? 'winner' : '' }} {{ $matchup[1]['name'] === 'TBD' ? 'tbd' : '' }}">
                                    {{ $matchup[1]['name'] }}
                                </div>                                
                            </div>
                            @php $gameCounter++; @endphp
                        @endif                        
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="round">
                    <div class="round-title">{{ $roundLabels[$roundCount] ?? 'Champion' }}</div>
                    <div class="matchups">
                        <div class="matchup champion">
                            <div>Winner Final</div>
                            <div class="team">{{ $champion }}</div>
                        </div>
                    </div>
                </div>                
        </div>
    @endif
</div>

<style>
    .container {
        max-width: 100%;
    }

    .bracket-container {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px;
    }

    .bracket {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
    }

    .round {
        min-width: 200px;
        margin: 0 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .round-title {
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
        background-color: #1e3c72;
        color: white;
        padding: 5px;
        border-radius: 8px;
        width: 100%;
    }

    .matchups {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
        height: 100%;
    }

    .matchup {
        background-color: #f0f0f0;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 8px;
        text-align: center;
        outline: 2px solid #999;
        cursor: pointer;
    }

    .matchup:hover {
    /* background-color: #ffffff; */
    transform: scale(1.02); 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .team {
        padding: 5px 10px;
        border: 1px solid #000;
        width: 150px;
        text-align: center;
        background-color: #f8f9fa;
        margin: 10px auto;
    }

    .champion {
        background-color: gold;
        font-weight: bold;
    }
    .team.winner {
    background-color: #95e197;
    color: rgb(0, 0, 0);
    font-weight: bold;
    padding: 5px;
}
.team.tbd {
    background-color: #d3d3d3;
    color: #000000;
    font-style: italic;
}
</style>
@endsection
