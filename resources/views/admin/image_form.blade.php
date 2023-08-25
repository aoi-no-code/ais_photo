<style>
    /* スタイルシート（CSS） */
.table-wrapper {
  position: relative;
  max-height: 500px;  /* この高さを超えるとスクロールが有効になります */
  overflow-y: auto;   /* 縦方向にスクロール可能にする */
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table thead th {
  position: sticky;
  top: 0;  /* スクロールしてもこの位置に固定 */
  background-color: #f1f1f1;  /* 背景色を設定（任意） */
}

</style>

<div id="upload" class="tab">
    <div style="display: flex">
        <form action="{{ route('upload.image') }}" method="post" enctype="multipart/form-data" style="display: flex">
            @csrf
            <div class="form-group">
                <label for="image">画像:</label>
                <input type="file" name="images[]" id="image" required multiple>
            </div>
            <button type="submit" class="btn btn-primary">アップロード</button>
        </form>
    </div>

    <button id="editButton">編集</button>
    <div class="table-wrapper">
        <form action="{{ route('images.updateAllCategories') }}" method="post">
            <button type="submit" style="display: none;" class="saveButton">一括更新</button>
            @csrf
            @method('PUT')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>画像</th>
                        @foreach($categories as $category)
                            <th style="font-size: 3px">{{ $category->name }}</th>
                        @endforeach
                        <th>操作</th> <!-- 新しい列のヘッダーを追加 -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($images as $image)
                        <tr>
                            <td><img src="{{ Storage::disk('s3')->url('images/' . $image->filename) }}" alt="画像" onclick="showImage(this.src)" style="aspect-ratio:4 / 5; object-fit: cover; height: 50px; width: auto;"></td>
                            @foreach($categories as $category)
                                <td class="checkboxContainer">
                                    <input type="checkbox" name="category_image[{{ $image->id }}][categories][]" value="{{ $category->id }}" {{ $image->categories->contains($category->id) ? 'checked' : '' }} style="display: none;">
                                    @if($image->categories->contains($category->id))
                                        <span class="category-checkmark">◯</span>  
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                
                                <button onclick="deleteImage({{ $image->id }})">削除</button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $images->links() }}
        </form>
    </div>
    <!-- モーダルの構造 -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
            <img id="modalImage" src="" alt="画像" style="width:100%;">
            </div>
        </div>
        </div>
    </div>
</div>