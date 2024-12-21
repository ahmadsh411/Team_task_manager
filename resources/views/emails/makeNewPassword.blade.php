<!DOCTYPE html>
<html>
<head>
    <title>Your New Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
        }
        .content {
            margin: 20px 0;
            font-size: 16px;
        }
        .password {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">Your New Password</div>
    <div class="content">
        <p>Hello,</p>
        <p>We have generated a new password for your account as requested. Please use the following password to log in:</p>
        <div class="password">{{ $password }}</div>
        <p>After logging in, we strongly recommend updating your password to something more memorable.</p>
    </div>
    <div class="footer">
        <p>Thank you,<br>Your Application Team</p>
    </div>
</div>
</body>
</html>
