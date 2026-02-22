@extends('layouts.app2')

@section('title', 'Edit Turnamen')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white italic uppercase tracking-tight">Edit Tournament</h1>
        <p class="text-slate-400 text-sm mt-1">Update details for <span class="text-primary font-bold">{{ $tournament->name }}</span></p>
    </div>

    <!-- Form Card -->
    <div class="glass-panel border border-[#393028] rounded-2xl relative overflow-hidden">
        
        <!-- Subtle background glow -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="p-6 md:p-10 relative z-10">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-500 shadow-sm">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        Please fix the following errors:
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tournament.update', $tournament->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- General Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tournament Name -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Turnamen</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">emoji_events</span>
                            <input type="text" name="name" id="name" value="{{ old('name', $tournament->name) }}" required
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none">
                        </div>
                    </div>

                    <!-- Organizer -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="organizer" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Penyelenggara</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">corporate_fare</span>
                            <input type="text" name="organizer" id="organizer" value="{{ old('organizer', $tournament->organizer) }}" required
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="description" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Deskripsi</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-4 text-slate-500 text-[20px]">description</span>
                            <textarea name="description" id="description" rows="4"
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none resize-y min-h-[100px]">{{ old('description', $tournament->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="space-y-2">
                        <label for="start_date" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">event</span>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $tournament->start_date) }}" required
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none [color-scheme:dark]">
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="space-y-2">
                        <label for="end_date" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">event_available</span>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $tournament->end_date) }}" required
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none [color-scheme:dark]">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="status" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Status Turnamen</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">flag</span>
                            <select name="status" id="status" required
                                class="w-full bg-[#181411] border border-[#393028] text-white rounded-xl py-3 pl-12 pr-10 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors hover:border-slate-600 outline-none appearance-none cursor-pointer">
                                <option value="upcoming" {{ (old('status', $tournament->status) == 'upcoming') ? 'selected' : '' }} class="bg-[#1c1613]">Upcoming</option>
                                <option value="scheduled" {{ (old('status', $tournament->status) == 'scheduled') ? 'selected' : '' }} class="bg-[#1c1613]">Scheduled</option>
                                <option value="ongoing" {{ (old('status', $tournament->status) == 'ongoing') ? 'selected' : '' }} class="bg-[#1c1613]">Ongoing</option>
                                <option value="completed" {{ (old('status', $tournament->status) == 'completed') ? 'selected' : '' }} class="bg-[#1c1613]">Completed</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">expand_more</span>
                        </div>
                        <p class="text-xs text-amber-500 mt-2 font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Changing status to 'Scheduled' unlocks bracket generation.
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-6 mt-6 border-t border-[#393028] flex items-center justify-end gap-4">
                    <a href="{{ route('tournament.detail', $tournament->id) }}" class="px-6 py-3 rounded-xl font-bold text-sm text-slate-400 hover:text-white hover:bg-[#221914] transition-colors border border-transparent hover:border-[#393028]">
                        Cancel
                    </a>
                    <button type="submit" class="flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-primary to-orange-600 hover:to-primary text-white font-bold text-sm tracking-wide shadow-[0_4px_15px_-5px_rgba(244,140,37,0.5)] hover:scale-105 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
