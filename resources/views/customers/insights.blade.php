@extends('layouts_app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-[#25799B]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21H3V3"/><path d="M7 14l4-4 4 4 6-6"/><circle cx="11" cy="10" r="1.5"/><circle cx="15" cy="14" r="1.5"/><circle cx="21" cy="8" r="1.5"/></svg>
            <span>Customer Intelligence & Lifetime Value Analytics</span>
        </div>
        <h1 class="mt-1 font-recoleta text-3xl sm:text-4xl font-extrabold text-[#182830]">
            Customer Insights
        </h1>
        <p class="text-xs text-[#25799B] font-medium mt-0.5">
            Behavioral analysis, loyalty segmentation, top spenders, visit frequencies, and churn prevention.
        </p>
    </div>

    <!-- Date Range Filter & Mode Badge -->
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('customers.insights') }}" class="flex flex-wrap items-end gap-2 rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-2 shadow-[2px_2px_0px_#182830]">
            <div>
                <label for="insight-date-from" class="block font-mono text-[10px] font-bold uppercase text-[#25799B]">From</label>
                <input id="insight-date-from" type="date" name="from" value="{{ $from }}" class="field mt-0.5 h-8 px-2 py-1 text-xs font-mono font-bold w-32">
            </div>
            <div>
                <label for="insight-date-to" class="block font-mono text-[10px] font-bold uppercase text-[#25799B]">To</label>
                <input id="insight-date-to" type="date" name="to" value="{{ $to }}" class="field mt-0.5 h-8 px-2 py-1 text-xs font-mono font-bold w-32">
            </div>
            <button type="submit" class="retro-btn-primary h-8 px-3 text-xs">Filter</button>
            @if($from || $to)
                <a href="{{ route('customers.insights') }}" class="retro-btn-secondary h-8 px-2.5 text-xs">Clear</a>
            @endif
        </form>

        <span class="rounded-xl border-2 border-[#182830] bg-[#25799B] px-3 py-1.5 font-mono text-xs font-extrabold uppercase text-[#FFFDF8] shadow-[2px_2px_0px_#182830]">
            Executive Portal
        </span>
    </div>
</div>

<!-- 4 High-Impact Customer KPIs -->
<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="retro-panel flex items-start gap-3.5 p-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#A2C5D8] shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Avg Customer LTV</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">₱{{ number_format($averageLtv, 2) }}</span>
            <span class="block font-mono text-[11px] text-slate-500">Lifetime revenue per account</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-emerald-100 text-emerald-800 shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Repeat Visit Rate</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">{{ $repeatRate }}%</span>
            <span class="block font-mono text-[11px] text-slate-500">{{ $repeatCount }} of {{ $totalCustomers }} returning clients</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#F7E6CB] shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Avg Order Value (AOV)</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">₱{{ number_format($averageOrderValue, 2) }}</span>
            <span class="block font-mono text-[11px] text-slate-500">Across {{ $totalOrdersCount }} completed tickets</span>
        </div>
    </div>

    <div class="retro-panel flex items-start gap-3.5 p-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-[#182830] bg-[#25799B] text-white shadow-[2px_2px_0px_#182830]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-wider text-[#25799B]">Avg Weight / Wash</span>
            <span class="block font-recoleta text-2xl font-extrabold text-[#182830] leading-tight my-0.5">{{ number_format($averageKgPerOrder, 1) }} kg</span>
            <span class="block font-mono text-[11px] text-slate-500">Typical laundry load size</span>
        </div>
    </div>
</div>

<!-- Customer Segmentation Matrix -->
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
        <div class="flex items-center justify-between">
            <span class="badge border-[#182830] bg-amber-100 text-amber-900 font-bold uppercase text-[10px]">Tier 1 · VIP Champions</span>
            <svg class="h-4 w-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="mt-2">
            <span class="font-recoleta text-3xl font-extrabold text-[#182830]">{{ $vipCount }}</span>
            <p class="font-mono text-xs font-semibold text-[#25799B] mt-0.5">5+ visits or ₱2,000+ total spend</p>
            <p class="text-[11px] text-slate-600 mt-1">High retention bedrock. Eligible for loyalty cards & priority rack handling.</p>
        </div>
    </div>

    <div class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
        <div class="flex items-center justify-between">
            <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] font-bold uppercase text-[10px]">Tier 2 · Loyal Regulars</span>
            <svg class="h-4 w-4 text-[#25799B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="mt-2">
            <span class="font-recoleta text-3xl font-extrabold text-[#182830]">{{ $regularCount }}</span>
            <p class="font-mono text-xs font-semibold text-[#25799B] mt-0.5">2 to 4 completed intakes</p>
            <p class="text-[11px] text-slate-600 mt-1">Returning households. Consistent weekly or bi-weekly laundry cadence.</p>
        </div>
    </div>

    <div class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
        <div class="flex items-center justify-between">
            <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-bold uppercase text-[10px]">Tier 3 · New Walk-ins</span>
            <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        </div>
        <div class="mt-2">
            <span class="font-recoleta text-3xl font-extrabold text-[#182830]">{{ $newCount }}</span>
            <p class="font-mono text-xs font-semibold text-[#25799B] mt-0.5">1 single visit registered</p>
            <p class="text-[11px] text-slate-600 mt-1">Prime conversion targets. Send follow-up SMS or offer repeat discounts.</p>
        </div>
    </div>

    <div class="rounded-xl border-2 border-[#182830] bg-[#FFFDF8] p-4 shadow-[2px_2px_0px_#182830]">
        <div class="flex items-center justify-between">
            <span class="badge border-[#182830] bg-red-100 text-[#CB1B03] font-bold uppercase text-[10px]">Tier 4 · Churn Risk</span>
            <svg class="h-4 w-4 text-[#CB1B03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="mt-2">
            <span class="font-recoleta text-3xl font-extrabold text-[#CB1B03]">{{ $dormantCount }}</span>
            <p class="font-mono text-xs font-semibold text-[#CB1B03] mt-0.5">Inactive > 45 days</p>
            <p class="text-[11px] text-slate-600 mt-1">Customers at risk of leaving. Require reactivation re-engagement outreach.</p>
        </div>
    </div>
