<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>成功報名</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="{{ url('/public/css/style.css')}}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
        <style>
            h1{
                padding-top:20%;
                color:#888;
            }
            #successForm{
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
            }
            small{
                padding-top: 10px;
                color:#999;
            }
            body{
                overflow: scroll;
            }
            a{
                height: 50px;
                width: 100px;
                background-color: #2accd5;
                color: #eee;
                text-decoration: auto;
                padding: 12px;
                text-align: center;
                border-radius: 10px;
                margin: 5px;
            }
            a:hover{
                color: #555;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="successForm">
        @csrf
            <h1>God Plus 神家</h1>
            <img src="{{ url('/public/images/logo.png') }}" style="width:60vw;max-width:400px;">
            如想睇返報名, 可以👇<a href="{{ $url }}">CLICK</a><br>
            <small>必為登記電話的whatsapp</small>
        </from>
    </body>
</html>