@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen flex items-center justify-center font-['Lexend'] py-20 px-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#221914] to-[#181411] z-0"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay z-0"></div>
    <div class="absolute -top-32 -left-32 size-96 bg-[#f48c25] rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>
    <div class="absolute -bottom-32 -right-32 size-96 bg-orange-600 rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>

    <div class="glass-panel p-8 md:p-12 rounded-3xl border border-[#393028] w-full max-w-[500px] relative z-10 shadow-2xl">
        <div class="text-center mb-10">
            <div class="flex items-center justify-center mx-auto mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="HoopTourney Logo" class="h-24 object-contain drop-shadow-[0_0_15px_rgba(244,140,37,0.4)]">
            </div>
            <h3 class="text-3xl font-black text-white italic uppercase tracking-tight">
                Create <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Account</span>
            </h3>
            <p class="text-slate-400 mt-2 text-sm">Join the platform to organize or participate in tournaments.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Role Selection -->
            <div class="text-center mb-8">
                <label class="block text-slate-300 text-sm font-bold mb-3 uppercase tracking-wide">Register As</label>
                <div class="flex justify-center gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="1" class="peer sr-only" required checked>
                        <div class="px-6 py-2.5 rounded-xl border border-[#393028] bg-[#221914] text-slate-400 font-bold uppercase tracking-wider text-sm transition-all peer-checked:border-[#f48c25] peer-checked:text-[#f48c25] peer-checked:bg-[#f48c25]/10 hover:border-slate-500">
                            Organizer
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="2" class="peer sr-only" required>
                        <div class="px-6 py-2.5 rounded-xl border border-[#393028] bg-[#221914] text-slate-400 font-bold uppercase tracking-wider text-sm transition-all peer-checked:border-[#f48c25] peer-checked:text-[#f48c25] peer-checked:bg-[#f48c25]/10 hover:border-slate-500">
                            Team
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Name -->
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Full Name</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">person</span>
                    <input type="text" name="name" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('name') border-red-500 @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                </div>
                @error('name')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">mail</span>
                    <input type="email" name="email" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('email') border-red-500 @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Team Specific Fields -->
            <div id="teamFields" style="display: none;" class="space-y-6 pt-4 border-t border-[#393028]">
                <div class="flex items-center gap-2 text-[#f48c25] font-bold text-sm uppercase tracking-wide mb-2 mt-2">
                    <span class="material-symbols-outlined text-[18px]">groups</span> Team Details
                </div>
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Team Name</label>
                    <input type="text" name="team_name" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="e.g. Lakers">
                </div>
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Coach Name</label>
                    <input type="text" name="coach" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="Name of Head Coach">
                </div>
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Manager Name</label>
                    <input type="text" name="manager" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl px-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="Name of Team Manager">
                </div>
            </div>
            
            <!-- Passwords -->
            <div class="pt-2">
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">lock</span>
                    <input type="password" name="password" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('password') border-red-500 @enderror" placeholder="Create a secure password" required>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Confirm Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">lock_reset</span>
                    <input type="password" name="password_confirmation" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600" placeholder="Repeat your password" required>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-[#f48c25] text-[#181411] rounded-xl font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                    Create Account
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </button>
            </div>
            
            <!-- Footer links -->
            <div class="text-center pt-6 border-t border-[#393028] mt-6">
                <p class="text-slate-400 text-sm">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#f48c25] font-bold hover:text-orange-400 transition-colors">Sign in</a>
                </p>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="role"]').forEach((radio) => {
            radio.addEventListener('change', function () {
                const teamFields = document.getElementById('teamFields');
                if(this.value == 2) {
                    teamFields.style.display = 'block';
                    // Optional: add a tiny fade-in effect
                    teamFields.style.opacity = '0';
                    setTimeout(() => teamFields.style.opacity = '1', 10);
                } else {
                    teamFields.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
@endsection