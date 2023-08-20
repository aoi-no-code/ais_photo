<div id="category" class="tab">
    <h2>カテゴリー追加</h2>
    <form action="{{ route('categories.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="category-name">カテゴリー名:</label>
            <input type="text" name="name" id="category-name" required>
        </div>
        <button type="submit" class="btn btn-primary">カテゴリー追加</button>
    </form>

    <!-- カテゴリーの一覧を表示 -->
    <h2>カテゴリー一覧</h2>
    <ul>
        @foreach($categories as $category)
            <li>
                {{ $category->name }}
                
                <!-- 編集用のフォーム -->
                <form action="{{ route('categories.update', $category->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PUT')
                    
                    <input type="text" name="name" value="{{ $category->name }}" placeholder="新しいカテゴリ名" required>
                    
                    <!-- style_id のドロップダウンを追加 -->
                    <select name="style_id" required>
                        <option value="">スタイルを選択してください</option>
                        @foreach($styles as $style)
                            <option value="{{ $style->id }}" {{ $category->style_id == $style->id ? 'selected' : '' }}>{{ $style->name }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit">更新</button>
                </form>
        
                <!-- 削除ボタン -->
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">削除</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
