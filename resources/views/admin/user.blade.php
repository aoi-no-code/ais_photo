<div id="user" class="tab">
    <h2>ユーザー管理</h2>
    <div class="container" style="display: block">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
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
                    {{-- <td>{{ $user->status }}</td>
                    <td>{{ $user->banned_until }}</td>
                    <td>{{ $user->ban_reason }}</td>
                    <td>
                        <button class="btn btn-primary ban-btn" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}" data-user-id="{{ $user->id }}">
                            ログイン制限設定
                        </button>
                    </td> --}}
                </tr>

                <!-- ここからモーダル -->
                <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">ログイン制限設定</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('user.updateBanInfo', $user->id) }}" id="banForm{{ $user->id }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="ban_reason{{ $user->id }}" class="col-form-label">制限理由:</label>
                                        <textarea class="form-control" id="ban_reason{{ $user->id }}" name="ban_reason{{ $user->id }}"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                        <button type="submit" class="btn btn-primary">保存</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- ここまでモーダル -->

                    @endforeach
                </div>
            </tbody>
        </table>


        <div class="container" style="display: block">
            <h2>Add New User</h2>
            <form action="{{ route('user.store') }}" method="post">
                @csrf
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" autocomplete="off" required>
                </div>
                <div>
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="email" autocomplete="off" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" autocomplete="off" required>
                </div>
                <div>
                    <button type="submit">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>
