<!DOCTYPE html>
<html>
<head>
    <title>New Meeting Scheduled</title>
</head>
<body>
    <h2>Policy Management Information System</h2>
    
    <p>Dear Council Members,</p>
   
    <p>We would like to inform you that the Submission schedule and Meeting Date of the proposals for the <strong>{{ $quarter }}</strong>, <strong>{{ $council_type }}</strong>.</p>


    <ul>
        <li><strong>Description:</strong> {{ $description }}</li>
        <li><strong>Date & Time:</strong> {{ $date }}</li>
        <li><strong>Submission Start:</strong> {{ $submission_start }}</li>
        <li><strong>Submission End:</strong> {{ $submission_end }}</li>

        @if ($modality == 1)  {{-- 1 = Face-to-Face --}}
        <li><strong>Venue:</strong> {{ $venue }}</li>
        @elseif ($modality == 2)  {{-- 2 = Online --}}
            <li><strong>Online Meeting Link:</strong> <a href="{{ $link }}">{{ $link }}</a></li>
        @endif
    </ul>
    <p>Please take note of the schedule and ensure you participate.</p>
    <p>Best regards,</p>
    <p><strong>Meeting Organizer</strong></p>
</body>
</html>
