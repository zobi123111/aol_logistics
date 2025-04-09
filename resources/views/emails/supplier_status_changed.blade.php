<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #333;
            padding: 30px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .footer {
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Hello,</p>

        <p>Your supplier account associated with <strong>{{ $data['company_name'] ?? 'N/A' }}</strong> has had its status updated.</p>

        <p>
            <strong>Previous Status:</strong> {{ $data['old_status'] ?? 'N/A' }}<br>
            <strong>New Status:</strong> {{ $data['new_status'] ?? 'N/A' }}
        </p>

        @if(($data['new_status'] ?? '') === 'Inactive')
            <p><strong>Note:</strong> You will not be able to log in while your account is inactive.</p>
        @endif

        <p>If you have any questions, please contact our support team.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
