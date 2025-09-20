<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea market</title>
    <link rel="stylesheet" href="../../css/auth.common.css">
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
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>