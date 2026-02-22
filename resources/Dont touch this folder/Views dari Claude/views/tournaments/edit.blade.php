@extends('layouts.app2')

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-8">
    <div class="size-12 rounded-xl bg-yellow-500/10 border border-yellow-500/30 flex items-center justify-center">
        <span class="material-symbols-outlined text-yellow-500 text-2xl">edit</span>
    </div>
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">Edit Turnamen</h1>
        <p class="text-slate-400 text-sm">Update your tournament details</p>
    </div>
</div>

<div class="glass-panel rounded-2xl p-6 md:p-8 border border-[#393028] shadow-xl max-w-2xl">
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
                <p class="text-red-400 text-sm font-bold uppercase tracking-wider">Errors Found</p>
            </div>
            <ul class="list-disc list-inside text-red-400 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tournament.update', $tournament->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Nama Turnamen</label>
            <input type="text" name="name" value="{{ $tournament->name }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors placeholder-slate-600" required>
        </div>

        <div>
            <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Penyelenggara</label>
            <input type="text" name="organizer" value="{{ $tournament->organizer }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors placeholder-slate-600" required>
        </div>

        <div>
            <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors placeholder-slate-600 resize-none">{{ $tournament->description }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $tournament->start_date }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors [color-scheme:dark]" required>
            </div>
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $tournament->end_date }}" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors [color-scheme:dark]" required>
            </div>
        </div>

        <div>
            <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Status</label>
            <select name="status" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-primary transition-colors" required>
                <option value="upcoming" {{ $tournament->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="scheduled" {{ $tournament->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="ongoing" {{ $tournament->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ $tournament->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary to-orange-400 text-[#181411] rounded-xl font-black uppercase tracking-wider transition-all hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">save</span> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
