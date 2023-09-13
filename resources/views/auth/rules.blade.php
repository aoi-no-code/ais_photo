<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <!-- Uncomment the next line if you want to use the Nunito font -->
    {{-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

    <!-- Styles and Scripts -->
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        font-family: 'Baskerville', serif;
    }

    h1 {
        text-align: center;
        margin-bottom: 10px;
        margin-top: 50px;
        font-size: 15px;
        color: black; /* 文字色を黒に */
    }

    table {
        width: 80%;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 100px;
        border-collapse: collapse;
        background-color: white; /* 背景を白に */
    }

    th, td {
        font-size: 10px;
        border: 1px solid #ddd;
        padding: 12px 15px;
        text-align: left;
        color: black; /* 文字色を黒に */
    }

    th {
        background-color: #f2f2f2;
    }
    
    </style>
    </head>


    <body>
        <h1>特定商取引法に基づく表記</h1>

        <table>

            <tr>
                <th>項目</th>
                <th>内容</th>
            </tr>
            <tr>
                <td>販売業者名</td>
                <td>GByou株式会社</td>
            </tr>
            <tr>
                <td>代表者の氏名</td>
                <td>鈴木蒼生</td>
            </tr>
            <tr>
                <td>住所</td>
                <td>東京都千代田区神田佐久間町２１番地５　ヒガシカンダビル３０７号</td>
            </tr>
            <tr>
                <td>電話番号</td>
                <td>070-9195-1549</td>
            </tr>
            <tr>
                <td>メールアドレス</td>
                <td>ai.s.photo.official@gmail.com</td>
            </tr>
            <tr>
                <td>商品代金以外の必要料金</td>
                <td>入会手数料　税込11,000円</td>
            </tr>
            <tr>
                <td>支払方法</td>
                <td>契約時にクレジットカードの登録にて、毎月自動的にお支払いいただきます。</td>
            </tr>
            <tr>
                <td>契約成立のタイミング</td>
                <td>オンラインでの契約締結および支払いのクレジットカード登録になります。</td>
            </tr>
            <tr>
                <td>商品提供時期</td>
                <td>契約締結と同時にアカウントをこちら発行しアカウント情報をお渡し致します。</td>
            </tr>
            <tr>
                <td>サービスの有効期限や条件</td>
                <td>有効期限は解約の申出後、当サービスの商品の掲載削除を確認できた時点で解約と致します。</td>
            </tr>

        </table>
    </body>
</html>

