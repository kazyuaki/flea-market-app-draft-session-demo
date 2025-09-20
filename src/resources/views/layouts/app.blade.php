<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea market</title>
    <link rel="stylesheet" href="{{ asset('css/app.common.css')}}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="{{ route('items.index') }}">
                    <img src="../../img/logo.svg" alt="COACHTECH" width="350">
                </a>
            </div>
            <div class="search-bar">
                <form action="{{ route('items.index') }}" method="GET">
                    <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                </form>
            </div>
            <div class="nav-links">
                @auth
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
                <a href="/mypage">マイページ</a>

                <a href="/sell" class="sell-button">出品</a>
                @endauth

                @guest
                <a href="/login">ログイン</a>
                <a href="/mypage">マイページ</a> {{-- ← 表示だけしておく --}}
                <a href="/sell" class="sell-button">出品</a>
                @endguest
            </div>
        </div>
    </header>
    @yield('content')
    @yield('js')
</body>

</html>