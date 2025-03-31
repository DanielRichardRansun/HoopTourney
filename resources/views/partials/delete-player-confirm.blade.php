<div id="deleteConfirmForm" class="floating-form">
    <div class="form-header">
        <h4>Konfirmasi Hapus Pemain</h4>
        <button class="close-btn" onclick="closeAllForms()">&times;</button>
    </div>
    <p>Anda yakin ingin menghapus pemain <strong id="playerToDelete"></strong>?</p>
    <form id="deletePlayerForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" id="delete_player_id" name="id">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary mr-2" onclick="closeAllForms()">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
    </form>
</div>

<script>
    // Set form action berdasarkan ID pemain
    document.getElementById('deleteConfirmForm').addEventListener('show', function() {
        const playerId = document.getElementById('delete_player_id').value;
        document.getElementById('deletePlayerForm').action = `/players/${playerId}`;
    });
</script>
<script>
    // Fungsi untuk menampilkan form delete dan mengatur action
    function showDeleteConfirm(playerId, playerName) {
        document.getElementById('delete_player_id').value = playerId;
        document.getElementById('playerToDelete').textContent = playerName;
        
        // Set form action
        document.getElementById('deletePlayerForm').action = `/players/${playerId}`;
        
        // Tampilkan form
        document.getElementById('deleteConfirmForm').style.display = 'block';
    }
</script>