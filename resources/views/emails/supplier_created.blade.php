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

        <p>Welcome to {{ config('app.name') }}!</p>

        <p>Your supplier account for the company <strong>{{ $data['company_name'] ?? '' }}</strong> has been successfully created.</p>

        <p>
            <strong>Login Email:</strong> {{ $data['login_email'] ?? '' }}<br>
            <strong>Primary Contact Email:</strong> {{ $data['contact_email'] ?? '' }}<br>
            <strong>Password:</strong> {{ $data['password'] ?? '' }}
        </p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
