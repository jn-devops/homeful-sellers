<!DOCTYPE html>
<html>
<body>
Hi {{$body['seller_name']}}!<br><br>
Welcome to Seller App!<br>
Your account is ready — log in using:<br><br>


Seller Web App: {{$body['sellerURL']}}<br><br>
Username: {{$body['username']}}<br>
Password: {{$body['password']}}<br><br>

or
<br><br>
Click <a href="{{$body['url']}}">here</a> to login.

change your password immediately.<br><br>
<!-- Login to  and Change your password immediately<br><br> -->

We’re excited to have you with us! Let us know if you need any help.<br><br>

Best,<br>
Homeful<br><br>
</body>
</html>