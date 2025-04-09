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

    <p>This is to inform you that a user account under your company <strong>{{ $data['company_name'] ?? 'Company' }}</strong> has been deleted.</p>

    <p><strong>Email:</strong> {{ $data['email'] }}</p>

    <p>If this was not authorized or you have any questions, please contact support.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
