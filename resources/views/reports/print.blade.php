<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trowa Laundry Business Summary</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: #182830;
            background-color: #F7E6CB;
            margin: 32px;
            padding: 0;
        }
        .sheet {
            background-color: #FFFDF8;
            border: 2px solid #182830;
            box-shadow: 4px 4px 0px #182830;
            border-radius: 16px;
            padding: 32px;
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 28px;
            font-weight: 800;
            color: #182830;
            margin: 0 0 4px 0;
        }
        h2 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 18px;
            margin-top: 28px;
            border-bottom: 2px solid #182830;
            padding-bottom: 6px;
            color: #25799B;
        }
        .kpis {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .kpi {
            background-color: #FFFDF8;
            border: 2px solid #182830;
            border-radius: 12px;
            padding: 14px 18px;
            min-width: 170px;
            box-shadow: 2px 2px 0px #182830;
        }
        .kpi strong {
            display: block;
            font-family: 'Space Grotesk', monospace;
            font-size: 22px;
            font-weight: 800;
            color: #182830;
            margin-top: 6px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 12px;
            font-size: 13px;
        }
        th, td {
            border-bottom: 1px solid #182830;
            padding: 9px 12px;
            text-align: left;
        }
        th {
            background: #A2C5D8;
            color: #182830;
            font-family: 'Space Grotesk', monospace;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }
        .toolbar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: flex-end;
        }
        .toolbar button {
            background-color: #CB1B03;
            color: #FFFDF8;
            border: 2px solid #182830;
            box-shadow: 3px 3px 0px #182830;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
        }
        @media print {
            body { background-color: #ffffff; margin: 0; }
            .sheet { border: none; box-shadow: none; padding: 0; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button onclick="window.print()">Print or Save as PDF</button>
</div>
<div class="sheet">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #182830; padding-bottom: 16px;">
        <div>
            <h1>Trowa Laundry House</h1>
            <p style="margin: 0; font-size: 12px; color: #25799B; font-weight: 600;">Business and operations summary · {{ $from || $to ? (($from ?? 'Beginning').' to '.($to ?? 'Today')) : 'All recorded dates' }}</p>
        </div>
        <div style="text-align: right; font-family: 'Space Grotesk', monospace; font-size: 11px; color: #182830;">
            Generated {{ now()->format('F j, Y g:i A') }}
        </div>
    </div>

    <div class="kpis">
        <div class="kpi">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #25799B;">Claimed order revenue</span>
            <strong>₱{{ number_format((float) $revenue, 2) }}</strong>
        </div>
        <div class="kpi">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #25799B;">Recorded expenses</span>
            <strong>₱{{ number_format((float) $expensesTotal, 2) }}</strong>
        </div>
        <div class="kpi">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #25799B;">Estimated net profit</span>
            <strong style="color: #CB1B03;">₱{{ number_format((float) $netProfit, 2) }}</strong>
        </div>
        <div class="kpi">
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #25799B;">Orders & Volume</span>
            <strong>{{ $orders->count() }} ({{ $totalLoads }} loads)</strong>
        </div>
    </div>

    <h2>Executive Business Plan &amp; 30-Day Projections</h2>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 14px;">
        <div style="background: #F7E6CB; border: 2px solid #182830; border-radius: 10px; padding: 12px;">
            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #25799B;">30-Day Revenue Run-Rate</span>
            <strong style="display: block; font-family: 'Space Grotesk', monospace; font-size: 18px; margin-top: 4px;">₱{{ number_format($projections['projected_30d_rev'], 2) }}</strong>
        </div>
        <div style="background: #A2C5D8; border: 2px solid #182830; border-radius: 10px; padding: 12px;">
            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #182830;">30-Day Net Profit Est.</span>
            <strong style="display: block; font-family: 'Space Grotesk', monospace; font-size: 18px; margin-top: 4px;">₱{{ number_format($projections['projected_30d_profit'], 2) }}</strong>
        </div>
        <div style="background: #FFFDF8; border: 2px solid #182830; border-radius: 10px; padding: 12px;">
            <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #CB1B03;">Monthly Break-Even</span>
            <strong style="display: block; font-family: 'Space Grotesk', monospace; font-size: 18px; margin-top: 4px;">{{ $projections['break_even_loads'] }} Loads</strong>
        </div>
    </div>

    <h2>Unit Economics &amp; Fleet Capacity</h2>
    <table>
        <thead>
            <tr><th>Metric</th><th>Figure</th><th>Benchmark Notes</th></tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: 600;">Total Weight Handled</td>
                <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">{{ number_format($totalKg, 1) }} kg</td>
                <td>Cumulative laundry across {{ $totalLoads }} loads</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Average Revenue per Kilogram</td>
                <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">₱{{ number_format($revenuePerKg, 2) }} / kg</td>
                <td>Price realized per kg processed</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Operating Cost per Kilogram</td>
                <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">₱{{ number_format($costPerKg, 2) }} / kg</td>
                <td>Supplies, utilities & labor expense per kg</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Net Margin per Kilogram</td>
                <td style="font-family: 'Space Grotesk', monospace; font-weight: 700; color: #CB1B03;">₱{{ number_format($revenuePerKg - $costPerKg, 2) }} / kg</td>
                <td>Net contribution per kg processed</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Average Revenue per Washer Load</td>
                <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">₱{{ number_format($revenuePerLoad, 2) }} / load</td>
                <td>Ticket value per wash drum turn</td>
            </tr>
        </tbody>
    </table>

    <h2>Orders by Status</h2>
    <table>
        <thead>
            <tr><th>Status</th><th>Order count</th></tr>
        </thead>
        <tbody>
            @foreach($orderStages as $status => $count)
                <tr>
                    <td style="font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                    <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Payments by Method</h2>
    <table>
        <thead>
            <tr><th>Method</th><th>Net collected</th></tr>
        </thead>
        <tbody>
            @forelse($paymentsByMethod as $method => $amount)
                <tr>
                    <td style="font-weight: 600; text-transform: capitalize;">{{ $method }}</td>
                    <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">₱{{ number_format($amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No payments recorded</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Expense Ledger</h2>
    <table>
        <thead>
            <tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th></tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td style="font-family: 'Space Grotesk', monospace;">{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td style="font-weight: 600; text-transform: capitalize;">{{ $expense->category }}</td>
                    <td>{{ $expense->description }}</td>
                    <td style="font-family: 'Space Grotesk', monospace; font-weight: 700;">₱{{ number_format((float) $expense->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No expenses recorded</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
