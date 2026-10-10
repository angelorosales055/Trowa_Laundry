<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Trowa Laundry Verification Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F7E6CB;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #182830;
        }
        .container {
            max-width: 560px;
            margin: 24px auto;
            background-color: #FFFDF8;
            border: 3px solid #182830;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 6px 6px 0px #182830;
        }
        .header {
            background-color: #1E6482;
            padding: 18px 24px;
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
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #182830;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 24px;
        }
        .code-card {
            background-color: #FFFDF8;
            border: 2px dashed #CB1B03;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
            box-shadow: 3px 3px 0px #182830;
        }
        .code-label {
            font-size: 11px;
            font-family: monospace;
            font-weight: bold;
            color: #CB1B03;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .code-digits {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 8px;
            color: #182830;
            font-family: monospace;
            margin: 4px 0;
        }
        .code-expiry {
            font-size: 11px;
            color: #64748B;
            margin-top: 6px;
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
            <p>Customer Self-Service Portal · Account Verification</p>
        </div>

        <div class="content">
            <div class="greeting">Hello, {{ $user->name }}! 🫧</div>
            <p class="text">
                Welcome to Trowa Laundry! To activate your Customer Self-Service Portal, track your live washing cycles, and request intakes online, enter the 6-digit verification code below:
            </p>

            <div class="code-card">
                <div class="code-label">Your 6-Digit Activation Code</div>
                <div class="code-digits">{{ $code }}</div>
                <div class="code-expiry">Enter this code on the account verification screen.</div>
            </div>

            <div class="btn-wrapper">
                <a href="{{ route('customer.verify') }}" class="btn">Verify Account Now ➔</a>
            </div>

            <p class="text" style="font-size: 12px; color: #64748B; margin-top: 20px;">
                If you did not register for an account at Trowa Laundry, you can safely disregard this email.
            </p>
        </div>

        <div class="footer">
            Trowa Commercial Vintage Laundromat · 8 Commercial Washers Bay<br>
            Fresh scent, gentle garment care, and spotless turnaround.
        </div>
    </div>
</body>
</html>
