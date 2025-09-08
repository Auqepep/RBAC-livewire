<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .verify-button:hover {
            transform: translateY(-2px);
            color: #ffffff !important;
        }

        .message {
            text-align: center;
            margin: 20px 0;
        }
        .alternative-link {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 12px;
            color: #6c757d;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
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
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <h1>Welcome, {{ $user->name }}!</h1>
        </div>

        <div class="message">
            <h2>Please verify your email address</h2>
            <p>Thank you for registering! To complete your registration and activate your account, please click the button below to verify your email address:</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="verify-button">
                ✓ Verify Email Address
            </a>
        </div>

        <div class="warning">
            <strong>Important:</strong> This verification link will expire in 60 minutes for security reasons.
        </div>

        <div class="alternative-link">
            <strong>Having trouble with the button?</strong><br>
            Copy and paste this link into your browser:<br>
            <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
        </div>

        <div class="footer">
            <p>If you didn't create an account, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
