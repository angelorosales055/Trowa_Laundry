<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $customSubject }} - Trowa Laundry</title>
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
            background-color: #1E6482;
            padding: 20px 24px;
            border-bottom: 3px solid #182830;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #F7E6CB;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 4px 0 0;
            color: #BAE6FD;
            font-size: 11px;
            font-family: monospace;
            text-transform: uppercase;
        }
        .content {
            padding: 28px 26px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #182830;
            margin-bottom: 12px;
        }
        .subject-badge {
            display: inline-block;
            background-color: #FEE2E2;
            color: #CB1B03;
            border: 2px solid #182830;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 10px;
            margin-bottom: 16px;
            box-shadow: 2px 2px 0px #182830;
        }
        .message-box {
            background-color: #F8FAFC;
            border: 2px solid #182830;
            border-radius: 12px;
            padding: 18px;
            font-size: 14px;
            line-height: 1.6;
            color: #182830;
            white-space: pre-wrap;
            margin-bottom: 24px;
            box-shadow: 3px 3px 0px #182830;
        }
        .portal-btn {
            display: block;
            text-align: center;
            background-color: #CB1B03;
            color: #FFFDF8 !important;
            text-decoration: none;
            font-weight: 900;
            font-size: 14px;
            padding: 12px 20px;
            border: 2px solid #182830;
            border-radius: 12px;
            box-shadow: 3px 3px 0px #182830;
            margin: 20px auto 0;
            max-width: 280px;
        }
        .footer {
            background-color: #F7E6CB;
            border-top: 2px solid #182830;
            padding: 16px 20px;
            text-align: center;
            font-size: 11px;
            color: #475569;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧺 TROWA LAUNDRY</h1>
            <p>Direct Message from Branch Team</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $customer->name }},</div>

            <div class="subject-badge">
                Regarding: {{ $customSubject }}
            </div>

            <div class="message-box">
{{ $customMessage }}
            </div>

            <p style="font-size: 13px; color: #64748B; margin-top: 14px;">
                @if($staff)
                    Sent by: <strong>{{ $staff->name }}</strong> (Trowa Laundry Staff)<br>
                @endif
                You can track your laundry progress, past receipts, or start a new laundry intake anytime on your customer portal.
            </p>

            <a href="{{ route('customer.portal') }}" class="portal-btn">
                🧺 Open Customer Portal
            </a>
        </div>

        <div class="footer">
            Trowa Laundry · Premium Garment Care & Self-Service Portal<br>
            Got questions? Call counter or reply directly to this email.
        </div>
    </div>
</body>
</html>
