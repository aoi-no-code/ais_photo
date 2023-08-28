@extends('layouts.app')

<link href="{{ asset('css/main.css') }}" rel="stylesheet">

@section('content')
    <div class="main body-no-scroll">

        <div class="totalCount" >
            <p class="total-text">全掲載枚数：{{ $totalImagesCount }}枚</p>
        </div>


        <div class="filter-dropdown">
            <a href="#" class="filter-text" role="button" id="categoryDropdown">
                フィルター
            </a>
        </div>
    
        <div id="noMatchMessage" style="display: none;">
            条件に一致する画像が見つかりませんでした。
        </div>

        <div class="image-container">
            @foreach($images as $image)
                <div class="image-sub-container">
                    <div class="image-wrapper" 
                            data-filename="{{ $image->filename }}" 
                            data-downloadcount="{{ $image->download_count }}"
                            data-createdat="{{ $image->created_at }}"
                            data-category="{{ $image->categories->pluck('name')->implode(',') }}">
                        <a href="{{ route('image.download', $image->filename) }}">
                            <img src="{{ Storage::disk('s3')->url('images/' . $image->filename) }}" alt="Image" class="image">
                        </a>
                        <span class="download-count">
                            <i class="bi bi-download"></i> 
                            {{ $image->download_count }}
                        </span>
                    </div>   
                </div>
            @endforeach
        </div>
        <div class="fullscreen-modal" id="fullscreenModal">
            <img src="" id="fullscreenImage" style="display: none;">
        </div>

        
        <div class="filter-modal" id="filterModal">
            
            {{-- <button id="sortByDate">新着順</button>
            <button id="sortByDownloads">ダウンロード数順</button> --}}
            
            @foreach($sortedStyles as $sortedStyle)
                <h2 class="category-title">{{ $sortedStyle->name }}</h2>
                <div>
                    @foreach($sortedStyle->categories as $category)
                        <label class="category-item-wrapper">
                            @if($category->name == 'Mens')
                                <input type="checkbox" name="categories[]" value="{{ $category->name }}" class="hidden-checkbox" disabled>
                                <span class="category-item">{{ $category->name }}（現在開発中）</span>
                            @else
                                <input type="checkbox" name="categories[]" value="{{ $category->name }}" class="hidden-checkbox">
                                <span class="category-item">{{ $category->name }}</span>
                            @endif                        
                        </label>                    
                    @endforeach
                </div>
            @endforeach
            
            <button id="closeFilterModal">exit</button>
        </div>
        
        <div class="notification-container">
            <div id="loadingIndicator" class="notification-message" style="display: none;">Loading...</div>
            <div id="endMessage" class="notification-message" style="display: none;">
                只今順次作成中です<br>少々お待ちください
            </div>
        </div>
    </div>


