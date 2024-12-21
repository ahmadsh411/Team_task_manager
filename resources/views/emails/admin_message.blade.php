<!DOCTYPE html>
<html>
<head>
    <title>Your Password Reset Code</title>
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
        .code {
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
    <div class="header">Password Reset Request</div>
    <div class="content">
        <p>Hello,</p>
        <p>We received a request to reset your password. Please use the following code to reset your password:</p>
        <div class="code">{{ $code }}</div>
        <p>If you did not request this, please ignore this email.</p>
    </div>
    <div class="footer">
        <p>Thank you,<br>Your Application Team</p>
    </div>
</div>
</body>
</html>
