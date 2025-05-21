@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm" style="border-color: #1e3c72;">
        <div class="card-header" style="background-color: #1e3c72; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">My Tournament Requests</h3>
                <div>
                    <div class="dropdown d-inline-block me-2">
                        <button class="btn btn-light dropdown-toggle" type="button" id="tournamentFilter" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(request('tournament', 'all') == 'all')
                                All My Tournaments
                            @else
                                {{ $tournaments->firstWhere('id', request('tournament'))->name ?? 'Select Tournament' }}
                            @endif
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="tournamentFilter">
                            <li><a class="dropdown-item" href="?tournament=all&status={{ request('status', 'all') }}">All My Tournaments</a></li>
                            @foreach($tournaments as $tournament)
                            <li><a class="dropdown-item" href="?tournament={{ $tournament->id }}&status={{ request('status', 'all') }}">{{ $tournament->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-light dropdown-toggle" type="button" id="statusFilter" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(request('status', 'all') == 'all')
                                All Statuses
                            @else
                                {{ ucfirst(request('status', 'all')) }}
                            @endif
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="statusFilter">
                            <li><a class="dropdown-item" href="?status=all&tournament={{ request('tournament', 'all') }}">All Requests</a></li>
                            <li><a class="dropdown-item" href="?status=pending&tournament={{ request('tournament', 'all') }}">Pending</a></li>
                            <li><a class="dropdown-item" href="?status=approved&tournament={{ request('tournament', 'all') }}">Approved</a></li>
                            <li><a class="dropdown-item" href="?status=rejected&tournament={{ request('tournament', 'all') }}">Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest of your view remains the same -->
        <div class="card-body">
            @if($tournaments->isEmpty())
                <div class="alert alert-info">
                    You haven't created any tournaments yet. Create a tournament to see join requests.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>Team</th>
                                <th>Tournament</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->team->name }}</td>
                                <td>{{ $request->tournament->name }}</td>
                                <td>
                                    <span class="badge 
                                        @if($request->status == 'pending') bg-warning text-dark
                                        @elseif($request->status == 'approved') bg-success
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($request->status == 'pending')
                                    <form action="{{ route('admin.tournament.approve', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.tournament.reject', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                    @else
                                    <span class="text-muted">Action completed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No requests found for your tournaments</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(30, 60, 114, 0.05);
    }
    .badge {
        padding: 0.5em 0.75em;
        font-size: 0.85em;
        font-weight: 500;
    }
</style>
@endsection