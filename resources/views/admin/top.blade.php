@extends('admin_layouts.app')

<link href="{{ asset('css/admin.css') }}" rel="stylesheet">

@section('content')
<div class="container">

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


    <div class="admin-content">


        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
        
    </div>

</div>
@endsection
