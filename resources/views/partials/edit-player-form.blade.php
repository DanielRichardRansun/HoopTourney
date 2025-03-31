<div id="editPlayerForm" class="floating-form">
    <div class="form-header">
        <h4>Edit Pemain</h4>
        <button class="close-btn" onclick="closeAllForms()">&times;</button>
    </div>
    <form id="editPlayerFormAction" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_player_id" name="id">
        <div class="form-group">
            <label for="edit_player_name">Nama Pemain</label>
            <input type="text" class="form-control" id="edit_player_name" name="name" required>
        </div>
        <div class="form-group">
            <label for="edit_jersey_number">Nomor Punggung</label>
            <input type="number" class="form-control" id="edit_jersey_number" name="jersey_number" required>
        </div>
        <div class="form-group">
            <label for="edit_position">Posisi</label>
            <input type="text" class="form-control" id="edit_position" name="position" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script>
    // Fungsi untuk menampilkan form edit
    function showEditForm(playerId, name, jerseyNumber, position) {
        // Isi data ke form
        document.getElementById('edit_player_id').value = playerId;
        document.getElementById('edit_player_name').value = name;
        document.getElementById('edit_jersey_number').value = jerseyNumber;
        document.getElementById('edit_position').value = position;
        
        // Set action form
        document.getElementById('editPlayerFormAction').action = `/players/${playerId}`;
        
        // Tampilkan form
        document.getElementById('editPlayerForm').style.display = 'block';
    }
</script>
