<!DOCTYPE html>
<html>
<head>
    <title>Fund Request Approved</title>
</head>
<body>
    <h1>Fund Request Approved</h1>
    <p>Dear {{ $fundRequest->user->first_name }},</p>
    <p>Your fund request for the activity "{{ $fundRequest->activity->activity_name }}" has been approved.</p>
    <p>Amount: {{ $fundRequest->request_amount }} birr</p>
    <p>Thank you for your submission.</p>
</body>
</html>