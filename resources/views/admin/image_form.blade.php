@extends('admin_layouts.app')

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

.filter-button {
        text-decoration: underline;
        cursor: pointer;
}

</style>


@section('content')



    <form action="{{ url()->current() }}" method="get">
        <input type="text" name="search" placeholder="画像名で検索">
        <button type="submit">検索</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        <div>

            <button class="mb-3" id="editButton">編集</button>
            <!-- 新着順 -->
            <a href="{{ url()->current() }}?orderBy=created_at&orderDirection=desc">新着順</a>
            <!-- 人気順 -->
            <a href="{{ url()->current() }}?orderBy=download_count&orderDirection=desc">人気順</a>
    
        </div>



        <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>画像</th>

                            @foreach($categories as $category)
                                <th style="font-size: 3px">
                                    <span class="filter-button" onclick="filterByCategory({{ $category->id }})">{{ $category->name }}</span>
                                </th>
                            @endforeach
                                        

                            <th>            
                                <form action="{{ route('images.updateAllCategories') }}" method="post">
                                <button type="submit" style="display: none;" class="saveButton">一括更新</button>
                                @csrf
                                @method('PUT')
                            </th> <!-- 新しい列のヘッダーを追加 -->
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
                                    
                                    <button type="button" class="btn btn-link" onclick="deleteImage('{{ $image->filename }}')">削除</button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

<script>
    function setEditButtonListener() {
        const editButton = document.getElementById('editButton');
        if (editButton) {
            editButton.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.checkboxContainer input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.style.display = 'inline-block';
                });

                const checkmarks = document.querySelectorAll('.category-checkmark');
                checkmarks.forEach(checkmark => {
                    checkmark.style.display = 'none';
                });

                const saveButtons = document.querySelectorAll('.saveButton');
                saveButtons.forEach(btn => {
                    btn.style.display = 'inline-block';
                });
                this.style.display = 'none';
            });
        }
    }

    function showImage(src) {
        document.getElementById('modalImage').src = src;
        $('#imageModal').modal('show');
    }

    function filterByCategory(categoryId) {
        let url = new URL(window.location.href);
        url.searchParams.set('category', categoryId);
        window.location.href = url.toString();
    }


    function deleteImage(filename) {
    // ユーザーに確認を求める
    if(confirm('本当にこの画像を削除しますか？')) {
        // 確認後、削除処理を行う
        $.ajax({
            url: 'image/delete/' + filename,
            type: 'DELETE',
            data: {filename: filename},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('画像が削除されました');
                // ここでDOMから画像を削除するコードを追加できます。
            },
            error: function(error) {
                alert('画像の削除に失敗しました');
            }
        });
    } else {
        // ユーザーが「キャンセル」を選んだ場合、何もしない
    }
}


        // 初回読み込み時にsetEditButtonListenerを呼び出す
    window.addEventListener('load', setEditButtonListener);

</script>

@endsection
