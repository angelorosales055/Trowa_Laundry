@extends('layouts_app')
@section('content')
<div class="mb-7 flex items-start justify-between"><div><p class="mb-1 text-sm text-slate-500">{{ $customers->total() }} registered customers</p><h1 class="text-3xl font-bold text-slate-900">Customers</h1></div><button type="button" data-modal-open="customer-modal" class="btn-primary">＋ Add Customer</button></div>
<form method="GET" action="{{ route('customers.index') }}" class="mb-5"><input name="search" value="{{ $search }}" class="field" placeholder="Search by name, phone, or email..."></form>
<div class="panel overflow-hidden"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Orders</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($customers as $customer)<tr><td class="px-5 py-3"><span class="mr-2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-brand-700">{{ strtoupper(substr($customer->name,0,1)) }}</span>{{ $customer->name }}</td><td class="px-5 py-3">{{ $customer->contact_number ?? $customer->phone ?? '—' }}</td><td class="px-5 py-3">{{ $customer->email ?? '—' }}</td><td class="px-5 py-3"><span class="badge {{ $customer->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></td><td class="px-5 py-3">{{ $customer->orders_count }}</td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No customers found.</td></tr>@endforelse</tbody></table></div><div class="border-t border-slate-100 px-5 py-3">{{ $customers->links() }}</div></div>
@if(auth()->user()->role === 'admin')
<section class="mt-6 space-y-4"><h2 class="font-display text-xl font-semibold">Customer Record Administration</h2>
@foreach($customers as $customer)
<details class="panel p-4"><summary class="cursor-pointer font-semibold">{{ $customer->name }} <span class="ml-2 text-xs font-normal text-slate-500">{{ $customer->orders_count }} orders · {{ $customer->is_active ? 'Active' : 'Inactive' }}</span></summary>
<div class="mt-4 grid gap-5 lg:grid-cols-2">
    <form method="POST" action="{{ route('customers.update', $customer) }}" class="grid gap-3 sm:grid-cols-2">@csrf @method('PUT')
        <label class="text-sm font-medium">Name<input name="name" value="{{ $customer->name }}" class="field mt-1" required></label>
        <label class="text-sm font-medium">Phone<input name="phone" value="{{ $customer->phone }}" class="field mt-1"></label>
        <label class="text-sm font-medium">Contact number<input name="contact_number" value="{{ $customer->contact_number }}" class="field mt-1"></label>
        <label class="text-sm font-medium">Email<input name="email" type="email" value="{{ $customer->email }}" class="field mt-1"></label>
        <label class="text-sm font-medium sm:col-span-2">Address<input name="address" value="{{ $customer->address }}" class="field mt-1"></label>
        <button class="btn-secondary sm:col-span-2">Save Customer</button>
    </form>
    <div class="space-y-3">
        @if($customer->is_active && !$customer->merged_into_id)
        <form method="POST" action="{{ route('customers.merge', $customer) }}" class="flex gap-2">@csrf
            <select name="target_customer_id" class="field" required><option value="">Merge into active customer...</option>@foreach($mergeTargets->where('id', '!=', $customer->id) as $target)<option value="{{ $target->id }}">{{ $target->name }}</option>@endforeach</select>
            <button class="btn-secondary" onclick="return confirm('Merge this customer into the selected record? Existing order customer snapshots will be preserved.')">Merge</button>
        </form>
        <form method="POST" action="{{ route('customers.toggle-active', $customer) }}">@csrf @method('PATCH')
            <button class="text-sm font-semibold text-amber-700">{{ $customer->is_active ? 'Deactivate Customer' : 'Reactivate Customer' }}</button>
        </form>
        @endif
        @if($customer->merged_into_id)<p class="text-sm text-slate-500">Merged into {{ $customer->mergedInto?->name }}.</p>@endif
    </div>
</div></details>
@endforeach
</section>
@endif
<div id="customer-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4"><div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"><div class="mb-5 flex items-center justify-between"><h2 class="font-display text-xl font-bold">Add Customer</h2><button data-modal-close class="text-2xl text-slate-400">×</button></div><form method="POST" action="{{ route('customers.store') }}" class="space-y-4">@csrf<label class="block text-sm font-medium">Full Name<input name="name" class="field mt-1" placeholder="John Doe" required></label><label class="block text-sm font-medium">Phone<input name="phone" class="field mt-1" placeholder="09XXXXXXXXX"></label><label class="block text-sm font-medium">Email<input name="email" type="email" class="field mt-1" placeholder="email@mail.com"></label><div class="flex gap-3 pt-2"><button type="button" data-modal-close class="btn-secondary flex-1">Cancel</button><button class="btn-primary flex-1">Add Customer</button></div></form></div></div>
@push('scripts')<script>document.querySelectorAll('[data-modal-open]').forEach(b=>b.addEventListener('click',()=>document.getElementById(b.dataset.modalOpen).classList.replace('hidden','flex')));document.querySelectorAll('[data-modal-close]').forEach(b=>b.addEventListener('click',()=>b.closest('[data-modal]').classList.replace('flex','hidden')));</script>@endpush
@endsection
