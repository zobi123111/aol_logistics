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

    <p>A new user has been created under your company <strong>{{ $data['company_name'] ?? 'Your Company' }}</strong>.</p>

<p>
    <strong>Login Email:</strong> {{ $data['email'] }}<br>
    <strong>Password:</strong> {{ $data['password'] }}<br>
</p>

<p>Please use the above credentials to log in and manage your profile.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
