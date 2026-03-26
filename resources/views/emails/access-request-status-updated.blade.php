<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #111;">
<div style="max-width: 640px; margin: 0 auto; padding: 24px;">
    <h2 style="margin: 0 0 12px 0; font-size: 18px;">{{ config('app.name') }}</h2>
    <p style="margin: 0 0 12px 0;">{{ $messageText ?? '' }}</p>
    <p style="margin: 0; color: #666; font-size: 12px;">
        This is an automated email. Please do not reply.
    </p>
</div>
</body>
</html>

