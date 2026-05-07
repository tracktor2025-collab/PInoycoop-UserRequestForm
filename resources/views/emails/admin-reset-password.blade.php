<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:#eef4f8; font-family:Arial, Helvetica, sans-serif; color:#20304f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#eef4f8; margin:0; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background-color:#ffffff; border:1px solid #dbe5ee; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="padding:36px 40px 18px; text-align:center; background:linear-gradient(180deg, #f8fbfd 0%, #ffffff 100%);">
                            <div style="margin:0 0 8px; color:#00a7e1; font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase;">
                                MASS-SPECC Cooperative Development Center
                            </div>
                            <h1 style="margin:0; font-size:30px; line-height:1.2; color:#20304f;">Reset Your Password</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 40px 40px;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#52637f;">
                                We received a request to reset the password for your {{ $accountLabel ?? 'admin account' }}.
                            </p>
                            <p style="margin:0 0 24px; font-size:16px; line-height:1.7; color:#52637f;">
                                Use the button below to choose a new password. This reset link will expire in <strong>{{ $expire }} minutes</strong>.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 28px;">
                                <tr>
                                    <td align="center" bgcolor="#00a7e1" style="border-radius:10px;">
                                        <a href="{{ $actionUrl }}" style="display:inline-block; padding:14px 28px; color:#ffffff; text-decoration:none; font-size:16px; font-weight:700;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#52637f;">
                                If you did not request a password reset, you can safely ignore this email.
                            </p>
                            <div style="margin:28px 0 0; padding-top:24px; border-top:1px solid #e6edf4; font-size:13px; line-height:1.7; color:#6b7c95;">
                                If the button does not work, copy and paste this link into your browser:
                                <br>
                                <a href="{{ $actionUrl }}" style="color:#0b80bb; word-break:break-all;">{{ $actionUrl }}</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
