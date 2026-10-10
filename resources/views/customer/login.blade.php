@extends('layouts_app')

@section('content')
<div class="mx-auto max-w-md py-8 sm:py-14">

    <!-- Brand Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 rounded-full border-2 border-[#182830] bg-[#FFFDF8] px-4 py-1.5 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B] shadow-[2px_2px_0px_#182830] mb-3">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Customer Self-Service Terminal</span>
        </div>
        <h1 class="font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
            Customer Sign In
        </h1>
        <p class="mt-2 text-xs sm:text-sm font-medium text-[#25799B] max-w-sm mx-auto">
            Log in to view live wash cycles, track laundry pickups, and submit self-service laundry intakes.
        </p>
    </div>

    <!-- Login Card -->
    <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 sm:p-8 shadow-[8px_8px_0px_#182830]">
        
        <!-- Header Banner -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] -mx-6 sm:-mx-8 -mt-6 sm:-mt-8 mb-6 p-4 text-[#FFFDF8] rounded-t-[21px]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-lg font-black text-[#F7E6CB] leading-tight">Access Portal</h2>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]">Order Tracker & Requests</p>
                </div>
            </div>
            <a href="{{ route('customer.register') }}" class="rounded-lg border border-[#182830] bg-[#CB1B03] px-2.5 py-1 font-mono text-[10px] font-bold text-white uppercase shadow-[1px_1px_0px_#182830] hover:bg-[#B51702] transition">
                Register New ➔
            </a>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-2xl border-2 border-emerald-600 bg-emerald-50 p-3 text-xs font-bold text-emerald-900 shadow-[2px_2px_0px_#182830]">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 flex items-center gap-2.5 rounded-xl border-2 border-[#182830] bg-[#CB1B03] p-3 text-xs font-bold text-white shadow-[2px_2px_0px_#182830]">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login.post') }}" id="customer-login-form" class="space-y-4">
            @csrf

            <div>
                <label for="login-input" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Email Address or Username
                </label>
                <input type="text" 
                       id="login-input" 
                       name="login" 
                       value="{{ old('login') }}" 
                       required 
                       placeholder="e.g. maria@example.com" 
                       class="field text-sm font-medium" 
                       autofocus>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password-input" class="block font-mono text-xs font-bold uppercase text-[#182830]">
                        Password
                    </label>
                </div>
                <input type="password" 
                       id="password-input" 
                       name="password" 
                       required 
                       placeholder="Enter your password" 
                       class="field text-sm">
            </div>

            <div class="flex items-center justify-between text-xs font-mono">
                <label class="flex items-center gap-2 text-slate-700 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#182830] text-[#CB1B03] focus:ring-0">
                    <span>Remember me</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" id="btn-submit-cust-login" class="w-full rounded-2xl border-3 border-[#182830] bg-[#CB1B03] py-3.5 px-5 font-recoleta text-base font-black text-white shadow-[4px_4px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center justify-center gap-2">
                    <span>Open My Customer Portal ➔</span>
                </button>
            </div>
        </form>

        <!-- Footer Links -->
        <div class="mt-6 pt-4 border-t-2 border-dashed border-[#182830]/20 flex flex-col items-center gap-2 text-xs font-mono">
            <span class="text-slate-600">
                New to Trowa Laundry?
                <a href="{{ route('customer.register') }}" class="font-bold text-[#CB1B03] hover:underline ml-1">
                    Register an Account
                </a>
            </span>
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-[#182830] font-semibold mt-1">
                Staff & Admin Sign In ➔
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.getElementById('customer-login-form')?.addEventListener('submit', function() {
    if (window.TrowaLoading) {
        window.TrowaLoading.show(
            'Signing in to Customer Portal...',
            'Retrieving active wash orders, tickets, and transaction history'
        );
    }
});
</script>
@endpush
@endsection
