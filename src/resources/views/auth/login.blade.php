@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css' )}}">
@endsection

@section('content')
<div class="login">
    <div class="login__title">
        <h2>ログイン</h2>
    </div>
    <form class="login__form" action="/login" method="POST">
        <div class="form-group">
            @csrf
            <label class="form-group__label" for="email">メールアドレス</label>
            <input class="form-group__input" type="text" id="email" name="email">
            @error('email')
            <div class="form-group__error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-group__label" for="password">パスワード</label>
            <input class="form-group__input" type="password" id="password" name="password">
            @error('password')
            <div class="form-group__error">{{ $message }}</div>
            @enderror
        </div>
        <button class="login__button" type="submit">ログインする</button>
    </form>
    <a class="login__link" href="/register">会員登録はこちら</a>
</div>
@endsection