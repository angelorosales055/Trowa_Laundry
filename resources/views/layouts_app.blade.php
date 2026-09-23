<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Trowa Laundry' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
@auth
    <div class="min-h-screen lg:flex">
        <aside class="flex w-full shrink-0 flex-col bg-slate-950 text-white lg:fixed lg:inset-y-0 lg:w-56">
            <div class="flex h-16 items-center justify-between border-b border-slate-800 px-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-display text-sm font-bold">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500">✦</span>
                    Trowa Laundry
                </a>
                <span class="text-slate-500">◀</span>
            </div>
            <nav class="flex gap-1 overflow-x-auto p-3 lg:block lg:space-y-1">
                @php($nav = [
                    ['dashboard', 'Dashboard', '▦', ['staff','manager','admin']],
                    ['customers.index', 'Customers', '♟', ['staff','manager','admin']],
                    ['schedule.index', 'Schedule', '▣', ['staff','manager','admin']],
                    ['reports', 'Reports', '▥', ['manager','admin']],
                    ['billing', 'Billing', '▤', ['admin']],
                    ['services.index', 'Settings', '⚙', ['admin']],
                ])
                @foreach($nav as [$route, $label, $icon, $roles])
                    @if(in_array(auth()->user()->role, $roles, true))
                        <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'bg-brand-500 text-white' : 'text-slate-300 hover:bg-slate-900' }} flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition">
                            <span class="w-5 text-center">{{ $icon }}</span>{{ $label }}
                        </a>
                    @endif
                @endforeach
            </nav>
            <div class="mt-auto border-t border-slate-800 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500/20 text-sm text-brand-300">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-slate-400">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button title="Log out" class="text-slate-400 hover:text-white">↪</button></form>
                </div>
            </div>
        </aside>
        <main class="min-w-0 flex-1 lg:ml-56">
            <div class="mx-auto max-w-[1500px] p-5 sm:p-8">
                @if(session('status'))
                    <div class="mb-5 rounded-lg border border-brand-100 bg-brand-50 px-4 py-3 text-sm text-brand-700">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
@else
    @yield('content')
@endauth
@stack('scripts')
</body>
</html>
