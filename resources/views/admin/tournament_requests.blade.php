@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm" style="border-color: #1e3c72;">
        <div class="card-header" style="background-color: #1e3c72; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Tournament Join Requests</h3>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="statusFilter" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusFilter">
                        <li><a class="dropdown-item" href="?status=all">All Requests</a></li>
                        <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                        <li><a class="dropdown-item" href="?status=approved">Approved</a></li>
                        <li><a class="dropdown-item" href="?status=rejected">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>Player</th>
                            <th>Team</th>
                            <th>Tournament</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
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