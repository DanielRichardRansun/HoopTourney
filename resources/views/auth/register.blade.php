@extends('layouts.app')
@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 15px;">
        <div class="text-center mb-3">
            <h3 class="fw-bold text-primary">Register</h3>
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="text-center">
                <label class="form-label">Register as</label>
                <div class="d-flex justify-content-center">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="role" id="admin" value="1" required checked>
                        <label class="form-check-label" for="admin">Organizer</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="role" id="team" value="2" required>
                        <label class="form-check-label" for="team">Team</label>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div id="teamFields" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Nama Tim</label>
                    <input type="text" name="team_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pelatih</label>
                    <input type="text" name="coach" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Manajer</label>
                    <input type="text" name="manager" class="form-control">
                </div>
            </div>
            
            <script>
                document.querySelectorAll('input[name="role"]').forEach((radio) => {
                    radio.addEventListener('change', function () {
                        document.getElementById('teamFields').style.display = (this.value == 2) ? 'block' : 'none';
                    });
                });
            </script>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-decoration-none">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</div>
@endsection