@extends('admin_layouts.app')

<link href="{{ asset('css/admin.css') }}" rel="stylesheet">

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">

        {{-- <div id="upload" class="tab">
            <h2>画像アップロード</h2>
            <form action="{{ route('upload.image') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="image">画像:</label>
                    <input type="file" name="images[]" id="image" required multiple>
                </div>
                <button type="submit" class="btn btn-primary">アップロード</button>
            </form>
        </div>
        
        <!-- カテゴリー追加フォーム -->
        <div id="category" class="tab" style="display: none;">
            <h2>カテゴリー追加</h2>
            <form action="{{ route('categories.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="category-name">カテゴリー名:</label>
                    <input type="text" name="name" id="category-name" required>
                </div>
                <button type="submit" class="btn btn-primary">カテゴリー追加</button>
            </form>
        </div> --}}

        <a href="{{ url('/logout') }}">ログアウト</a>

    </div>

</div>
@endsection