@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const baseUrl = "{{ Storage::disk('s3')->url('images/') }}";

    function attachModalForImage(imgElement) {
        imgElement.parentElement.addEventListener('click', function(e) {
            e.preventDefault();

            // モーダルを表示
            const modal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');
            fullscreenImage.src = imgElement.src;
            modal.style.display = 'flex';
            fullscreenImage.style.display = 'flex';

            // filenameを取得
            const url = imgElement.src;
            const filename = url.split('/').pop();

            if(filename) {
                // ダウンロードカウントをインクリメントするAjaxリクエスト
                $.ajax({
                    url: `/increment-download-count/${filename}`,
                    type: 'GET',
                    success: function(data) {
                        // インクリメント成功後の処理（必要であれば）
                    },
                    error: function() {
                        // インクリメント失敗後の処理（必要であれば）
                    }
                });
            } else {
                console.log("Filename could not be extracted.");
            }
        });
    }

    // 独立したモバイルデバイス用のコード
    if (isMobile) {
        let images = document.querySelectorAll('.image');
        const modal = document.getElementById('fullscreenModal');
        const fullscreenImage = document.getElementById('fullscreenImage');

        // モーダルを閉じるためのイベントリスナー
        modal.addEventListener('click', function() {
            this.style.display = 'none';
            document.body.classList.remove('body-no-scroll');
            fullscreenImage.style.display = 'none';
        });

        images.forEach(img => {
            attachModalForImage(img);
        });
    }











    // fotterが表示されたら読み込むようにしている
    let isLoading = false;
    let noMoreImages = false;
    $(window).on('scroll', function() {
        if (!isLoading && !noMoreImages && $(window).scrollTop() + $(window).height() > $(document).height() - $("#footer").height()) {
            loadMoreImages();
        }
    });

    // カテゴリのチェックボックス要素を取得
    const categoryItems = document.querySelectorAll('.category-item');
    // カテゴリ項目をクリックした際のフィルタリング処理

    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('selected');

            const selectedCategories = [];
            document.querySelectorAll('.category-item.selected').forEach(selectedItem => {
                selectedCategories.push(selectedItem.textContent.trim());
            });

            // selectedCategory変数を更新
            selectedCategory = selectedCategories.join(',');

            // 例：Ajaxでサーバにデータを送る部分
            $.ajax({
                url: '/fetch-images',  // コントローラーのURL
                method: 'GET',
                data: {
                    categoryName: selectedCategory,  // 選択されたカテゴリ
                    // 他のパラメーター（例：offset, sortなど）
                },
                success: function(response) {
                    addImagesToDOM(response.images, true);  // 既存の画像を削除して新しい画像を追加

                    const totalImagesElement = document.querySelector('.total-text');
                    totalImagesElement.textContent = `検索結果：${response.totalImages}枚`;

                    checkVisible();
                }
            });
        });
    });
    

    // 新しく既存の画像を削除してから追加、または削除せずに追加
    function addImagesToDOM(images, clearExisting = false) {
        const imageContainer = document.querySelector('.image-container');

        if (clearExisting) {
            while (imageContainer.firstChild) {
                imageContainer.removeChild(imageContainer.firstChild);
            }
        }

        images.forEach(function(imageData) {
            // メインのdiv要素を作成
            const newImageSubContainer = document.createElement('div');
            newImageSubContainer.className = "image-sub-container";
            
            // 画像を囲むdiv要素を作成
            const imageWrapper = document.createElement('div');
            imageWrapper.className = "image-wrapper";
            imageWrapper.setAttribute("data-filename", imageData.filename);

            // ダウンロードリンクを作成
            const downloadLink = document.createElement('a');
            downloadLink.href = `/image/${imageData.filename}`; // Laravelのルート設定に基づいて

            // img要素を作成
            const imgElement = document.createElement('img');
            imgElement.src = baseUrl + imageData.filename;  // baseUrlは外部で定義されていると仮定
            imgElement.className = "image";
            
            // download-count要素を作成
            const downloadCountSpan = document.createElement('span');
            downloadCountSpan.className = "download-count";
            
            const downloadIcon = document.createElement('i');
            downloadIcon.className = "bi bi-download";

            const downloadCountText = document.createTextNode(` ${imageData.download_count}`);
            downloadCountSpan.appendChild(downloadIcon);
            downloadCountSpan.appendChild(downloadCountText);

            // すべてをまとめる
            downloadLink.appendChild(imgElement);
            imageWrapper.appendChild(downloadLink);
            imageWrapper.appendChild(downloadCountSpan);
            newImageSubContainer.appendChild(imageWrapper);
            
            // メインのコンテナに追加
            imageContainer.appendChild(newImageSubContainer);
        });
    }



    let offset = 20; // 初期値は20ですが、初めから何枚か画像が表示されている場合はその数に設定
    const limit = 20; // 一度に取得する画像の数

    function loadMoreImages() {
        // 現在読み込み中であることを示す
        isLoading = true;

        const selectedCategories = [];
        document.querySelectorAll('.category-item.selected').forEach(selectedItem => {
            selectedCategories.push(selectedItem.textContent.trim());
        });

        // selectedCategory変数を更新
        selectedCategory = selectedCategories.join(',');

        $.ajax({
            url: '/fetch-images',  // サーバーサイドのエンドポイント
            method: 'GET',
            data: {
                categoryName: selectedCategory,  // 選択されたカテゴリ
                offset: offset  // オフセット（すでに読み込んだ画像の数）
            },
            success: function(response) {
                // 画像がない場合はフラグをセット
                if (response.images.length === 0) {
                    noMoreImages = true;
                    return;
                }

                // 画像をDOMに追加
                addImagesToDOM(response.images);

                // オフセットを更新
                offset += limit;

                // 画像が表示されたかどうかを確認
                checkVisible();
            },
            complete: function() {
                // 読み込みが完了したのでフラグをリセット
                isLoading = false;
            }
        });
    }



    // // 画像要素を取得
    // const images = document.querySelectorAll('.image-container .image');

    // // カテゴリに基づいて画像をフィルタリングする関数
    // function filterImages(selectedCategories) {
    //     const images = document.querySelectorAll('.image-wrapper');

    //     let matchFound = false; // マッチする画像が見つかったかどうかのフラグ
    //     const notificationContainer = document.querySelector('.notification-container');
    //     const noMatchMessage = document.getElementById('noMatchMessage');

    //     // カテゴリが選択されていない場合の処理
    //     if (selectedCategories.length === 0) {
    //         images.forEach(img => {
    //             img.style.display = 'block'; // すべての画像を表示
    //         });
    //         noMatchMessage.style.display = 'none';
    //         notificationContainer.style.display = 'block'; // 再度表示する
    //         return;
    //     }

    //     // カテゴリにマッチする画像を表示する処理
    //     images.forEach(img => {
    //         const dataCategory = img.getAttribute('data-category');
    //         const imgCategories = dataCategory ? dataCategory.split(',') : [];

    //         if (selectedCategories.every(cat => imgCategories.includes(cat))) {
    //             img.style.display = 'block';
    //             matchFound = true;
    //         } else {
    //             img.style.display = 'none';
    //         }
    //     });

    //     // マッチする画像が一つもない場合の処理
    //     if (!matchFound) {
    //         noMatchMessage.style.display = 'block';
    //         notificationContainer.style.display = 'none'; // ここで再度表示する必要があるか確認してください。
    //     } else {
    //         noMatchMessage.style.display = 'none';
    //         notificationContainer.style.display = 'block'; // 他の条件下で再表示するためのロジック
    //     }
    // }

    // 画像追加で読み込む

    // function loadMoreImages() {
    //     isLoading = true;
    //     $.ajax({
    //         url: `/fetch-images`,
    //         type: 'GET',
    //         data: {
    //             offset: offset,
    //             limit: limit,
    //             categoryName: selectedCategory,
    //         },
    //         success: function(data) {
    //             if (data.images && data.images.length > 0) {
    //                 data.images.forEach(image => {
    //                     const imageUrl = `{{ Storage::disk('s3')->url('images/') }}${image.filename}`;
    //                     const downloadLink = `{{ route('image.download', '') }}/${image.filename}`;

    //                     const imageElement = document.createElement('div');
    //                     imageElement.className = "image-wrapper";
    //                     imageElement.innerHTML = `
    //                         <a href="${downloadLink}">
    //                             <img src="${imageUrl}" alt="Image" class="image">
    //                         </a>
    //                     `;

    //                     // loadMoreImages内の関連部分
    //                     if (isMobile) {
    //                         const imgElement = imageElement.querySelector('img');
    //                         attachModalForImage(imgElement);
    //                     }
                        
    //                     // ダウンロード数の表示のための要素を作成
    //                     const downloadCountSpan = document.createElement('span');
    //                     downloadCountSpan.className = "download-count";

    //                     const downloadIcon = document.createElement('i');
    //                     downloadIcon.className = "bi bi-download";
    //                     const downloadCountText = document.createTextNode(` ${image.download_count}`);
                        
    //                     downloadCountSpan.appendChild(downloadIcon);
    //                     downloadCountSpan.appendChild(downloadCountText);
    //                     imageElement.appendChild(downloadCountSpan);
                        
    //                     // 画像をコンテナに追加
    //                     $('.image-container').append(imageElement);
    //                 });
                    
    //                 offset += data.images.length;
    //             } else {
    //                 $("#endMessage").show();
    //                 noMoreImages = true;
    //             }
    //         },
    //         complete: function() {
    //             isLoading = false;
    //         }
    //     });
    // }

    // // イベントハンドラを設定
    // categoryCheckboxes.forEach(checkbox => {
    //     checkbox.addEventListener('change', function() {
    //         if (this.checked) {
    //             selectedCategories.push(this.value);
    //         } else {
    //             const index = selectedCategories.indexOf(this.value);
    //             if (index > -1) {
    //                 selectedCategories.splice(index, 1);
    //             }
    //         }

    //         // Ajaxでサーバに送信
    //         $.ajax({
    //             url: '/fetch-images', // コントローラーのURL
    //             method: 'GET',
    //             data: {
    //                 categories: selectedCategories.join(',') // 配列をカンマ区切りの文字列に
    //             },
    //             success: function(response) {
    //                 // 画像の更新処理（こちらは前述の通り）
    //             }
    //         });
    //     });
    // });

    

    














    // 【フィルター】の文字の固定
    const filterText = document.querySelector('.filter-text');
    const filterTextInitialTop = filterText.getBoundingClientRect().top + window.scrollY;

    $(window).on('scroll', function() {
        // 現在のスクロール位置がfilterTextの初期位置から26.5pxを減算した位置を超えているかどうかを判定します
        if (window.scrollY >= filterTextInitialTop - 26.5) {
            // filterTextの位置をfixedにして、ページのトップから26.5pxの位置に固定します
            filterText.style.position = 'fixed';
            filterText.style.top = '26.5px';
        } else {
            // 現在のスクロール位置がfilterTextの初期位置から26.5pxを減算した位置を超えていない場合、
            // filterTextを元の位置に戻します
            filterText.style.position = 'absolute';
            filterText.style.top = 'initial';
        }
    });

    // カテゴリフィルタのモーダルを制御する部分
    const filterButton = document.getElementById('categoryDropdown');
    const filterModal = document.getElementById('filterModal');
    const closeModalButton = document.getElementById('closeFilterModal');

    filterButton.addEventListener('click', function(e) {
        e.preventDefault();
        filterModal.classList.add('show');
        // bodyにクラスを追加してスクロールを無効にする
        document.body.classList.add('body-no-scroll');
    });

    closeModalButton.addEventListener('click', function() {
        filterModal.classList.remove('show');
        // bodyのクラスを削除してスクロールを有効にする
        document.body.classList.remove('body-no-scroll');
    });

    const imageWrappers = document.querySelectorAll('.image-wrapper');

    // 画像が画面内に表示されているかのチェック関数
    function checkVisible() {
        // ここで毎回 .image-wrapper 要素を取得します
        const imageWrappers = document.querySelectorAll('.image-wrapper');

        imageWrappers.forEach(wrapper => {
            const rect = wrapper.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                wrapper.style.opacity = "1";
                wrapper.style.transform = "translateY(0)";
            }
        });
    }
    checkVisible();

    // スクロールの度に表示チェックを行う
    window.addEventListener('scroll', checkVisible);
});
</script>
    
