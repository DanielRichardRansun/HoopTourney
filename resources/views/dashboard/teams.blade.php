@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
    }
    .team-card {
        border: 2px solid #1e3c72;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        text-align: center;
        transition: 0.3s;
        cursor: pointer;
    }
    .team-card:hover {
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        transform: scale(1.03);
    }
    .team-title {
        font-weight: bold;
        background-color:  #1e3c72;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .team-info {
        font-size: 1.1em;
        margin-bottom: 5px;
    }
</style>

<div class="container mt-5">
    <h2 class="text-center mb-4">Daftar Tim {{ $tournament->name }}</h2>
    
    <div class="row">
        @foreach($teams as $index => $team)
            <div class="col-md-4">
                <div class="team-card" onclick="window.location='{{ route('teams.show', ['tournament_id' => $tournament->id, 'id' => $team->id]) }}'">
                    <div class="team-title">TEAM {{ $index + 1 }}</div>
                    <p class="team-info"><strong>Name:</strong> {{ $team->name }}</p>
                    <p class="team-info"><strong>Coach:</strong> {{ $team->coach }}</p>
                    <p class="team-info"><strong>Manager:</strong> {{ $team->manager }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if($teams->isEmpty())
    <div class="alert alert-warning text-center"> No teams available in this tournament.</div>
    @endif
</div>
@endsection
