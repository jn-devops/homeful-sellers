<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $body['subject'] ?? 'Freeform Email' }}</title>
</head>
<body>
    <h2>Hello {{ $body['name'] ?? 'User' }}</h2>
    <p>{{ $body['message'] ?? 'No message provided.' }}</p>
</body>
</html>