</div>

<!-- Top Spenders Leaderboard Table -->
<div class="retro-panel mb-6 p-5">
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#182830]/15 pb-3 gap-2">
        <div>
            <h2 class="font-recoleta text-xl font-bold text-[#182830]">Top Spending Customer Leaderboard</h2>
            <p class="font-mono text-xs text-[#25799B]">Highest lifetime value clients and wash patterns</p>
        </div>
        <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830]">
            Top 10 Accounts
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left font-sans text-xs">
            <thead class="border-b-2 border-[#182830] bg-[#A2C5D8]/20 font-mono text-[11px] font-bold uppercase text-[#182830]">
                <tr>
                    <th class="py-3 px-3">Rank & Client</th>
                    <th class="py-3 px-3">Contact</th>
                    <th class="py-3 px-3 text-right">Lifetime Spend</th>
                    <th class="py-3 px-3 text-center">Orders</th>
                    <th class="py-3 px-3 text-right">Total Kg</th>
                    <th class="py-3 px-3 text-right">Avg Ticket</th>
                    <th class="py-3 px-3 text-center">Last Visit</th>
                    <th class="py-3 px-3 text-center">Status Tier</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#182830]/10 font-medium">
                @forelse($topSpenders as $index => $customer)
                    @php($lastOrder = $customer->orders->first())
                    @php($daysAgo = $lastOrder?->created_at ? $lastOrder->created_at->diffInDays(now()) : null)
                    @php($avgTicket = $customer->orders_count > 0 ? (float) $customer->orders_sum_total_price / $customer->orders_count : 0)
                    <tr class="hover:bg-[#F7E6CB]/30 transition">
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border-2 border-[#182830] {{ $index === 0 ? 'bg-amber-300 font-black' : ($index === 1 ? 'bg-slate-200 font-bold' : ($index === 2 ? 'bg-amber-600 text-white font-bold' : 'bg-[#FFFDF8] font-mono')) }} text-xs shadow-[1px_1px_0px_#182830]">
                                    #{{ $index + 1 }}
                                </span>
                                <div>
                                    <strong class="font-recoleta text-sm text-[#182830] block">{{ $customer->name }}</strong>
                                    <span class="font-mono text-[10px] text-slate-500">ID: #{{ $customer->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 font-mono text-[11px] text-slate-600">
                            {{ $customer->phone ?? $customer->contact_number ?? '—' }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-sm font-extrabold text-[#182830]">
                            ₱{{ number_format((float) $customer->orders_sum_total_price, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex rounded-md border border-[#182830]/30 bg-[#F7E6CB] px-2 py-0.5 font-mono text-xs font-bold text-[#182830]">
                                {{ $customer->orders_count }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-xs font-bold text-[#25799B]">
                            {{ number_format((float) $customer->orders_sum_weight_kg, 1) }} kg
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-xs text-slate-700">
                            ₱{{ number_format($avgTicket, 2) }}
                        </td>
                        <td class="py-3 px-3 text-center font-mono text-xs">
                            @if($lastOrder?->created_at)
                                <span class="block font-bold text-[#182830]">{{ $lastOrder->created_at->format('M j, Y') }}</span>
                                <span class="block text-[10px] {{ $daysAgo > 45 ? 'text-[#CB1B03] font-bold' : 'text-slate-500' }}">
                                    {{ $daysAgo === 0 ? 'Today' : ($daysAgo === 1 ? 'Yesterday' : "{$daysAgo} days ago") }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($customer->orders_count >= 5 || (float) $customer->orders_sum_total_price >= 2000)
                                <span class="badge border-[#182830] bg-amber-100 text-amber-900 font-mono text-[10px] font-bold">VIP Champion</span>
                            @elseif($customer->orders_count >= 2)
                                <span class="badge border-[#182830] bg-[#A2C5D8] text-[#182830] font-mono text-[10px] font-bold">Regular</span>
                            @else
                                <span class="badge border-[#182830] bg-[#F7E6CB] text-[#182830] font-mono text-[10px] font-bold">Walk-in</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center font-mono text-xs text-slate-500">No customer spend records found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Visit Frequency & Weekly Intake Distribution -->
<div class="mb-6 grid gap-6 lg:grid-cols-2">
    <!-- Visit Frequency Cohorts -->
    <section class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Order Frequency Cohorts</h2>
                <p class="font-mono text-xs text-[#25799B]">Number of repeat visits per customer profile</p>
            </div>
            <span class="font-mono text-xs font-bold text-slate-600">{{ $totalCustomers }} Accounts</span>
        </div>

        <div class="space-y-3">
            @php($freqMax = max(1, max($frequencyDistribution)))
            @foreach($frequencyDistribution as $bucket => $count)
                @php($pct = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100, 1) : 0)
                <div class="flex items-center gap-3 rounded-xl border border-[#182830]/15 bg-[#FFFDF8] p-3 shadow-[1px_1px_0px_#182830]">
                    <span class="w-28 shrink-0 font-mono text-xs font-bold text-[#182830]">{{ $bucket }}</span>
                    <div class="h-3 flex-1 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                        <div class="h-full rounded-full bg-[#25799B]" style="width: {{ max(5, ($count / $freqMax) * 100) }}%"></div>
                    </div>
                    <span class="w-16 text-right font-mono text-xs font-black text-[#182830]">{{ $count }} ({{ $pct }}%)</span>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Day of Week Intake Pattern -->
    <section class="retro-panel p-5">
        <div class="mb-4 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2.5">
            <div>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Weekly Footfall & Intake Pattern</h2>
                <p class="font-mono text-xs text-[#25799B]">Drop-off volume concentration by day of week</p>
            </div>
            <span class="font-mono text-xs font-bold text-[#CB1B03]">Peak Analysis</span>
        </div>

        <div class="space-y-2">
            @php($dowMax = max(1, $dayOfWeekStats->max()))
            @foreach($dayOfWeekStats as $day => $count)
                @php($isPeak = $count > 0 && $count === $dowMax)
                <div class="flex items-center gap-3 rounded-xl border {{ $isPeak ? 'border-[#CB1B03] bg-red-50/50' : 'border-[#182830]/15 bg-[#FFFDF8]' }} p-2.5 shadow-[1px_1px_0px_#182830]">
                    <span class="w-24 shrink-0 font-mono text-xs font-bold {{ $isPeak ? 'text-[#CB1B03]' : 'text-[#182830]' }}">
                        {{ $day }}
                        @if($isPeak)
                            <span class="badge border-[#CB1B03] bg-[#CB1B03] text-white text-[9px] px-1 py-0 ml-1">Peak</span>
                        @endif
                    </span>
                    <div class="h-2.5 flex-1 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                        <div class="h-full rounded-full {{ $isPeak ? 'bg-[#CB1B03]' : 'bg-[#25799B]' }}" style="width: {{ max(4, ($count / $dowMax) * 100) }}%"></div>
                    </div>
                    <span class="w-12 text-right font-mono text-xs font-black text-[#182830]">{{ $count }} orders</span>
                </div>
            @endforeach
        </div>
    </section>
</div>

<!-- Preferred Services & Smart Retention Helper -->
<div class="grid gap-6 lg:grid-cols-2">
    <!-- Service Preferences -->
    <section class="retro-panel p-5">
        <h2 class="font-recoleta text-lg font-bold text-[#182830] mb-3 border-b-2 border-[#182830]/15 pb-2">
            Most Selected Laundry Services
        </h2>
        <div class="space-y-3">
            @forelse($servicePreferences as $service => $count)
                <div>
                    <div class="mb-1 flex justify-between font-mono text-xs font-bold">
                        <span class="text-[#182830]">{{ $service }}</span>
                        <span class="text-[#25799B]">{{ $count }} times</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full border border-[#182830] bg-[#F7E6CB]">
                        <div class="h-full rounded-full bg-[#25799B]" style="width: {{ $totalOrdersCount > 0 ? max(8, ($count / $totalOrdersCount) * 100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="font-mono text-xs text-slate-500 py-3">No service usage recorded in this period.</p>
            @endforelse
        </div>
    </section>

    <!-- Smart Retention & Growth Advisor -->
    <section class="retro-panel p-5 bg-[#FFFDF8]">
        <div class="mb-3 flex items-center justify-between border-b-2 border-[#182830]/15 pb-2">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg border-2 border-[#182830] bg-[#CB1B03] text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                </span>
                <h2 class="font-recoleta text-lg font-bold text-[#182830]">Retention &amp; Growth Advisor</h2>
            </div>
            <span class="badge border-[#182830] bg-emerald-100 text-emerald-800 text-[10px] font-bold">Automated Insights</span>
        </div>

        <div class="space-y-3 text-xs">
            <div class="rounded-xl border border-[#182830]/20 bg-[#F7E6CB]/40 p-3">
                <div class="flex items-center gap-2 font-mono font-bold text-[#CB1B03]">
                    <span>At-Risk Customer Re-engagement</span>
                </div>
                <p class="text-slate-700 mt-1">
                    You have <strong>{{ $dormantCount }} customers</strong> who have not brought laundry in over 45 days. Sending an automated SMS reminder with a 10% next-wash voucher typically recovers 18-24% of inactive accounts.
                </p>
            </div>

            <div class="rounded-xl border border-[#182830]/20 bg-[#A2C5D8]/20 p-3">
                <div class="flex items-center gap-2 font-mono font-bold text-[#25799B]">
                    <span>Mid-Week Footfall Balancing</span>
                </div>
                <p class="text-slate-700 mt-1">
                    Peak laundry volumes concentrate on weekend and Friday intakes. Introduce a "Tuesday Wash & Fold Special" to balance weekday capacity and maximize machine utilization across all 8 washers.
                </p>
            </div>

            <div class="rounded-xl border border-[#182830]/20 bg-emerald-50/60 p-3">
                <div class="flex items-center gap-2 font-mono font-bold text-emerald-800">
                    <span>VIP Loyalty Rewards</span>
                </div>
                <p class="text-slate-700 mt-1">
                    Your <strong>{{ $vipCount }} VIP Champions</strong> generate disproportionate revenue with an average LTV of ₱{{ number_format($averageLtv, 2) }}. Consider offering complimentary fabric conditioner upgrade or express next-day turnaround to cement brand loyalty.
                </p>
            </div>
        </div>
    </section>
</div>
@endsection
