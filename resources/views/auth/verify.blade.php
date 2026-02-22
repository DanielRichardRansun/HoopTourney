@extends('layouts.general')

@section('content')
<main class="flex-grow bg-[#181411] min-h-screen flex items-center justify-center font-['Lexend'] py-20 px-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#221914] to-[#181411] z-0"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay z-0"></div>
    <div class="absolute -top-32 -left-32 size-96 bg-[#f48c25] rounded-full blur-[150px] opacity-10 pointer-events-none z-0"></div>

    <div class="glass-panel p-8 md:p-12 rounded-3xl border border-[#393028] w-full max-w-[500px] relative z-10 shadow-2xl text-center">
        <div class="size-16 rounded-full bg-[#f48c25]/10 border border-[#f48c25]/30 flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-4xl text-[#f48c25]">mark_email_read</span>
        </div>
        <h3 class="text-3xl font-black text-white italic uppercase tracking-tight mb-2">
            Verify <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f48c25] to-orange-300">Email</span>
        </h3>
        
        @if (session('resent'))
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 font-medium text-sm flex items-start justify-center gap-3">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <p>{{ __('A fresh verification link has been sent to your email address.') }}</p>
            </div>
        @endif

        <div class="text-slate-400 text-sm space-y-4 mb-8">
            <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
            <p>{{ __('If you did not receive the email') }},</p>
        </div>

        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="w-full py-3.5 bg-[#f48c25] text-[#181411] rounded-xl font-black uppercase tracking-wider transition-all hover:bg-orange-400 hover:shadow-[0_10px_20px_-10px_rgba(244,140,37,0.5)] flex items-center justify-center gap-2">
                {{ __('Click here to request another') }}
                <span class="material-symbols-outlined text-[20px]">send</span>
            </button>
        </form>
    </div>
</main>
@endsection
