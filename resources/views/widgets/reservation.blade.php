<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Example App Laravel</title>

        <style>

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap');

        body, html {
        background-color: #ffffff;
        font-family: 'Poppins', sans-serif;
        margin: 0px 0px 0px 0px;
        overflow: hidden;
        }

        body  {
        padding: 16px 16px;
        }

        </style>

    </head>

    <body>

        <div>

            <h1>Hello reservation</h1>

            <p>Reservation ID: {{ $id }}</p>
            <p>Admin ID: {{ $adminId }}</p>
            @if ($chainId)
                <p>Chain ID: {{ $chainId }}</p> 
            @endif

            <p>User Uid: {{ $userUid }}</p>
            <p>Email: {{ $email }}</p>
            <p>Name: {{ $name }}</p>

            <a href="/">Go back</a>

        </div>

    </body>

</html>
