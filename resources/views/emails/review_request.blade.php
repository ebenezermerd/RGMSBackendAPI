<!DOCTYPE html>
<html>
<head>
    <title>Review Request</title>
</head>
<body>
    <h1>Review Request</h1>
    <p>You have been requested to review the proposal titled "{{ $assignment->proposal->title }}".</p>
    <p>Start Time: {{ $assignment->start_time }}</p>
    <p>End Time: {{ $assignment->end_time }}</p>
    <p>
        <a href="{{ $responseUrl }}">Click here to respond</a>
    </p>
</body>
</html>