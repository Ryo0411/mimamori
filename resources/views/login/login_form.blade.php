<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインフォーム</title>
    <!-- script -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </link><!-- bootsutorap用のCSS -->
    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    </link>
</head>

<body>
    <form class="form-signin" method="POST" action="{{ route('login') }}">
        @csrf
        <h1 class="h3 mb-3 font-weight-normal">ログイン</h1>
        @foreach ($errors->all() as $error)
        <ul class="alert alert-danger">
            <li class="list-unstyled">{{ $error }}</li>
        </ul>
        @endforeach

        <!-- ログインエラーの表示(db照合時のエラー) -->
        <x-alert type="danger" :session="session('login_error')" />
        <!-- ログアウトのアラート表示 -->
        <x-alert type="danger" :session="session('logout')" />

        <label for="inputEmail" class="sr-only">名前</label>
        <input type="name" id="inputName" class="form-control" placeholder="Name" name="name" required autofocus>
        <label for="inputPassword" class="sr-only">パスワード</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
    </form>
</body>

</html>