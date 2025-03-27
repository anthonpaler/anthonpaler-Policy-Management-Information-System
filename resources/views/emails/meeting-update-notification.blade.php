<!DOCTYPE html>
<html>
<head>
    <title>Meeting Update Notification</title>
</head>
<body>
    <p>Dear Council Member,</p>
    
    <p>We would like to inform you that some details of the <strong>{{ $quarter }}</strong>, <strong>{{ $council_type }} {{ $year }} meeting has been modified:</p>

    <ul>
        @foreach($updatedFields as $field => $values)
            <li><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                <br>
                <br> <span style="color: red;">Before: {{ $values['before'] ?? 'N/A' }}</span>
                <br>
                <br> <span style="color: green;">After: {{ $values['after'] ?? 'N/A' }}</span>
            
            </li>
        @endforeach
    </ul>

    <p>For more details, visit: <a href="https://policy.southernleytestateu.edu.ph">Policy Management Information System</a></p>

    <p>Thank you.</p>
</body>
</html>
