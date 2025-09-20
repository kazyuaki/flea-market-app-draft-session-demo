@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css' )}}">
@endsection

@section('content')
<main>
    <div class="item-detail">
        <div class="item-detail__image-box">
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
        </div>

        <div class="item-detail__info">
            <section class="item-purchase">
                <h2 class="item-title">{{ $item->name }}</h2>
                <p class="item-brand">{{ $item->brand }}</p>
                <div class="item-price">
                    <p>¥{{ number_format($item->price) }}</p>
                    <p class="tax">（税込）</p>
                </div>
                <div class="item-reactions">
                    <div class="reaction-group">
                        <form action="{{ route('items.favorite', $item->id) }}" method="POST">
                            @csrf
                            <button class="reaction-favorite" type="submit">
                                <img src="{{ Auth::check() && $item->isFavoritedBy(Auth::user()) ? asset('img/red-star.png') : asset('img/star.png') }}"
                                    alt="いいね" width="50">
                            </button>
                        </form>
                        <p class="reaction-favorite__number">{{ $item->favoritedBy->count() ?? 0 }}</p>
                    </div>
                    <div class="reaction-group">
                        <button class="reaction-comment">
                            <img src="../../img/speech-bubble.png" alt="いいね" width="50">
                        </button>
                        <p class="reaction-comment__number">{{ $item->comments->count() ?? 0 }}</p>
                    </div>
                </div>
                <form action="{{ route('purchase.confirm', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    <div class="item-purchase__button">
                        @if (!$isLoggedIn)
                        <a href="{{ route('login') }}" class="btn-primary">ログインして購入</a>

                        @elseif ($isSold)
                        <button class="btn-sold" disabled>売り切れ</button>

                        @elseif ($isOwner)
                        <button class="btn-secondary" disabled>自分の商品です</button>

                        @elseif ($myOngoingTransaction)
                        <a href="{{ route('transactions.show', $myOngoingTransaction->id) }}" class="btn-primary">取引画面へ</a>

                        @else
                        <a href="{{ route('purchase.confirm', $item) }}" class="btn-primary">購入手続きへ</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="item-info">
                <h3>商品説明</h3>
                <p>{{ $item->detail }}</p>
            </section>

            <section class="item-details">
                <h3>商品の情報</h3>
                <table>
                    <tr>
                        <th>カテゴリ</th>
                        <td>
                            @foreach($item->categories as $category)
                            <span class="category-badge">{{ $category->content }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>商品の状態</th>
                        <td>{{ $item->condition_label }}</td>
                    </tr>
                </table>
            </section>

            <section class="item-comments">
                <h3>コメント（{{ $item->comments->count() }}）</h3>
                @foreach($item->comments as $comment)
                <div class="comment">
                    <img class="comment__avatar" src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="ユーザー写真">
                    <p class="comment__user">{{ $comment->user->name }}</p>
                </div>
                <p class="comment__text">{{ $comment->content }}</p>
                @endforeach

                @if(Auth::check())
                <form class="comment-form" action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST">
                    @csrf
                    <label for="comment">商品へのコメント</label>
                    <textarea id="comment" name="content" rows="5">{{ old('body') }}</textarea>
                    @error('body')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="comment-form__button">コメントを送信する</button>
                </form>
                @else
                <p>
                <p><a href="{{ route('login') }}">ログイン</a>するとコメントできます</p>
                </p>
                @endif
                @error('content')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </section>
        </div>
    </div>
</main>
@endsection