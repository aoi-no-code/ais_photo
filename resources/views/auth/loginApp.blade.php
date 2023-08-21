<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    



    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>AI's photo【美容師向けAI画像ダウンロードし放題サブスクサービス】</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    {{-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

    <!-- Scripts -->
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>


</head>
<style>
    /* ベースのスタイル */

    body, html {
        overflow: hidden; /* スクロールを無効にする */
        height: 100%;     /* ページの高さを100%に設定 */
    }

    body {
        background-color: #151515; /* 黒の背景色 */
        overflow: hidden;
        height: 100vh;
        position: relative;
        font-family: 'Baskerville', serif;
    }
    
</style>

<body>
    @yield('content')
</body>


</html>
