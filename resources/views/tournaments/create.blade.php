@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen text-slate-300 font-['Lexend'] pb-20 flex items-center justify-center pt-24 px-6">
    <div class="glass-panel p-8 md:p-12 rounded-2xl border border-[#393028] w-full max-w-3xl relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 size-48 bg-[#f48c25] rounded-full blur-[100px] opacity-20 pointer-events-none"></div>

        <div class="text-center mb-10 relative z-10">
            <h2 class="text-3xl md:text-4xl font-black text-white italic uppercase tracking-tight flex-col flex items-center gap-2">
                <span class="material-symbols-outlined text-4xl text-[#f48c25]">add_box</span>
                Create Tournament
            </h2>
            <p class="text-slate-400 mt-2 text-sm md:text-base">Host your own basketball competition on the platform.</p>
        </div>
        
        @if ($errors->any())
            <div class="mb-8 p-4 rounded-xl border border-red-500/30 bg-red-500/10 text-red-400 font-medium text-sm z-10 relative">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    Please check the errors below:
                </div>
                <ul class="list-disc list-inside space-y-1 ml-1 text-slate-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tournament.store') }}" method="POST" class="relative z-10">
            @csrf

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Tournament Name</label>
                    <input type="text" name="name" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 pb-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="e.g. Summer Pro League 2026" required value="{{ old('name') }}">
                </div>

                <!-- Organizer -->
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">The Organizer</label>
                    <input type="text" name="organizer" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="Name of the host or organization" required value="{{ old('organizer') }}">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Description</label>
                    <textarea name="description" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 resize-y min-h-[120px]" placeholder="Brief description of the tournament rules and format..." required>{{ old('description') }}</textarea>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">event</span> Start Date
                        </label>
                        <input type="date" name="start_date" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors cursor-pointer" required value="{{ old('start_date') }}">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">event_available</span> End Date
                        </label>
                        <input type="date" name="end_date" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors cursor-pointer" required value="{{ old('end_date') }}">
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-[#393028] flex flex-col md:flex-row gap-4 items-center justify-between">
                <a href="{{ route('tournament.mine') }}" class="w-full md:w-auto text-center px-6 py-3 rounded-full border border-[#393028] text-slate-400 hover:text-white hover:bg-[#221914] transition-colors font-bold uppercase tracking-wider text-sm">
                    Cancel
                </a>
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#f48c25] text-[#181411] rounded-full font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Create Tournament
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
