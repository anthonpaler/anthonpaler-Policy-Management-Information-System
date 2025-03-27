<!DOCTYPE html>
<html>
<head>
    <title>Order of Business Dissemination</title>
</head>
<body>
    <p>Dear Council Members,</p>

    <p>Good day!</p>

    <p>The Provisional agenda (Order of Business) for the <strong>{{ config('meetings.quaterly_meetings')[$meeting->quarter] ?? '' }} – {{ $council_type }}</strong> 
       this <strong>{{ \Carbon\Carbon::parse($meeting->meeting_date_time)->format('F j, Y \a\t g:i A') }}</strong> is now available.</p>

    <p>Please visit this link: <a href="https://policy.southernleytestateu.edu.ph">policy.southernleytestateu.edu.ph</a> for further information.</p>

    <p>Thank you.</p>
</body>
</html>
