<link href="{{ asset('css/header.css') }}" rel="stylesheet">

<!-- ヘッダー開始 -->
<header class="header">
    <!-- ナビゲーションバー開始 -->
    <nav class="fixed-top">
        <!-- コンテナ開始：中央に配置するためのスタイルが適用されています -->
        <div class="container">
            <!-- ブランドロゴ: サイトのホームへのリンク -->
            <a class="navbar-brand" href="{{ url('/') }}">AI's photo</a>
            
            <!-- モバイル用のナビゲーションボタン -->
            <button class="custom-toggler" type="button">
                <span class="navbar-toggler-icon">
                    <i class="bi bi-list"></i>
                </span>
            </button>

            <!-- ナビゲーションバーのメニュー部分開始 -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <!-- 右側のナビゲーションメニュー開始 -->
                <ul class="navbar-nav">
                    
                    <!-- ゲストユーザーの場合のメニュー項目 -->
                    @guest
                        <!-- ログインのリンクがある場合は表示 -->
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        <!-- 登録のリンクがある場合は表示（現在はコメントアウトされている） -->
                        {{-- @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif --}}
                    
                    <!-- 認証済みのユーザーの場合のメニュー項目 -->
                    @else
                        <!-- ドロップダウンメニュー：ユーザー名で表示 -->
                        {{-- <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <!-- ドロップダウンの内容開始 -->
                            <div class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="navbarDropdown">
                                <!-- ログアウトのリンク -->
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <!-- ログアウトの実際の処理用のフォーム（非表示） -->
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div> <!-- ドロップダウンの内容終了 -->
                        </li> --}}
                    @endguest

                </ul> <!-- 右側のナビゲーションメニュー終了 -->
            </div> <!-- ナビゲーションバーのメニュー部分終了 -->
        </div>
    </nav> <!-- ナビゲーションバー終了 -->
</header> <!-- ヘッダー終了 -->
