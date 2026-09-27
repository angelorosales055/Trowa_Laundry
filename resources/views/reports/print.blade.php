<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trowa Laundry Business Summary</title>
    <style>
        body{font:14px Arial,sans-serif;color:#0f172a;margin:32px}h1{margin-bottom:4px}h2{margin-top:28px;border-bottom:1px solid #cbd5e1;padding-bottom:6px}.kpis{display:flex;gap:24px;flex-wrap:wrap}.kpi{border:1px solid #cbd5e1;padding:12px;min-width:180px}.kpi strong{display:block;font-size:20px;margin-top:8px}table{border-collapse:collapse;width:100%;margin-top:12px}th,td{border-bottom:1px solid #e2e8f0;padding:8px;text-align:left}th{background:#f1f5f9}.toolbar{margin-bottom:24px}.toolbar button{padding:10px 16px}@media print{.toolbar{display:none}body{margin:14mm}}
    </style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Print or Save as PDF</button></div>
<h1>Trowa Laundry House</h1><p>Business and operations summary · {{ $from || $to ? (($from ?? 'Beginning').' to '.($to ?? 'Today')) : 'All recorded dates' }} · Generated {{ now()->format('F j, Y g:i A') }}</p>
<div class="kpis"><div class="kpi">Claimed order revenue<strong>₱{{ number_format((float) $revenue, 2) }}</strong></div><div class="kpi">Recorded expenses<strong>₱{{ number_format((float) $expensesTotal, 2) }}</strong></div><div class="kpi">Estimated net profit<strong>₱{{ number_format((float) $netProfit, 2) }}</strong></div><div class="kpi">Orders<strong>{{ $orders->count() }}</strong></div></div>
<h2>Orders by Status</h2><table><thead><tr><th>Status</th><th>Order count</th></tr></thead><tbody>@foreach($orderStages as $status=>$count)<tr><td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td><td>{{ $count }}</td></tr>@endforeach</tbody></table>
<h2>Payments by Method</h2><table><thead><tr><th>Method</th><th>Net collected</th></tr></thead><tbody>@forelse($paymentsByMethod as $method=>$amount)<tr><td>{{ ucfirst($method) }}</td><td>₱{{ number_format($amount, 2) }}</td></tr>@empty<tr><td colspan="2">No payments recorded</td></tr>@endforelse</tbody></table>
<h2>Expense Ledger</h2><table><thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th></tr></thead><tbody>@forelse($expenses as $expense)<tr><td>{{ $expense->expense_date->format('Y-m-d') }}</td><td>{{ ucfirst($expense->category) }}</td><td>{{ $expense->description }}</td><td>₱{{ number_format((float) $expense->amount, 2) }}</td></tr>@empty<tr><td colspan="4">No expenses recorded</td></tr>@endforelse</tbody></table>
</body></html>
