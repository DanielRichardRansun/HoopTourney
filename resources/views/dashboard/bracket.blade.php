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
            $stages = ['Champion', 'Final', 'Semi Final', 'Quarter Final', 'Penyisihan'];

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
                                @if ($matchup[0] !== '-' && $matchup[1] !== '-')
                                    <div class="matchup">
                                        <div><strong>Game {{ $gameCounter }}</strong></div>
                                        <div class="team">{{ $matchup[0] }}</div>
                                        <div class="team">{{ $matchup[1] }}</div>
                                    </div>
                                    @php $gameCounter++; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="round">
                    <div class="round-title">{{ $roundLabels[$roundCount - 1] ?? 'Champion' }}</div>
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
        margin-bottom: 10px;
        border-radius: 8px;
        text-align: center;
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
</style>
@endsection
