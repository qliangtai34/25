<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>新規登録</title>
</head>
<body>
    <h1>新規登録</h1>

    {{-- バリデーションエラー表示 --}}
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color:red;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">名前</label><br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
            >
        </div>

        <div>
            <label for="email">メールアドレス</label><br>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
            >
        </div>

        <div>
            <label for="password">パスワード</label><br>
            <input
                type="password"
                id="password"
                name="password"
            >
        </div>

        <div>
            <label for="password_confirmation">パスワード（確認）</label><br>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
            >
        </div>

        <button type="submit">登録する</button>
    </form>

    <p>
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</body>
</html>
