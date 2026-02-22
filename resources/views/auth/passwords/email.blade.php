@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen flex items-center justify-center font-['Lexend'] py-20 px-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#221914] to-[#181411] z-0"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay z-0"></div>
    <div class="absolute top-0 right-0 size-96 bg-[#f48c25] rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>

    <div class="glass-panel p-8 md:p-12 rounded-3xl border border-[#393028] w-full max-w-[450px] relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <div class="size-16 rounded-full bg-[#f48c25]/10 border border-[#f48c25]/30 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl text-[#f48c25]">key</span>
            </div>
            <h3 class="text-3xl font-black text-white italic uppercase tracking-tight">
                Reset <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Password</span>
            </h3>
            <p class="text-slate-400 mt-2 text-sm">Enter your email address and we will send you a link to reset your password.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 font-medium text-sm flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 uppercase tracking-wide">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-500 text-[20px]">mail</span>
                    <input type="email" name="email" class="w-full bg-[#221914] border border-[#393028] text-white rounded-xl pl-12 pr-4 py-3 outline-none focus:border-[#f48c25] transition-colors placeholder-slate-600 @error('email') border-red-500 @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-2 italic flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-[#f48c25] text-[#181411] rounded-xl font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                    Send Reset Link
                    <span class="material-symbols-outlined text-[20px]">send</span>
                </button>
            </div>

            <div class="text-center pt-6 border-t border-[#393028] mt-6">
                <a href="{{ route('login') }}" class="text-slate-400 text-sm hover:text-white transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
