@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/confirm.css' )}}">
@endsection

@section('content')
<main>
    <div class="content">
        <div class="item-confirm">
            <section class="item-detail">
                <div class="item-detail__image">
                    @if ($item->images->isNotEmpty())
                    @php
                    $imagePath = $item->images->first()->file_path;
                    @endphp
                    @if (Str::startsWith($imagePath, 'http'))
                    <img src="{{ $imagePath }}" alt="商品画像">
                    @else
                    <img src="{{ asset('storage/' . $imagePath) }}" alt="商品画像">
                    @endif
                    @else
                    <img src="{{ asset('img/noimage.png') }}" alt="商品画像">
                    @endif
                </div>
                <div class="item-detail__info">
                    <h2 class="item-title">{{ $item->name }}</h2>
                    <div class="item-price">
                        <p>¥{{ number_format($item->price) }}</p>
                    </div>
                </div>
            </section>

            <section class="payment-method">
                <div class="payment-method__title">
                    <h3>支払い方法</h3>
                </div>
                <div class="payment-method__select">
                    <form action="{{ route('purchase.confirm.store', [ 'item' => $item->id ]) }}" method="POST">
                        @csrf
                        <select name="payment_method" onchange="this.form.submit()">
                            <option value="" hidden>選択してください</option>
                            <option value="コンビニ払い" {{ session('payment_method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                            <option value="カード払い" {{ session('payment_method') == 'カード払い' ? 'selected' : '' }}>カード払い</option>
                        </select>
                    </form>
                </div>
                @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif
            </section>

            <section class="shipping-address">
                <div class="shipping-address__title--box">
                    <h3 class="shipping-address__title">配送先</h3>
                    <div class="shipping-address__change">
                        <a href="{{ route('purchase.address.edit',['item' => $item->id]) }}">変更する</a>
                    </div>
                </div>
                <div class="shipping-address__content">
                    <p class="post_code">〒{{ $user->post_code }}</p>
                    <p class="address">{{ $user->address }} {{ $user->building_name }}</p>
                </div>
            </section>
        </div>
        <div class="item-purchase__box">
            <div class="item-purchase__confirm">
                <table>
                    <tr>
                        <th class="table-title">商品代金</th>
                        <td class="table-price">¥{{ number_format($item->price) }}</td>
                    </tr>
                    <tr>
                        <th class="table-title">支払い方法</th>
                        <td class="table-price">{{ $payment_method ?? '未選択' }}</td>
                    </tr>
                </table>
            </div>
            <form action="{{ route('purchase.checkout', ['item' => $item->id]) }}" method="POST">
                @csrf
                <div class="item-purchase__button">
                    <button>購入する</button>
                </div>
            </form>
        </div>
    </div>
    @endsection