@extends('layouts_app')

@section('content')
<div class="mx-auto max-w-lg py-8 sm:py-14">

    <!-- Brand Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 rounded-full border-2 border-[#182830] bg-[#FFFDF8] px-4 py-1.5 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B] shadow-[2px_2px_0px_#182830] mb-3">
            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Security Verification</span>
        </div>
        <h1 class="font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
            Verify Your Email
        </h1>
        <p class="mt-2 text-xs sm:text-sm font-medium text-[#25799B] max-w-sm mx-auto">
            We have sent a 6-digit verification code to
            <strong class="font-bold text-[#182830]">{{ $user->email ?? 'your email' }}</strong>.
        </p>
    </div>

    <!-- Dev Code Quick Helper Banner (if available) -->
    @if(session('dev_code'))
        <div class="mb-6 rounded-2xl border-2 border-[#25799B] bg-[#A2C5D8]/30 p-4 text-[#182830] shadow-[3px_3px_0px_#25799B]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="block font-mono text-[10px] font-bold uppercase text-[#25799B]">Local Dev / Testing Code</span>
                    <strong class="font-mono text-2xl font-black text-[#CB1B03] tracking-widest">{{ session('dev_code') }}</strong>
                </div>
                <button type="button" id="btn-autofill-dev" class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] px-3 py-1.5 font-mono text-xs font-bold text-[#182830] shadow-[2px_2px_0px_#182830] hover:bg-[#F7E6CB] transition cursor-pointer">
                    Auto-fill Code ➔
                </button>
            </div>
        </div>
    @endif

    <!-- Verification Card -->
    <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 sm:p-8 shadow-[8px_8px_0px_#182830]">
        
        <!-- Header Banner -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] -mx-6 sm:-mx-8 -mt-6 sm:-mt-8 mb-6 p-4 text-[#FFFDF8] rounded-t-[21px]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-lg font-black text-[#F7E6CB] leading-tight">6-Digit Access Code</h2>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]">Customer Account Activation</p>
                </div>
            </div>
            <span class="rounded-lg border border-[#182830] bg-[#CB1B03] px-2.5 py-1 font-mono text-[10px] font-bold text-white uppercase shadow-[1px_1px_0px_#182830]">
                Step 2 of 2
            </span>
        </div>

        @if($errors->any())
            <div class="mb-5 flex items-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] p-3 text-xs font-bold text-white shadow-[2px_2px_0px_#182830]">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.verify.post') }}" id="verify-form" class="space-y-6">
            @csrf

            <div>
                <label for="verification-code-input" class="block font-mono text-xs font-bold uppercase text-[#182830] text-center mb-2">
                    Enter 6-Digit Verification Code
                </label>
                <div class="relative">
                    <input type="text" 
                           id="verification-code-input" 
                           name="code" 
                           maxlength="6" 
                           pattern="[0-9]{6}" 
                           inputmode="numeric" 
                           required 
                           autocomplete="one-time-code"
                           placeholder="••••••" 
                           class="w-full rounded-2xl border-3 border-[#182830] bg-[#FFFDF8] py-4 text-center font-mono text-3xl sm:text-4xl font-black tracking-[0.4em] text-[#182830] shadow-[3px_3px_0px_#182830] focus:border-[#25799B] focus:outline-none placeholder:text-slate-300" 
                           autofocus>
                </div>
                <p class="text-center font-mono text-[11px] text-slate-500 mt-2">
                    Check your inbox and spam folder for your Trowa Laundry code.
                </p>
            </div>

            <button type="submit" id="btn-submit-verify" class="w-full rounded-2xl border-3 border-[#182830] bg-[#CB1B03] py-3.5 px-5 font-recoleta text-base font-black text-white shadow-[4px_4px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center justify-center gap-2">
                <span>Verify & Open Customer Portal ➔</span>
            </button>
        </form>

        <!-- Resend Code Form -->
        <div class="mt-6 pt-5 border-t-2 border-dashed border-[#182830]/20 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-mono">
            <form method="POST" action="{{ route('customer.resend-code') }}">
                @csrf
                <button type="submit" class="font-bold text-[#25799B] hover:text-[#CB1B03] hover:underline cursor-pointer">
                    Didn't receive code? Resend Email ↺
                </button>
            </form>

            <a href="{{ route('customer.register') }}" class="text-slate-500 hover:text-[#182830]">
                Change Registration Email
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const codeInput = document.getElementById('verification-code-input');
    const autoFillBtn = document.getElementById('btn-autofill-dev');

    @if(session('dev_code'))
        autoFillBtn?.addEventListener('click', () => {
            if (codeInput) {
                codeInput.value = "{{ session('dev_code') }}";
                codeInput.focus();
            }
        });
    @endif

    document.getElementById('verify-form')?.addEventListener('submit', function() {
        if (window.TrowaLoading) {
            window.TrowaLoading.show(
                'Verifying Code...',
                'Validating your 6-digit key and initializing your customer dashboard'
            );
        }
    });
});
</script>
@endpush
@endsection
