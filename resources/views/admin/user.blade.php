@extends('admin_layouts.app')

@section('content')


<div id="user" class="tab">
    <h2>ユーザー管理</h2>
    <div class="container" style="display: block">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>プラン</th>
                    <th>カテゴリ</th>
                    <th>管理者</th>
                    <th>削除</th>

                    {{-- <th>ステータス</th>
                    <th>ログイン制限</th>
                    <th>理由</th>
                    <th>Actions</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form action="{{ route('user.updatePlan', ['userId' => $user->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select onchange="confirmPlanChange(this, {{ $user->id }})">
                                <option value="personal" {{ $user->plan_type == 'personal' ? 'selected' : '' }}>個人</option>
                                <option value="corporate" {{ $user->plan_type == 'corporate' ? 'selected' : '' }}>法人</option>
                            </select>
                        </form>
                    </td>         
                    <td>
                        @if ($user->plan_type == 'personal')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#categoryModal{{ $user->id }}">カテゴリを編集</button>
                        @endif
                    </td>

                    <!-- モーダルの構造 -->
                    <div class="modal fade" id="categoryModal{{ $user->id }}" tabindex="-1" aria-labelledby="categoryModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="categoryModalLabel{{ $user->id }}">カテゴリ編集</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('user.preferences.update', $user->id) }}" method="POST">
                                    <div class="modal-body">
                                        @csrf
                                        @foreach($styles as $style)
                                            <div class="mb-3">
                                                <label for="style_{{ $style->id }}" class="form-label">{{ $style->name }}</label>
                                                <select class="form-select" name="preferences[{{ $style->id }}]" id="style_{{ $style->id }}">
                                                    <option value="">カテゴリーを選択してください</option>
                                                    @foreach($categories->where('style_id', $style->id) as $category)
                                                        <!-- style_idに基づいてフィルタリングされたカテゴリをループ -->
                                                        @php
                                                            // このユーザーが選んだこのスタイルに対するカテゴリIDを取得
                                                            $selectedCategoryId = optional($user->stylePreferences->where('style_id', $style->id)->first())->category_id;
                                                        @endphp
                                                        <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                        <button type="submit" class="btn btn-primary">保存</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <td>
                        <!-- 管理者ステータスの表示（変更不可） -->
                        <input type="checkbox" {{ $user->is_admin ? 'checked' : '' }} disabled>
                    </td>
                    <td>
                        <!-- ユーザーの削除 -->
                        <form action="/user/delete/{{ $user->id }}" method="POST" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


        <div class="container" style="display: block">
            <h2>新しいユーザーを追加する</h2>
            <form action="{{ route('user.store') }}" method="post" autocomplete="off">
                @csrf
                <div>
                    <label for="name">名前:</label>
                    <input type="text" id="name" name="name" autocomplete="off" required>
                </div>
                <div>
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="email" autocomplete="off" required>
                </div>
                <div>
                    <label for="password">パスワード:</label>
                    <input type="password" id="password" name="password" autocomplete="off" required>
                </div>
                <div>
                    <button type="submit">追加する</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updatePlanType(userId, planType) {
        // Ajaxリクエストを使ってサーバーにプランタイプの変更を送信
    }
    
    function updateAdminStatus(userId, isAdmin) {
        // Ajaxリクエストを使ってサーバーに管理者ステータスの変更を送信
    }
    
    function confirmDelete() {
        return confirm('このユーザーを削除しますか?');
    }

    function confirmPlanChange(selectElement, userId) {
        if (confirm('プランを変更してよろしいですか？')) {
            // ユーザーがOKを選択した場合、フォームを送信
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("user.updatePlan", "") }}/' + userId;
            var hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = '_method';
            hiddenField.value = 'PUT';
            form.appendChild(hiddenField);
            
            var tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = '{{ csrf_token() }}';
            form.appendChild(tokenField);

            var planField = document.createElement('input');
            planField.type = 'hidden';
            planField.name = 'plan_type';
            planField.value = selectElement.value;
            form.appendChild(planField);

            document.body.appendChild(form);
            form.submit();
        } else {
            // ユーザーがキャンセルを選択した場合、ドロップダウンの選択を元に戻す
            selectElement.value = '{{ $user->plan_type }}';
        }
    }
    </script>
    @endsection

