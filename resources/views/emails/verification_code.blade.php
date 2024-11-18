<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; margin: 0 auto; padding: 40px 20px;">
        <tr>
            <td>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px;">
                            <div style="background-color: #223662; border-radius: 8px; padding: 20px; text-align: center;">
                                <h1 style="margin: 0; font-size: 24px; color: #ffffff;">{{ $companyName }}</h1>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 20px 40px 40px 40px;">
                            <h2 style="margin: 0 0 20px 0; font-size: 20px; color: #111827; text-align: center;">Verify Your Email Address</h2>
                            <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 24px; color: #6b7280; text-align: center;">
                                Enter this verification code in the application:
                            </p>
                            
                            <!-- Verification Code -->
                            <div style="background-color: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 24px; text-align: center;">
                                <span style="font-family: monospace; font-size: 32px; font-weight: 700; letter-spacing: 4px; color: #111827;">{{ $verificationCode }}</span>
                            </div>

                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 20px; color: #6b7280; text-align: center;">
                                This code will expire in 60 minutes. If you didn't request this code, you can safely ignore this email.
                            </p>

                            <!-- Divider -->
                            <div style="border-top: 1px solid #e5e7eb; margin: 32px 0;"></div>

                            <!-- Footer -->
                            <p style="margin: 0; font-size: 14px; line-height: 20px; color: #9ca3af; text-align: center;">
                                If you're having trouble with the verification code, please contact our support team.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Company Footer -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="margin-top: 24px;">
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <p style="margin: 0; font-size: 14px; line-height: 20px; color: #6b7280;">
                                © {{ date('Y') }} {{ $companyName }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>