@extends('layouts_app')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-3">
    <div>
    <p class="mb-1 text-sm font-medium text-brand-600">Overview of business performance</p>
    <h1 class="text-3xl font-bold text-slate-900">Reports & Analytics</h1>
    </div>
    <a href="{{ route('reports.print', array_filter(['from' => $from, 'to' => $to])) }}" target="_blank" class="btn-primary">Print / Save as PDF</a>
</div>
<form method="GET" action="{{ route('reports') }}" class="panel mb-5 flex flex-wrap items-end gap-3 p-4">
    <label class="text-sm font-medium">From date<input type="date" name="from" value="{{ $from }}" class="field mt-1"></label>
    <label class="text-sm font-medium">To date<input type="date" name="to" value="{{ $to }}" class="field mt-1"></label>
    <button class="btn-primary">Apply date filter</button>
    @if($from || $to)<a href="{{ route('reports') }}" class="btn-secondary">Clear dates</a>@endif
    <p class="text-xs text-slate-500">{{ $from || $to ? 'Orders, payments, and expenses are filtered to the selected dates.' : 'Showing all recorded dates.' }}</p>
</form>
<div class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach([['💰','Completed revenue','₱'.number_format($revenue, 2),'From claimed orders'],['−','Expenses','₱'.number_format($expensesTotal, 2),'Recorded business costs'],['=','Net profit','₱'.number_format($netProfit, 2),'Revenue less expenses'],['▧','Total orders',$orders->count(),$from || $to ? 'Orders in selected range' : 'All recorded dates']] as [$icon,$label,$value,$hint])
        <div class="panel flex gap-4 p-5"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-xl">{{ $icon }}</span><div><p class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</p><p class="font-display text-2xl font-bold text-slate-900">{{ $value }}</p><p class="text-xs text-slate-400">{{ $hint }}</p></div></div>
    @endforeach
</div>
<div class="mb-5 grid gap-5 lg:grid-cols-2"><section class="panel p-5"><h2 class="mb-4 font-display text-lg font-semibold">Payments by Method</h2>@forelse($paymentsByMethod as $method => $amount)<div class="flex justify-between border-b border-slate-100 py-2 text-sm"><span class="capitalize">{{ $method }}</span><strong>₱{{ number_format($amount, 2) }}</strong></div>@empty<p class="text-sm text-slate-500">No payments recorded.</p>@endforelse</section><section class="panel p-5"><h2 class="mb-4 font-display text-lg font-semibold">Order Status Distribution</h2><div class="space-y-2">@foreach($statusBreakdown as $status => $count)<div class="flex justify-between text-sm"><span class="capitalize">{{ str_replace('_', ' ', $status) }}</span><strong>{{ $count }}</strong></div>@endforeach</div></section></div>
<div class="panel mb-5 p-5">
    <h2 class="mb-4 font-display text-lg font-semibold">Services Breakdown</h2>
    <div class="space-y-4">
        @forelse($serviceBreakdown as $service => $count)
            <div><div class="mb-1 flex justify-between text-sm"><span>{{ $service }}</span><span class="text-slate-500">{{ $count }} orders</span></div><div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-brand-400" style="width: {{ $orders->count() ? max(8, ($count / $orders->count()) * 100) : 0 }}%"></div></div></div>
        @empty <p class="text-sm text-slate-500">No order data yet.</p> @endforelse
    </div>
</div>
<div class="grid gap-5 lg:grid-cols-2">
    <div class="panel p-5"><h2 class="mb-4 font-display text-lg font-semibold">Order Status Distribution</h2><div class="space-y-3">@foreach($statusBreakdown as $status => $count)<div class="flex items-center justify-between"><span class="badge {{ $status === 'pending' ? 'bg-amber-100 text-amber-700' : ($status === 'cancelled' ? 'bg-red-100 text-red-700' : ($status === 'delivered' ? 'bg-slate-100 text-slate-700' : ($status === 'ready' ? 'bg-brand-100 text-brand-700' : 'bg-blue-100 text-blue-700'))) }}">{{ ucfirst($status) }}</span><span class="text-sm text-slate-500">{{ $count }}</span></div>@endforeach</div></div>
    <div class="panel p-5"><h2 class="mb-4 font-display text-lg font-semibold">Top Customers</h2><div class="space-y-3">@forelse($topCustomers as $customer)<div class="flex items-center justify-between text-sm"><span class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-brand-700">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>{{ $customer->name }}</span><strong class="text-brand-600">{{ $customer->orders_count }}</strong></div>@empty<p class="text-sm text-slate-500">No customers yet.</p>@endforelse</div></div>
</div>
@endsection
