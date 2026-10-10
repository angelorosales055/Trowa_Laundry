@extends('layouts_app')

@section('content')
<div class="mx-auto max-w-2xl py-6 sm:py-10">

    <!-- Brand Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 rounded-full border-2 border-[#182830] bg-[#FFFDF8] px-4 py-1.5 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B] shadow-[2px_2px_0px_#182830] mb-3">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Customer Self-Service Membership</span>
        </div>
        <h1 class="font-recoleta text-3xl sm:text-4xl font-extrabold tracking-tight text-[#182830]">
            Create Customer Account
        </h1>
        <p class="mt-2 text-xs sm:text-sm font-medium text-[#25799B] max-w-md mx-auto">
            Register your customer profile to track your laundry cycles live, receive pickup notifications, and submit quick self-service laundry intakes!
        </p>
    </div>

    <!-- Registration Card -->
    <div class="rounded-3xl border-3 border-[#182830] bg-[#FFFDF8] p-6 sm:p-8 shadow-[8px_8px_0px_#182830]">
        
        <!-- Header Banner -->
        <div class="flex items-center justify-between border-b-2 border-[#182830] bg-[#25799B] -mx-6 sm:-mx-8 -mt-6 sm:-mt-8 mb-6 p-4 text-[#FFFDF8] rounded-t-[21px]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#FFFDF8] text-[#25799B] shadow-[2px_2px_0px_#182830]">
                    <svg class="h-6 w-6 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-recoleta text-lg font-black text-[#F7E6CB] leading-tight">Customer Registration</h2>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-[#A2C5D8]">Email Verification & Profile Dossier</p>
                </div>
            </div>
            <span class="rounded-lg border border-[#182830] bg-[#CB1B03] px-2.5 py-1 font-mono text-[10px] font-bold text-white uppercase shadow-[1px_1px_0px_#182830]">
                Step 1 of 2
            </span>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border-2 border-[#182830] bg-[#CB1B03] p-4 text-xs font-bold text-white shadow-[3px_3px_0px_#182830]">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-1 font-mono text-[11px]">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.register.post') }}" id="customer-register-form" class="space-y-4">
            @csrf

            <!-- Name Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        First Name <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Maria" class="field text-sm font-medium" autofocus>
                </div>
                <div>
                    <label for="last_name" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Last Name <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Santos" class="field text-sm font-medium">
                </div>
            </div>

            <!-- Email & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Email Address <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="maria@example.com" class="field text-sm font-mono">
                    <span class="block text-[10px] text-slate-500 font-mono mt-1">We will send a 6-digit verification code here</span>
                </div>
                <div>
                    <label for="contact_number" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Contact Phone Number <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="tel" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required placeholder="0917XXXXXXX" class="field text-sm font-mono">
                    <span class="block text-[10px] text-slate-500 font-mono mt-1">For order status SMS and branch queries</span>
                </div>
            </div>

            <!-- Complete Address -->
            <div>
                <label for="address" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                    Complete Address <span class="text-[#CB1B03]">*</span>
                </label>
                <textarea name="address" id="address" rows="2" required placeholder="House / Unit #, Street, Barangay, City or Landmarks" class="field text-sm font-medium">{{ old('address') }}</textarea>
                <span class="block text-[10px] text-slate-500 font-mono mt-1">Auto-fills your pickup and delivery records for all future orders</span>
            </div>

            <!-- Passwords Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Password <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="password" name="password" id="password" required placeholder="At least 8 characters" class="field text-sm">
                </div>
                <div>
                    <label for="password_confirmation" class="block font-mono text-xs font-bold uppercase text-[#182830] mb-1">
                        Confirm Password <span class="text-[#CB1B03]">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Re-type password" class="field text-sm">
                </div>
            </div>

            <!-- Verification Notice Banner -->
            <div class="rounded-2xl border-2 border-[#182830] bg-[#F7E6CB]/60 p-3.5 text-xs text-[#182830]">
                <div class="flex items-start gap-2.5">
                    <svg class="h-5 w-5 text-[#25799B] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <div class="space-y-0.5">
                        <strong class="font-sans font-bold text-[#182830] block">Instant 6-Digit Email Code:</strong>
                        <p class="text-[11px] text-slate-600 font-medium">After submitting, check your email for your 6-digit confirmation key to instantly access your customer dashboard.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" id="btn-submit-register" class="w-full rounded-2xl border-3 border-[#182830] bg-[#CB1B03] py-3.5 px-5 font-recoleta text-base font-black text-white shadow-[4px_4px_0px_#182830] hover:bg-[#B51702] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 transition cursor-pointer flex items-center justify-center gap-2">
                    <span>Create Account & Send Verification Code ➔</span>
                </button>
            </div>
        </form>

        <!-- Footer Links -->
        <div class="mt-6 pt-4 border-t-2 border-dashed border-[#182830]/20 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-mono">
            <span class="text-slate-600">
                Already registered?
                <a href="{{ route('customer.login') }}" class="font-bold text-[#25799B] hover:text-[#CB1B03] underline ml-1">
                    Sign In to Portal
                </a>
            </span>
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-[#182830] font-semibold">
                Staff Counter Terminal ➔
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.getElementById('customer-register-form')?.addEventListener('submit', function() {
    if (window.TrowaLoading) {
        window.TrowaLoading.show(
            'Creating Customer Dossier...',
            'Registering account profile and sending your 6-digit verification code'
        );
    }
});
</script>
@endpush
@endsection
