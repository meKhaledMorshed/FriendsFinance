<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Hello {{ $receiverName }} !</h1>
    <p>Your account successfully created. Please wait for account authorization and we will notify you throw this email.</p>
    <p>After authorization please use below temporary password to login your account and change it immediately. </p>
    <p>Your temporary Password is <b>{{ $token }}</b></p>
    </br> </br> </br>
    <p>With Regards</p>
    <p><b>{{ $senderName }}</b></p>

</body>

</html>