@extends('layouts.app')

<link href="{{ asset('css/main.css') }}" rel="stylesheet">

@section('content')
    <div class="main body-no-scroll">

        <div class="totalCount" >
            <p class="total-text">現在の全掲載枚数：{{ $totalImagesCount }}枚</p>
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
                <div class="image-wrapper" 
                        data-filename="{{ $image->filename }}" 
                        data-downloadcount="{{ $image->download_count }}"
                        data-createdat="{{ $image->created_at }}"
                        data-category="{{ $image->categories->pluck('name')->implode(',') }}">
                    <a href="{{ route('increment-download-count', $image->filename) }}">
                        <img src="{{ Storage::disk('s3')->url('images/' . $image->filename) }}" alt="Image" class="image">
                    </a>
                    <span class="download-count">
                        <i class="bi bi-download"></i> 
                        {{ $image->download_count }}
                    </span>
                </div>   
            @endforeach
                            
            <div class="fullscreen-modal" id="fullscreenModal">
                <img src="" id="fullscreenImage" style="display: none;">
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

    const csrf_token_here = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 画像要素にラッパーを追加するための関数（未使用）
    function checkAndAddWrapper() {
        const container = document.querySelector('.image-container');
        const images = container.querySelectorAll('img');
    }

    let isLoading = false;  // ロード中かどうかを示すフラグ
    let noMoreImages = false;  // これ以上ロードする画像がないかどうかを示すフラグ

    // スクロール時に画像を追加ロードするイベントリスナー
    $(window).on('scroll', function() {
        if (!isLoading && !noMoreImages && $(window).scrollTop() + $(window).height() > $(document).height() - $("#footer").height()) {
            loadMoreImages();
        }
    });

    let offset = 20;
    const limit = 20;
    let selectedCategory = '';

    // 画像を追加でロードする関数
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

                        // For mobile devices
                        if (isMobile) {
                            const imgElement = imageElement.querySelector('img');
                            imgElement.parentElement.addEventListener('click', function(e) {
                                e.preventDefault();
                                // Increase the download_count by 1 (need server-side code to handle this)
                                fetch(`/increase-download-count/${image.filename}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': csrf_token_here // CSRFトークンをここにセット
                                    }
                                });

                                const modal = document.getElementById('fullscreenModal');
                                const fullscreenImage = document.getElementById('fullscreenImage');
                                fullscreenImage.src = imgElement.src;
                                modal.style.display = 'flex';

                                modal.addEventListener('click', function() {
                                    this.style.display = 'none';
                                });
                            });
                        }

                        const downloadCountSpan = document.createElement('span');
                        downloadCountSpan.className = "download-count";

                        const downloadIcon = document.createElement('i');
                        downloadIcon.className = "bi bi-download";
                        const downloadCountText = document.createTextNode(` ${image.download_count}`);
                        
                        downloadCountSpan.appendChild(downloadIcon);
                        downloadCountSpan.appendChild(downloadCountText);
                        imageElement.appendChild(downloadCountSpan);
                        
                        $('.image-container').append(imageElement);
                    });
                    
                    offset += data.images.length;
                } else {
                    $("#endMessage").show();
                    noMoreImages = true;
                }
            },
            complete: function() {
                $("#loadingIndicator").hide();
                isLoading = false;
            }
        });
    }

    
    // 画像要素を取得
    const images = document.querySelectorAll('.image-container .image');

    // カテゴリに基づいて画像をフィルタリングする関数
    function filterImages(selectedCategories) {
        const images = document.querySelectorAll('.image-wrapper');
        let matchFound = false; // マッチする画像が見つかったかどうかのフラグ

        const notificationContainer = document.querySelector('.notification-container');
        const noMatchMessage = document.getElementById('noMatchMessage');

        // カテゴリが選択されていない場合の処理
        if (selectedCategories.length === 0) {
            images.forEach(img => {
                img.style.display = 'block'; // すべての画像を表示
            });
            noMatchMessage.style.display = 'none';
            notificationContainer.style.display = 'block'; // 再度表示する
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
            noMatchMessage.style.display = 'block';
            notificationContainer.style.display = 'none'; // ここで再度表示する必要があるか確認してください。
        } else {
            noMatchMessage.style.display = 'none';
            notificationContainer.style.display = 'block'; // 他の条件下で再表示するためのロジック
        }
    }
    

    // モバイルデバイスで画像をクリックした時のモーダル表示処理
    if (isMobile) {
        let isDownloading = false;  // 画像のダウンロード中かどうかを追跡

        const modal = document.getElementById('fullscreenModal');
        const fullscreenImage = document.getElementById('fullscreenImage');

        // モーダルクリックでの閉じる処理をここで一度だけ設定
        modal.addEventListener('click', function() {
            if (!isDownloading) {
                this.style.display = 'none';
                document.body.classList.remove('body-no-scroll');

                // スピナーと画像の表示状態をリセット
                fullscreenImage.style.display = 'none';
            }
        });

        images.forEach(img => {
            img.parentElement.addEventListener('click', function(e) {
                e.preventDefault();

                // 既にダウンロード中なら何もしない
                if (isDownloading) {
                    return;
                }

                isDownloading = true;

                modal.style.display = 'flex';
                document.body.classList.add('body-no-scroll');

                fullscreenImage.style.display = 'none';  // 画像は非表示に設定

                // 画像情報を取得
                fetch(this.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if(response.ok) {

                        // 画像が読み込まれたらスピナーを非表示にして、画像を表示
                        fullscreenImage.onload = function() {
                            fullscreenImage.style.display = 'block';
                            isDownloading = false;  // ダウンロードが終了したのでフラグを更新
                        }

                        // 初期のステートとしてスピナーを表示し、画像を非表示にします。
                        fullscreenImage.style.display = 'none';

                        fullscreenImage.src = img.src;
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
    
