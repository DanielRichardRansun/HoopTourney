<div id="addPlayerForm" class="floating-form">
    <div class="form-header">
        <h4>Tambah Pemain Baru</h4>
        <button class="close-btn" onclick="closeAllForms()">&times;</button>
    </div>
    <form action="{{ route('players.store') }}" method="POST">
        @csrf
        <input type="hidden" name="team_id" value="{{ $team->id }}">
        <div class="form-group">
            <label for="player_name">Nama Pemain</label>
            <input type="text" class="form-control" id="player_name" name="name" required>
        </div>
        <div class="form-group">
            <label for="jersey_number">Nomor Punggung</label>
            <input type="number" class="form-control" id="jersey_number" name="jersey_number" required>
        </div>
        <div class="form-group">
            <label for="position">Posisi</label>
            <input type="text" class="form-control" id="position" name="position" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Pemain</button>
    </form>
</div>