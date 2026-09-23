@extends('layouts_app')

@section('content')
<div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-10 sm:px-6">
    <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-20 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>

    <div class="relative grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden bg-gradient-to-br from-brand-600 via-brand-500 to-cyan-500 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <div class="mb-16 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl shadow-lg">✦</span>
                    <span class="font-display text-lg font-bold">Trowa Laundry</span>
                </div>
                <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-brand-50">Laundry operations</p>
                <h1 class="max-w-sm font-display text-5xl font-bold leading-tight">Hello,<br>cleaner days.</h1>
                <p class="mt-5 max-w-sm text-sm leading-6 text-brand-50">Manage customers, schedules, services, and payments from one simple workspace.</p>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center text-xs">
                <div class="rounded-2xl bg-white/15 p-3 backdrop-blur"><strong class="block text-xl">01</strong>Track orders</div>
                <div class="rounded-2xl bg-white/15 p-3 backdrop-blur"><strong class="block text-xl">02</strong>Manage staff</div>
                <div class="rounded-2xl bg-white/15 p-3 backdrop-blur"><strong class="block text-xl">03</strong>Grow smarter</div>
            </div>
        </section>

        <section class="bg-white p-7 sm:p-10">
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 text-xl text-white">✦</span>
                <div><p class="font-display text-lg font-bold text-slate-900">Trowa Laundry</p><p class="text-xs text-slate-500">Laundry operations made simple.</p></div>
            </div>
            <div class="mb-7">
                <p class="mb-2 text-sm font-semibold text-brand-600">Welcome back</p>
                <h2 class="font-display text-3xl font-bold text-slate-900">Sign in to your workspace</h2>
                <p class="mt-2 text-sm text-slate-500">Sign in to manage your laundry shop.</p>
            </div>

            @if($errors->any())
                <div class="mb-5 flex gap-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <span>!</span><span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf
                <label class="block text-sm font-semibold text-slate-700">
                    Username
                    <input name="username" value="{{ old('username') }}" type="text" autocomplete="username" class="field mt-2 h-12" placeholder="Enter your username" required autofocus>
                </label>
                <label class="block text-sm font-semibold text-slate-700">
                    Password
                    <span class="relative mt-2 block">
                        <input id="login-password" name="password" type="password" autocomplete="current-password" class="field h-12 pr-20" placeholder="Enter your password" required>
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-400 hover:text-brand-600">Show</button>
                    </span>
                </label>
                <button class="btn-primary h-12 w-full text-base shadow-lg shadow-brand-500/20">Sign in <span class="ml-2">→</span></button>
            </form>

            <div class="mt-8 border-t border-slate-100 pt-5 text-center text-xs text-slate-400">
                Demo accounts are available in the project README for local development.
            </div>
        </section>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const input = document.getElementById('login-password');
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        this.textContent = visible ? 'Show' : 'Hide';
    });
</script>
@endpush
@endsection
