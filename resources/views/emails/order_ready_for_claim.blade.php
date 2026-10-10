<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Fresh Laundry is Ready for Claim!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F7E6CB;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #182830;
        }
        .container {
            max-width: 580px;
            margin: 24px auto;
            background-color: #FFFDF8;
            border: 3px solid #182830;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 6px 6px 0px #182830;
        }
        .header {
            background-color: #25799B;
            padding: 20px 24px;
            border-bottom: 3px solid #182830;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #F7E6CB;
            font-size: 20px;
            letter-spacing: 0.5px;
            font-weight: 900;
        }
        .header p {
            margin: 4px 0 0;
            color: #BAE6FD;
            font-size: 11px;
            font-family: monospace;
            text-transform: uppercase;
        }
        .content {
            padding: 32px 28px;
        }
        .status-badge {
            display: inline-block;
            background-color: #10B981;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 800;
            font-family: monospace;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            border: 2px solid #182830;
            box-shadow: 2px 2px 0px #182830;
            margin-bottom: 12px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 900;
            color: #182830;
            margin-bottom: 10px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 20px;
        }
        .order-card {
            background-color: #FFFDF8;
            border: 2px solid #182830;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 4px 4px 0px #182830;
        }
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #CBD5E1;
            font-size: 13px;
        }
        .order-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .order-label {
            color: #64748B;
            font-family: monospace;
            font-weight: 600;
        }
        .order-value {
            font-weight: bold;
            color: #182830;
            text-align: right;
        }
        .instructions-box {
            background-color: #FEF3C7;
            border: 2px solid #D97706;
            border-radius: 14px;
            padding: 16px;
            margin: 20px 0;
            font-size: 13px;
            line-height: 1.5;
            color: #92400E;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0 16px;
        }
        .btn {
            display: inline-block;
            background-color: #CB1B03;
            color: #FFFDF8 !important;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
            padding: 12px 28px;
            border: 2px solid #182830;
            border-radius: 12px;
            box-shadow: 3px 3px 0px #182830;
        }
        .footer {
            background-color: #F7E6CB;
            border-top: 2px solid #182830;
            padding: 16px 24px;
            text-align: center;
            font-size: 11px;
            color: #64748B;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ TROWA LAUNDRY ⚡</h1>
            <p>Order Fulfillment Notification · Counter Pick-up</p>
        </div>

        <div class="content">
            <span class="status-badge">✓ WASH CYCLE COMPLETE</span>
            <div class="greeting">Hi, {{ $order->customer_name }}! ✨</div>
            <p class="text">
                Great news! Your laundry order <strong>#{{ $order->order_number }}</strong> has finished its wash, dry, and folding process. Your clothes are fresh, neatly packaged, and ready for claiming at our counter.
            </p>

            <div class="order-card">
                <div class="order-row">
                    <span class="order-label">Order Ticket:</span>
                    <span class="order-value" style="color: #CB1B03;">#{{ $order->order_number }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Total Weight:</span>
                    <span class="order-value">{{ number_format((float) $order->weight_kg, 1) }} KG ({{ $order->number_of_loads ?? 1 }} drum load{{ ($order->number_of_loads ?? 1) == 1 ? '' : 's' }})</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Treatments:</span>
                    <span class="order-value">{{ $order->services }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Soap Preference:</span>
                    <span class="order-value">{{ $order->soap_preference ?: 'Standard Laundry Detergent' }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Total Amount:</span>
                    <span class="order-value" style="font-size: 15px; color: #25799B;">₱{{ number_format((float) $order->total_price, 2) }}</span>
                </div>
                <div class="order-row">
                    <span class="order-label">Payment Status:</span>
                    <span class="order-value" style="text-transform: uppercase;">{{ $order->payment_status }}</span>
                </div>
            </div>

            <div class="instructions-box">
                <strong>📍 Counter Claiming Instructions:</strong><br>
                Please present Order <strong>#{{ $order->order_number }}</strong> to our branch attendant.<br>
                Counter Hours: <strong>7:00 AM – 10:00 PM</strong><br>
                <em>Note: If you have an unpaid balance, payment can be settled via Cash or GCash upon claiming.</em>
            </div>

            <div class="btn-wrapper">
                <a href="{{ route('customer.portal') }}" class="btn">Open My Customer Portal &amp; Rate Service ➔</a>
            </div>
        </div>

        <div class="footer">
            Trowa Commercial Vintage Laundromat · 8 Commercial Washers Bay<br>
            Thank you for trusting Trowa Laundry with your everyday clothes!
        </div>
    </div>
</body>
</html>
