@extends('layouts.app2')

@section('content')
<style>
    .container {
        max-width: 100%;
    }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-white text-center" style="background: #1e3c72; font-weight: bold !important;">
                    <h3>Edit Turnamen</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tournament.update', $tournament->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Turnamen</label>
                            <input type="text" name="name" value="{{ $tournament->name }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="organizer" class="form-label">Penyelenggara</label>
                            <input type="text" name="organizer" value="{{ $tournament->organizer }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ $tournament->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ $tournament->start_date }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ $tournament->end_date }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="upcoming" {{ $tournament->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="scheduled" {{ $tournament->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="ongoing" {{ $tournament->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ $tournament->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
