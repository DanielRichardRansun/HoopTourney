@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px 0;
        }
        .table th {
            background-color: #1e3c72;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 2px solid #ffc107;
            transition: all 0.3s ease;
        }
        .btn-edit:hover {
            background-color: white;
            color: #ffc107;
            border-color: #ffc107;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 2px solid #dc3545;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            background-color: white;
            color: #dc3545;
            border-color: #dc3545;
        }
    </style>

    <div class="container">
        <div class="header text-center mb-3">
            <h2>Daftar Turnamen Saya</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @auth
        @if ($user->role == 1)
        <a href="{{ route('tournament.create') }}" class="btn text-white" style="background-color: #1e3c72; border-color: #1e3c72;">
            Buat Tourney
        </a>
        
        @endif
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
                        @if ($user->role == 1)
                            <th>Actions</th>
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

                            @if ($user->role == 1)
                            <td class="action-buttons">
                                <a href="{{ route('tournament.edit', $tournament->id) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('tournament.destroy', $tournament->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus turnamen ini?')">Delete</button>
                                </form>
                            </td>
                            @endif
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