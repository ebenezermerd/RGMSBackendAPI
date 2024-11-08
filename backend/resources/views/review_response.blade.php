<!DOCTYPE html>
<html>
<head>
    <title>Review Request Response</title>
</head>
<body>
    <h1>Review Request Response</h1>
    <p>Proposal Title: {{ $assignment->proposal->title }}</p>
    <p>Start Time: {{ $assignment->start_time }}</p>
    <p>End Time: {{ $assignment->end_time }}</p>
    <p>Status: {{ $assignment->request_status }}</p>

    <form action="{{ url('/review-request/' . $assignment->id . '/response') }}" method="POST">
        @csrf
        <label>
            <input type="radio" name="response" value="accepted" required> Accept
        </label>
        <label>
            <input type="radio" name="response" value="rejected" required> Reject
        </label>
        <textarea name="comment" placeholder="Optional comment"></textarea>
        <button type="submit">Submit</button>
    </form>
</body>
</html>