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

        <p>A new user has been successfully created under your business account 
            <strong>{{ $data['business_name'] ?? 'your company' }}</strong>.</p>

        <p><strong>User Details:</strong></p>
        <ul>
            <li><strong>Name:</strong> {{ $data['fname'] ?? '-' }}</li>
            <li><strong>Email:</strong> {{ $data['email'] ?? '-' }}</li>
            <li><strong>Password:</strong> {{ $data['password'] ?? '-' }}</li>
        </ul>

        <p>The user can now log in to the system using the above credentials. 
        It’s recommended that they change their password upon first login.</p>

        <p>Thanks,<br>{{ config('app.name') }} Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
