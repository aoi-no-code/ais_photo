<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <!-- Uncomment the next line if you want to use the Nunito font -->
    {{-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

    <!-- Styles and Scripts -->
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .admin-content{
            margin-top: 100px;
            margin-left: 0px;
        }

        .admin-sidebar {
            margin-top: 70px;
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            height: 100vh; /* Viewportの高さで固定する */
            overflow-y: auto; /* 必要に応じてスクロール */
        }

        .page-container {
            margin-left: 210px; /* sidebar width + a little space */
            width: calc(100% - 210px); /* コンテンツの最大幅を制限する */
            overflow-y: auto; /* 必要に応じてスクロール */
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="admin-sidebar">
            <ul>

                <li><a href="#" data-content="image" class="sidebar-link">画像管理</a></li>
                <li><a href="#" data-content="category" class="sidebar-link">カテゴリー追加</a></li>
                <li><a href="#" data-content="style" class="sidebar-link">カテ別けスタイル追加</a></li>

            </ul>
        </div>

        <div class="page-container">
            @include('admin_layouts.header')

            <div class="admin-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery and AJAX script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    
        function deleteImage(imageId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if (confirm('本当にこの画像を削除しますか？')) {
                $.ajax({
                    url: `/image/delete/${imageId}`,  // 適切なURLに変更する
                    method: 'DELETE',
                    success: function() {
                        alert('画像が削除されました');
                        location.reload();
                    },
                    error: function() {
                        alert('画像の削除に失敗しました');
                    }
                });
            }
        }
    
        $(document).ready(function() {
            $('.sidebar-link').on('click', function(e) {
                e.preventDefault();
                let content = $(this).data('content');
    
                $.ajax({
                    url: `/admin/get-content/${content}`,
                    method: 'GET',
                    success: function(response) {
                        $('.admin-content').html(response);
                        // Ajaxリクエストが成功した後でsetEditButtonListener関数を呼び出し
                        setEditButtonListener();
                    },
                    error: function() {
                        alert('コンテンツの読み込みに失敗しました');
                    }
                });
            });
    
            // 初回読み込み時にもsetEditButtonListenerを呼び出す
            setEditButtonListener();
        });
    </script>
            
    </body>

</html>
