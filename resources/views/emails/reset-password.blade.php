<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('logo.png') }}" alt="Adili Real Estate" style="height: 60px;"></p>
            <p style="color: #0ea5e9; font-style: italic; font-size: 12px; margin: 4px 0 0;">Defined by Trust</p>
        </div>

        <h1 style="color: #0ea5e9; font-size: 22px; margin: 0 0 8px;">Reset Your Password</h1>
        <p style="color: #6b7280; font-size: 14px; margin: 0 0 24px;">
            Hi <strong>{{ $name }}</strong>, we received a request to reset the password for your Adili buyer account.
        </p>

        <!-- Account Details -->
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <table style="width: 100%; font-size: 14px; color: #374151;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Account Email</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">{{ $email }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Request Type</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">Password Reset</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px;">
            Click the button below to set a new password. This link will expire in <strong>60 minutes</strong>.
        </p>

        <!-- CTA -->
        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ $url }}"
               style="display: inline-block; background: #0ea5e9; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                Reset My Password
            </a>
        </div>

        <p style="font-size: 13px; color: #6b7280; margin: 0 0 20px;">
            If the button doesn't work, copy and paste this URL into your browser:
        </p>
        <p style="font-size: 12px; color: #0ea5e9; word-break: break-all; background: #f9fafb; padding: 12px; border-radius: 6px; margin: 0 0 24px;">
            {{ $url }}
        </p>

        <!-- Warning Box -->
        <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <p style="font-size: 13px; color: #991b1b; margin: 0;">
                <strong>Didn't request this?</strong> You can safely ignore this email. Your password won't change unless you click the button above.
            </p>
        </div>

        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 24px 0 0;">
            Questions? Reply to this email or contact us at
            <a href="mailto:info@adilirealestate.com" style="color: #0ea5e9;">info@adilirealestate.com</a>
        </p>
    </div>

    <p style="text-align: center; font-size: 11px; color: #9ca3af; margin-top: 16px;">
        &copy; {{ date('Y') }} Adili Real Estate. All rights reserved.
    </p>
</body>
</html>