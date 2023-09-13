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
                <td>販売業社の名称</td>
                <td>GByou株式会社</td>
            </tr>
            <tr>
                <td>所在地</td>
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
                <td>運営統括責任者</td>
                <td>鈴木蒼生</td>
            </tr>
            <tr>
                <td>追加手数料等の追加料金</td>
                <td>・入会手数料　税込11,000円</td>
            </tr>
            <tr>
                <td>交換および返品（返金ポリシー）</td>
                <td>契約の申込みの撤回又は解除に関しましては、原則初月のみと致します。それ以降は毎月20日までに解約の旨を伝え、月末までに当サービスの画像を削除していただくことで解約とさせていただきます。<br></td>
            </tr>
            <tr>
                <td>引渡時期</td>
                <td>契約締結後1〜2営業日で専用のアカウントをこちらで発行し、アカウント情報をお渡し致します。</td>
            </tr>

            <tr>
                <td>受け付け可能な決済手段</td>
                <td>クレジットカードまたはデビットカード</td>
            </tr>
            <tr>
                <td>決済期間</td>
                <td>クレジットカード決済のみになるのでただちに処理されます。</td>
            </tr>

            <tr>
                <td>販売価格</td>
                <td>税込22,000-</td>
            </tr>
        </table>
    </body>
</html>

