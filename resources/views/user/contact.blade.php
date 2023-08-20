@extends('layouts.app')

<link href="{{ asset('css/contact.css') }}" rel="stylesheet">

@section('content')
<div class="contact-container">
    <h1 class="contact-title">作成依頼フォーム</h1>

    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ route('submitContact') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">名前</label>
            <input type="text" id="name" name="name" placeholder="美容　好子" required>
        </div>

        <div>
            <label for="name">フリガナ</label>
            <input type="text" id="hurigana" name="hurigana"  placeholder="ビヨウ　ヨシコ" required>
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email"  placeholder="example@email.com" required>
        </div>

        <div>
            <label for="phone">電話番号</label>
            <input type="tel" id="phone" name="phone" placeholder="000-0000-0000" required>
        </div>

        <div>
            <label for="referenceImage">参考画像（スクリーンショットなど）</label>
            <input type="file" id="referenceImage" name="referenceImage" accept="image/*" required>
        </div>

        <div>
            <label for="imageURL">参考画像引用URL</label>
            <input type="url" id="imageURL" name="imageURL" required>
        </div>

        <div>
            <button type="submit">送信</button>
        </div>
    </form>
</div>

@endsection

