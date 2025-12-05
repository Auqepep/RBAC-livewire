<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode OTP Anda</title>
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
        @elseif($type === 'login')
            <h2>Kode Login Anda</h2>
            <p>Gunakan kode OTP di bawah ini untuk menyelesaikan login Anda:</p>
    </div>

    <div class="otp-code">
        {{ $otp }}
    </div>

    <div class="warning">
        <strong>Penting:</strong> Kode ini akan kedaluwarsa dalam 10 menit. Jangan bagikan kode ini kepada siapa pun.
    </div>

    <div class="footer">
        <p>Jika Anda tidak meminta kode ini, abaikan email ini.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi undang-undang.</p>
    </div>
</body>
</html>
