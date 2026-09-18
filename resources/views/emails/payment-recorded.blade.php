<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Received</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('logo.png') }}" alt="Adili Real Estate" style="height: 60px;">
            <p style="color: #0ea5e9; font-style: italic; font-size: 12px; margin: 4px 0 0;">Defined by Trust</p>
        </div>

        <h1 style="color: #0ea5e9; font-size: 22px; margin: 0 0 8px;">Payment Received</h1>
        <p style="color: #6b7280; font-size: 14px; margin: 0 0 24px;">
            Hi <strong>{{ $buyerName }}</strong>, we've recorded your payment. Thank you.
        </p>

        <!-- Payment Details -->
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <table style="width: 100%; font-size: 14px; color: #374151;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Project</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">{{ $projectName }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Plot</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 600;">#{{ $plotNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Amount</td>
                    <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #0ea5e9;">
                        KES {{ number_format($amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Method</td>
                    <td style="padding: 6px 0; text-align: right;">{{ $method ?? 'N/A' }}</td>
                </tr>
                @if($reference)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Reference</td>
                    <td style="padding: 6px 0; text-align: right;">{{ $reference }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #6b7280;">Date</td>
                    <td style="padding: 6px 0; text-align: right;">{{ $date }}</td>
                </tr>
            </table>
        </div>

        <!-- Progress -->
        <h3 style="color: #111827; font-size: 15px; margin: 0 0 12px;">Payment Progress</h3>
        <table style="width: 100%; font-size: 14px; color: #374151; margin-bottom: 12px;">
            <tr>
                <td style="padding: 4px 0; color: #6b7280;">Total Price</td>
                <td style="padding: 4px 0; text-align: right;">KES {{ number_format($totalPrice, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: #6b7280;">Total Paid</td>
                <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0ea5e9;">
                    KES {{ number_format($totalPaid, 2) }}
                </td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: #6b7280;">Remaining</td>
                <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #ef4444;">
                    KES {{ number_format($remaining, 2) }}
                </td>
            </tr>
        </table>

        <!-- Progress Bar -->
        <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; margin-bottom: 24px;">
            <div style="height: 100%; width: {{ $progress }}%; background: #0ea5e9;"></div>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px;">
            Your receipt is attached to this email. You can also view it any time in your buyer portal.
        </p>

        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/buyer/portal') }}"
               style="display: inline-block; background: #0ea5e9; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                View in My Portal
            </a>
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