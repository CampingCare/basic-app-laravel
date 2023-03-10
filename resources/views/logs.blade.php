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

            <h1>Logs</h1>

            @foreach ($logs as $log)
                <pre><?php print_r(json_decode(json_encode($log))) ?></pre>
            @endforeach

            <a href="/">Back</a>  -  <a href="/logs?action=clear">Clear logs</a> 
            
            <script src="{{ asset('js/widgets.js')}}"></script>

        </div>

    </body>

</html>
