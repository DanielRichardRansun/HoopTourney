@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen flex items-center justify-center font-['Lexend'] py-20 px-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#221914] to-[#181411] z-0"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay z-0"></div>
    <div class="absolute -top-32 -left-32 size-96 bg-[#f48c25] rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>
    <div class="absolute -bottom-32 -right-32 size-96 bg-orange-600 rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>

    <div class="glass-panel p-8 md:p-12 rounded-3xl border border-[#393028] w-full max-w-[450px] relative z-10 shadow-2xl">
        <div class="text-center mb-10">
            <div class="flex items-center justify-center mx-auto mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="HoopTourney Logo" class="h-24 object-contain drop-shadow-[0_0_15px_rgba(244,140,37,0.4)]">
            </div>
            <h3 class="text-3xl font-black text-white italic uppercase tracking-tight">
                Welcome <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Back</span>
            </h3>
            <p class="text-slate-400 mt-2 text-sm">Sign in to manage your tournaments and teams.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <!-- Email -->
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">mail</span>
                    <input type="email" name="email" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('email') border-red-500 @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">lock</span>
                    <input type="password" name="password" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-[#f48c25] text-[#181411] rounded-xl font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                    Sign In
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </div>

            <!-- Footer links -->
            <div class="text-center pt-6 border-t border-[#393028] mt-6 flex flex-col gap-3">
                <p class="text-slate-400 text-sm">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#f48c25] font-bold hover:text-orange-400 transition-colors">Register here</a>
                </p>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-slate-500 text-xs hover:text-slate-300 transition-colors">Forgot your password?</a>
                @endif
            </div>
        </form>
    </div>
</main>
@endsection