<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .otp-code {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #495057;
            margin: 20px 0;
            border-radius: 8px;
        }
        .message {
            text-align: center;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="message">
        @if($type === 'register')
            <h2>Welcome! Please verify your email</h2>
            <p>Thank you for registering. Use the OTP code below to verify your email address:</p>
        @elseif($type === 'login')
            <h2>Your Login Code</h2>
            <p>Use the OTP code below to complete your login:</p>
        @elseif($type === 'password_reset')
            <h2>Password Reset</h2>
            <p>Use the OTP code below to reset your password:</p>
        @endif
    </div>

    <div class="otp-code">
        {{ $otp }}
    </div>

    <div class="warning">
        <strong>Important:</strong> This code will expire in 10 minutes. Do not share this code with anyone.
    </div>

    <div class="footer">
        <p>If you didn't request this code, please ignore this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
