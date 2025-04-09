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

        <p>We would like to inform you that a client user account associated with the business 
            <strong>{{ $data['business_name'] ?? 'your company' }}</strong> has been deleted from our system.</p>

        <p><strong>Deleted User Information:</strong></p>
        <ul>
            <li><strong>Name:</strong> {{ $data['deleted_name'] ?? '-' }}</li>
            <li><strong>Email:</strong> {{ $data['deleted_email'] ?? '-' }}</li>
        </ul>

        <p>If you believe this action was taken in error or if you have any questions, please contact our support team.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
