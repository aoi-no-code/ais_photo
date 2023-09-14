@extends('admin_layouts.app')

@section('content')


<div id="style" class="tab">
    <h2>カテゴリー整理</h2>
    <form action="{{ route('style.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="category-name">カテゴリー名:</label>
            <input type="text" name="name" id="category-name" autocomplete="off" required>
        </div>
        <button type="submit" class="btn btn-primary">カテゴリー追加</button>
    </form>

    {{-- {{-- <!-- スタイルの一覧を表示 --> --}}
    <h2>スタイル一覧</h2>
    <ul>
        @foreach($styles as $style)
            <li>
                {{ $style->name }}
                
                <!-- 編集用のフォーム -->
                <form action="{{ route('style.update', $style->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $style->name }}" placeholder="新しいスタイル名" required>
                    <button type="submit">更新</button>
                </form>
        
                <!-- 削除ボタン -->
                <form action="{{ route('style.destroy', $style->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">削除</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>

@endsection

