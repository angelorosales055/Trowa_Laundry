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
            font-family: monospace;
        }
        .po-badge {
            display: inline-block;
            background-color: #E0F2FE;
            color: #0369A1;
            border: 2px solid #182830;
            border-radius: 8px;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            margin-left: 6px;
            font-family: monospace;
        }
        .message-box {
            background-color: #F8FAFC;
            border: 2px dashed #182830;
            border-radius: 12px;
            padding: 18px;
            margin: 16px 0 20px 0;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            white-space: pre-line;
        }
        .supplier-info {
            background-color: #FEF3C7;
            border: 2px solid #182830;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 12px;
            font-family: monospace;
        }
        .footer {
            background-color: #F1F5F9;
            padding: 16px 24px;
            border-top: 2px solid #182830;
            text-align: center;
            font-size: 11px;
            color: #64748B;
            font-family: monospace;
        }
        .footer strong {
            color: #182830;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>TROWA LAUNDRY</h1>
            <p>Official Supply Chain &amp; Procurement Inquiry</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $supplier->contact_person ? $supplier->contact_person . ' (' . $supplier->name . ')' : $supplier->name }},
            </div>

            <div>
                <span class="subject-badge">{{ $customSubject }}</span>
                @if($poReference)
                    <span class="po-badge">Ref: {{ $poReference }}</span>
                @endif
            </div>

            <div class="supplier-info">
                <strong>Supplier:</strong> {{ $supplier->name }}<br>
                @if($supplier->phone)<strong>Contact Phone:</strong> {{ $supplier->phone }}<br>@endif
                <strong>Destination Email:</strong> {{ $supplier->email }}
            </div>

            <div class="message-box">
{{ $customMessage }}
            </div>

            <p style="font-size: 13px; color: #64748B; margin-top: 20px; line-height: 1.5;">
                This communication was dispatched automatically from the <strong>Trowa Laundry Management Station</strong> by {{ $sender?->name ?? 'Admin Management' }}. Please reply directly to this email to coordinate procurement, order fulfillment, and delivery invoicing.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>Trowa Laundry House</strong> · Quality Care &amp; Clean Commercial Washing<br>
            Automated Supply Chain Communication System
        </div>
    </div>
</body>
</html>
