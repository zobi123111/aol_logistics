<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <h1>Hi {{ $user->name }},</h1>
    <p>Your OTP for Two-Factor Authentication is:</p>
    <h2>{{ $otp }}</h2>
    <p>This OTP is valid for the next 5 minutes.</p>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>