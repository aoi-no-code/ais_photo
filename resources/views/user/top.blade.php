@extends('layouts.app')

<link href="{{ asset('css/main.css') }}" rel="stylesheet">

@section('content')
    <div class="main body-no-scroll">

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
        @endforeach
                    
            <div class="fullscreen-modal" id="fullscreenModal">
                <img src="" id="fullscreenImage">
            </div>
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


@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    function checkAndAddWrapper() {
        const container = document.querySelector('.image-container');
        const images = container.querySelectorAll('img');

    }





    // document.getElementById('sortByDate').addEventListener('click', function() {
    //     images.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    //     displayImages(images);
    // });

    // document.getElementById('sortByDownloads').addEventListener('click', function() {
    //     images.sort((a, b) => b.downloads - a.downloads);
    //     displayImages(images);
    // });

    // function displayImages(imageArray) {
    // const container = document.getElementById('imageContainer');
    // container.innerHTML = '';

    // imageArray.forEach(image => {
    //     const imgElem = document.createElement('img');
    //     imgElem.src = image.url;
    //     container.appendChild(imgElem);
    // });
    // }










    let isLoading = false;  // ロード中かどうかを示すフラグ
    let noMoreImages = false;  // これ以上ロードする画像がないかどうかを示すフラグ

    $(window).on('scroll', function() {
        if (!isLoading && !noMoreImages && $(window).scrollTop() + $(window).height() > $(document).height() - $("#footer").height()) {
            loadMoreImages();
        }
    });

    let offset = 20;
    const limit = 20;
    let selectedCategory = '';

    function loadMoreImages() {
        isLoading = true;
        $.ajax({
            url: `/fetch-images`,
            type: 'GET',
            data: {
                offset: offset,
                limit: limit,
                categoryName: selectedCategory
            },
            beforeSend: function() {
                $("#loadingIndicator").show();
            },
            success: function(data) {
                if (data.images && data.images.length > 0) {
                    data.images.forEach(image => {
                        const imageUrl = `{{ Storage::disk('s3')->url('images/') }}${image.filename}`;
                        const downloadLink = `{{ route('image.download', '') }}/${image.filename}`;

                        const imageElement = document.createElement('div');
                        imageElement.className = "image-wrapper";
                        imageElement.innerHTML = `
                            <a href="${downloadLink}">
                                <img src="${imageUrl}" alt="Image" class="image">
                            </a>
                        `;

                        // モバイルの場合の処理
                        if (isMobile) {
                            const imgElement = imageElement.querySelector('img');
                            imgElement.parentElement.addEventListener('click', function(e) {
                                e.preventDefault();
                                fetch(this.href, {
                                    method: 'GET',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    if(response.ok) {
                                        const modal = document.getElementById('fullscreenModal');
                                        const fullscreenImage = document.getElementById('fullscreenImage');
                                        fullscreenImage.src = imgElement.src;
                                        modal.style.display = 'flex';

                                        modal.addEventListener('click', function() {
                                            this.style.display = 'none';
                                        });
                                    }
                                });
                            });
                        }
                        console.log("loadMoreImages called");

                        // ダウンロード数の表示のための要素を作成
                        const downloadCountSpan = document.createElement('span');
                        downloadCountSpan.className = "download-count";

                        const downloadIcon = document.createElement('i');
                        downloadIcon.className = "bi bi-download";
                        const downloadCountText = document.createTextNode(` ${image.download_count}`);
                        
                        downloadCountSpan.appendChild(downloadIcon);
                        downloadCountSpan.appendChild(downloadCountText);
                        imageElement.appendChild(downloadCountSpan);
                        
                        // 画像をコンテナに追加
                        $('.image-container').append(imageElement);
                    });
                    
                    offset += data.images.length;
                } else {
                    $("#endMessage").show();
                    noMoreImages = true;  // これ以上ロードする画像がない場合はフラグをtrueに設定 
                }
            },
            complete: function() {
                $("#loadingIndicator").hide();
                isLoading = false;  // ロード完了時にフラグをfalseに変更
            }
        });
    }

    
    // 画像要素を取得
    const images = document.querySelectorAll('.image-container .image');

    // カテゴリに基づいて画像をフィルタリングする関数
    function filterImages(selectedCategories) {
        const images = document.querySelectorAll('.image-wrapper');
        let matchFound = false; // マッチする画像が見つかったかどうかのフラグ

        // カテゴリが選択されていない場合の処理
        if (selectedCategories.length === 0) {
            images.forEach(img => {
                img.style.display = 'block'; // すべての画像を表示
            });
            document.getElementById('noMatchMessage').style.display = 'none';
            return;
        }

        // カテゴリにマッチする画像を表示する処理
        images.forEach(img => {
            const dataCategory = img.getAttribute('data-category');
            const imgCategories = dataCategory ? dataCategory.split(',') : [];

                if (selectedCategories.every(cat => imgCategories.includes(cat))) {
                img.style.display = 'block';
                matchFound = true;
            } else {
                img.style.display = 'none';
            }
        });

        // マッチする画像が一つもない場合の処理
        if (!matchFound) {
            document.getElementById('noMatchMessage').style.display = 'block';
        } else {
            document.getElementById('noMatchMessage').style.display = 'none';
        }
    }
    

    // モバイルデバイスで画像をクリックした時のモーダル表示処理
    if (isMobile) {
        images.forEach(img => {
            img.parentElement.addEventListener('click', function(e) {
                e.preventDefault();

                // 画像情報を取得
                fetch(this.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if(response.ok) {
                        const modal = document.getElementById('fullscreenModal');
                        const fullscreenImage = document.getElementById('fullscreenImage');
                        fullscreenImage.src = img.src;
                        modal.style.display = 'flex';
                        document.body.classList.add('body-no-scroll');
                    
                        
                        modal.addEventListener('click', function() {
                            this.style.display = 'none';
                            document.body.classList.remove('body-no-scroll');
                        });
                    }
                });
            });
        });
    }

    // filterTextの要素を取得します
    const filterText = document.querySelector('.filter-text');

    const filterTextInitialTop = filterText.getBoundingClientRect().top + window.scrollY;

    // windowの'scroll'イベントに関数をバインドします
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

    // カテゴリ項目をクリックした際のフィルタリング処理
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.toggle('selected');
            
            const selectedCategories = [];
            document.querySelectorAll('.category-item.selected').forEach(selectedItem => {
                selectedCategories.push(selectedItem.textContent.trim());
            });

            filterImages(selectedCategories);
        });
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
    
