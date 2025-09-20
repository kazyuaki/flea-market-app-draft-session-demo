@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css' )}}">
@endsection

@section('content')
<main>
    <div class="register">
        <div class="register__title">
            <h2>会員登録</h2>
        </div>
        <form class="register__form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-group__label" for="name">ユーザー名</label>
                <input class="form-group__input" type="text" id="name" name="name" value="{{ old('name')}}">
                @error('name')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            <div class=" form-group">
                <label class="form-group__label" for="email">メールアドレス</label>
                <input class="form-group__input" type="email" id="email" name="email" value="{{ old('email')}}">
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
            <div class="form-group">
                <label class="form-group__label" for="password_confirmation">確認用パスワード</label>
                <input class="form-group__input" type="password" id="password_confirmation" name="password_confirmation">
                @error('password_confirmation')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            <button class="register__button" type="submit">登録する</button>
        </form>
        <a class="register__link" href="/login">ログインはこちら</a>
    </div>
</main>
@endsection