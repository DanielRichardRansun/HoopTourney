@extends('layouts.app')

@section('content')
<style>
    .team-card {
        border: 2px solid #1e3c72;
        border-radius: 10px;
        padding: 20px;
        background-color: #f8f9fa;
        text-align: center;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
    .team-title {
        font-weight: bold;
        background-color: #1e3c72;
        color: white;
        padding: 12px;
        border-radius: 5px;
        font-size: 1.5em;
        margin-bottom: 15px;
    }
    .team-info {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .player-table {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .table thead {
        background-color: #1e3c72 !important;
        color: white;
    }
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        padding: 12px;
    }
    .table thead th {
    background-color: #1e3c72 !important;
    color: white !important;


    .floating-form {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        z-index: 1000;
        width: 400px;
        max-width: 90%;
    }
    
    .form-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
    
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
}
</style>

<div class="container mt-5">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <!-- Floating Forms (hidden by default) -->
    @include('partials.edit-team-form')
    @include('partials.add-player-form')
    @include('partials.edit-player-form')
    @include('partials.delete-player-confirm')

    <!-- Informasi Tim -->
    <div class="team-card">
        <div class="team-title">Informasi Tim</div>
        <p class="team-info"><strong>Nama:</strong> {{ $team->name }}</p>
        <p class="team-info"><strong>Coach:</strong> {{ $team->coach }}</p>
        <p class="team-info"><strong>Manager:</strong> {{ $team->manager }}</p>
        <button class="btn btn-warning btn-edit-team" onclick="showEditTeamForm()">Edit Tim</button>
    </div>

    <!-- Daftar Pemain -->
    <div class="d-flex justify-content-between align-items-center mt-5">
        <h3 class="text-center">Daftar Pemain</h3>
        <button class="btn btn-success" onclick="showAddPlayerForm()">Tambah Pemain</button>
    </div>
    
    <div class="table-responsive player-table mt-3">
        <table id="playerTable" class="table table-bordered">
            <thead> 
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>No. Punggung</th>
                    <th>Posisi</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $index => $player)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $player->name }}</td>
                        <td>{{ $player->jersey_number }}</td>
                        <td>{{ $player->position }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="showEditForm({{ $player->id }}, '{{ $player->name }}', {{ $player->jersey_number }}, '{{ $player->position }}')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="showDeleteConfirm({{ $player->id }}, '{{ $player->name }}')">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($players->isEmpty())
        <p class="text-center text-muted mt-4">Belum ada pemain dalam tim ini.</p>
    @endif
</div>

<style>
    .floating-form {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        z-index: 1000;
        width: 400px;
        max-width: 90%;
    }
    
    .form-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
    
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>

<!-- JavaScript untuk mengontrol form -->
<script>
    // Fungsi untuk menampilkan form edit tim
    function showEditTeamForm() {
        document.getElementById('editTeamForm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }
    
    // Fungsi untuk menampilkan form tambah pemain
    function showAddPlayerForm() {
        document.getElementById('addPlayerForm').style.display = 'block';
        document.getElementById('formOverlay').style.display = 'block';
    }
    
    // Handle form edit player submission
const editForm = document.getElementById('editPlayerFormAction');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'PUT',
                name: document.getElementById('edit_player_name').value,
                jersey_number: document.getElementById('edit_jersey_number').value,
                position: document.getElementById('edit_position').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate data');
        });
    });
}

// Handle form delete player submission
const deleteForm = document.getElementById('deletePlayerForm');
if (deleteForm) {
    deleteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    });
}
    
    // Fungsi untuk menutup semua form
    function closeAllForms() {
        document.querySelectorAll('.floating-form').forEach(form => {
            form.style.display = 'none';
        });
        document.getElementById('formOverlay').style.display = 'none';
    }
    
    // Event listener untuk form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Tutup form ketika mengklik overlay
        document.getElementById('formOverlay').addEventListener('click', closeAllForms);
        
        // Handle form edit player submission
        const editForm = document.getElementById('editPlayerFormAction');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const playerId = form.dataset.playerId; // Pastikan ada data attribute ini di form
        
        fetch(`/players/${playerId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT' // Gunakan ini untuk override method
            },
            body: formData
        })
        .then(handleResponse)
        .catch(handleError);
    });
}
        
        // Handle form delete player submission
        const deleteForm = document.getElementById('deletePlayerForm');
if (deleteForm) {
    deleteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const playerId = form.dataset.playerId; // Pastikan ada data attribute ini di form
        
        fetch(`/players/${playerId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'DELETE'
            }
        })
        .then(handleResponse)
        .catch(handleError);
    });
}
        
        // Tutup form ketika tombol close diklik
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', closeAllForms);
        });
    });
</script>

<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#playerTable').DataTable();
    });
</script>
@endsection
