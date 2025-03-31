@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg p-4" style="width: 50rem; border-radius: 15px;">
        <h2 class="text-center mb-4 text-primary">Buat Tournament</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tournament.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Tournament</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama turnamen" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">The Organizer</label>
                <input type="text" name="organizer" class="form-control" placeholder="Nama penyelenggara" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat turnamen" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg w-100">Buat Tournament</button>
            </div>
        </form>
    </div>
</div>
@endsection
