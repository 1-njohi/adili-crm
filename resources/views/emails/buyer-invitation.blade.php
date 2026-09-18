<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Adili Real Estate</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('logo.png') }}" alt="Adili Real Estate" style="height: 60px;">
            <p style="color: #0ea5e9; font-style: italic; font-size: 12px; margin: 4px 0 0;">Defined by Trust</p>
        </div>

        <h1 style="color: #0ea5e9; font-size: 22px; margin: 0 0 8px;">Welcome to Adili Real Estate</h1>
        <p style="color: #6b7280; font-size: 14px; margin: 0 0 24px;">
            Hi <strong>{{ $name }}</strong>, your buyer account is ready. Follow the steps below to activate it and access your portal.
        </p>

        <!-- Account Details -->
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <table style="width: 100%; font-size: 14px; color: #374151;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Your Email</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">{{ $email }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Account Type</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">Buyer Portal</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Status</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #0ea5e9;">Pending Activation</td>
                </tr>
            </table>
        </div>

        <!-- Steps -->
        <h3 style="color: #111827; font-size: 15px; margin: 0 0 12px;">How to activate your account</h3>
        <table style="width: 100%; font-size: 14px; color: #374151; margin-bottom: 24px; border-collapse: separate; border-spacing: 0 8px;">
            <tr>
                <td style="width: 32px; vertical-align: top;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #0ea5e9; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-size: 12px; font-weight: 700;">1</span>
                </td>
                <td style="padding-left: 8px; color: #374151;">
                    Click the button below to open the login page.
                </td>
            </tr>
            <tr>
                <td style="width: 32px; vertical-align: top;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #0ea5e9; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-size: 12px; font-weight: 700;">2</span>
                </td>
                <td style="padding-left: 8px; color: #374151;">
                    Enter your email address and click <strong>"Email password reset link"</strong>.
                </td>
            </tr>
            <tr>
                <td style="width: 32px; vertical-align: top;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #0ea5e9; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-size: 12px; font-weight: 700;">3</span>
                </td>
                <td style="padding-left: 8px; color: #374151;">
                    You'll receive a second email from us. Open it and click the reset link.
                </td>
            </tr>
            <tr>
                <td style="width: 32px; vertical-align: top;">
                    <span style="display: inline-block; width: 24px; height: 24px; background: #0ea5e9; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-size: 12px; font-weight: 700;">4</span>
                </td>
                <td style="padding-left: 8px; color: #374151;">
                    Set your new password and log in to access your buyer portal.
                </td>
            </tr>
        </table>

        <!-- CTA -->
        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ $loginUrl }}"
               style="display: inline-block; background: #0ea5e9; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                Set My Password
            </a>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px; text-align: center;">
            Once activated, you'll be able to view your plot, track installments, and manage everything about your land purchase.
        </p>

        <!-- Info box -->
        <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <p style="font-size: 13px; color: #991b1b; margin: 0;">
                <strong>Note:</strong> If you didn't expect this email, or you're having trouble activating your account, please contact us right away.
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