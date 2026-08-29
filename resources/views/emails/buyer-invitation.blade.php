<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Adili Real Estate</title>
</head>
<body>
    <h1>Welcome to Adili Real Estate!</h1>
    <p>Dear {{ $name }},</p>
    <p>You have been registered as a buyer. Use the link below to set new password. Use this email.</p>
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
    </ul>
    <p><a href="{{ $loginUrl }}">Click here to set new password</a></p>
    <p>On the page:</p>
    <ol>
        <li>Enter the email address.</li>
        <li>Click "Email password reset link"</li>
        <li>You will receive another email from us</li>
        <li>Click the link on the new email</li>
        <li>Set new password</li>
        <li>Login to access your buyer portal</li>
    </ol>
    <p>Thank you,<br>Adili Real Estate Team</p>
</body>
</html>
