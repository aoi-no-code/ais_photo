<div id="upload" class="tab">
    <h2>画像アップロード</h2>
    <form action="{{ route('upload.image') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="image">画像:</label>
            <input type="file" name="images[]" id="image" required multiple>
        </div>
        <button type="submit" class="btn btn-primary">アップロード</button>
    </form>

    <h2>画像の一覧とカテゴリ編集</h2>
    <button id="editButton">編集</button>
    <form action="{{ route('images.updateAllCategories') }}" method="post">
        @csrf
        @method('PUT')
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>画像</th>
                    @foreach($categories as $category)
                        <th>{{ $category->name }}</th>
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
        <button type="submit" style="display: none;" class="saveButton">一括更新</button>
    </form>
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