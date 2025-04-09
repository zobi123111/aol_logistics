<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .container {
            max-width: 600px;
            margin: auto;
            padding: 40px;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Hello,</p>

        <p>We’d like to inform you that a load assigned to the company 
            <strong>{{ $data['company_name'] ?? 'Unknown Company' }}</strong> has been cancelled.</p>

        <p><strong>AOL Number:</strong> {{ $data['aol_number']  }}</p>

        <p><strong>Reason for cancellation:</strong><br>
        {{ $data['reason']  }}</p>

        <p>If you have any questions or need further assistance, please contact our support team.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
