<!DOCTYPE html>
<html>
<head>
    <title>Review Request</title>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 0;
        }
        .button-accept {
            background-color: #28a745;
        }
        .button-reject {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <h1>Review Request from {{ $coe }}</h1>
    <p>You have been requested to review the proposal titled "{{ $assignment->proposal->proposal_title }}".</p>
    <p><strong>Start Time:</strong> {{ $assignment->start_time }}</p>
    <p><strong>End Time:</strong> {{ $assignment->end_time }}</p>
    <p>Please click one of the buttons below to respond:</p>
    <a href="{{ $responseUrl }}?response=accepted" class="button button-accept">Accept</a>
    <a href="{{ $responseUrl }}?response=rejected" class="button button-reject">Reject</a>
</body>
</html>