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
        <p>Hello {{ $data['business_name'] ?? 'Client' }},</p>

        <p>We’d like to inform you that the shipment status for your load with AOL number <strong>{{ $data['aol_number'] }}</strong> has been updated.</p>

        <p><strong>Previous Status:</strong> {{ $data['old_status'] ?? 'pending' }}</p>
        <p><strong>New Status:</strong> {{ $data['new_status'] }}</p>

        <p>If you have any questions, feel free to contact our support team.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
