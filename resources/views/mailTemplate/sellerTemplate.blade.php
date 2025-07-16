<!DOCTYPE html>
<html>
<body>
Welcome to Seller App!<br>
Your account is ready — log in here using:<br><br>

Username: {{$body['username']}}<br>
Password: {{$body['password']}}<br><br>

Login to {{$body['url']}} and Change your password immediately<br><br>

We’re excited to have you with us! Let us know if you need any help.<br><br>

Best,<br>
Homeful<br><br>

    Hi Mr/Mrs/Ms <b></b>,<br><br>

    Please complete your booking with <b>{{$body['seller_name']}}</b> with link below:<br>
    {{$body['matchlink']}}<br><br>
    
    Thank you!<br><br>
    <p>----this is an automated email do not reply----</p>
</body>
</html>