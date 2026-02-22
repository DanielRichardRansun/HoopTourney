<div id="editTeamForm" class="floating-form">
    <div class="form-header">
        <h4>Edit Tim</h4>
        <button class="close-btn" onclick="closeAllForms()">&times;</button>
    </div>
    <form action="{{ route('teams.update', $team->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nama Tim</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $team->name }}" required>
        </div>
        <div class="form-group">
            <label for="coach">Pelatih</label>
            <input type="text" class="form-control" id="coach" name="coach" value="{{ $team->coach }}" required>
        </div>
        <div class="form-group">
            <label for="manager">Manager</label>
            <input type="text" class="form-control" id="manager" name="manager" value="{{ $team->manager }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>