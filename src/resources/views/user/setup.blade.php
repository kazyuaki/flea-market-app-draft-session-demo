@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/setup.css' )}}">
@endsection

@section('content')
<main>
    <div class="mypage-edit">
        <div class="mypage-edit__title">
            <h2>プロフィール設定</h2>
        </div>

        <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <div class="user">
                    <img class="user__avatar" src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : '#' }}" alt="ユーザー写真">
                    <label class="form-group__label picture-button" for="profile_image">画像を選択する</label>
                    <input class="form-group__input" type="file" id="profile_image" name="profile_image">
                    @error('profile_image')
                    <div class="form-group__error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-group__label" for="name">ユーザー名</label>
                <input class="form-group__input" type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-group__label" for="post_code">郵便番号</label>
                <input class="form-group__input" type="text" id="post_code" name="post_code" value="{{ old('post_code', $user->post_code) }}">
                @error('post_code')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-group__label" for="address">住所</label>
                <input class="form-group__input" type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
                @error('address')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-group__label" for="building_name">建物名</label>
                <input class="form-group__input" type="text" id="building_name" name="building_name" value="{{ old('building_name', $user->building_name) }}">
                @error('building_name')
                <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>

            <button class="update__button" type="submit">更新する</button>
        </form>
    </div>
</main>
@endsection