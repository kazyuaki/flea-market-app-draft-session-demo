@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css' )}}">
@endsection

@section('content')
<main>
    <div class="address-edit">
        <div class="address-edit__title">
            <h2>住所の変更</h2>
        </div>
        <form class="address-edit__form" action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="POST">
            @csrf
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