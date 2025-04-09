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

        <p>Welcome to {{ config('app.name') }}! Your client account has been successfully created.</p>

        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Password:</strong> {{ $data['password'] }}</p>

        <p>You can now log in using the above credentials.</p>

        <p>If you have any questions, feel free to contact our support team.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
