@extends('auth.loginApp')

<link href="{{ asset('css/login.css') }}" rel="stylesheet">

@section('content')
    <div class="container-center">
        <div class="bg-container">
            @foreach($images as $index => $image)
                <img src="{{ Storage::disk('s3')->url('images/' . $image->filename) }}" alt="Image" class="bg-image bg-image-{{ $index + 1 }}"> 
            @endforeach
        </div>

        <div class="description">
            <p>撮影はもう必要ない。<br>理想の画像はきっと見つかる</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="">
                <input id="email" type="email" class="email_form input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>


            <div class="">
                <input id="password" type="password" class="password_form input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="">
                <button type="submit" class="login_btn input">Login</button>
            </div>

        </form>
    </div>
@endsection
