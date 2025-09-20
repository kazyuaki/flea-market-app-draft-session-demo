@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css' )}}">
@endsection

@section('content')
<main>
    <div class="mypage__content">
        <div class="user">
            <div class="user-info-wrap">
                <img class="user__avatar" src="{{ asset('storage/' . $user->profile_image) }}" alt="ユーザー写真">
                <div class="user-info">
                    <h2>{{ $user->name }}</h2>

                    @if ($ratingsCount > 0)
                    <div class="user-rating" aria-label="平均評価 {{ number_format($user->rating_avg ?? 0, 1) }} / 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <img
                            src="{{ asset($i <= $ratingAvg ? 'img/Star yellow.png' : 'img/Star gray.png') }}"
                            alt=""
                            class="star-icon">
                            @endfor
                    </div>
                    @endif
                </div>
            </div>
            <div class="profile__button">
                <a href="{{ route('profile.edit') }}" class="profile__button-submit">プロフィールを編集</a>
            </div>
        </div>
    </div>



    <nav class="nav">
        <a href="/mypage?page=sell" class="{{ $activeTab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="/mypage?page=buy" class="{{ $activeTab === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="/mypage?page=transactions" class="{{ $activeTab === 'transactions' ? 'active' : '' }}">取引中の商品</a>
        @if(($unreadTotal ?? 0) > 0)
        <span class="badge">{{ $unreadTotal }}</span>
        @endif
    </nav>

    @if ($activeTab === 'transactions')
    <div class="transaction-list">
        @forelse($transactions as $transaction)
        <a href="{{ route('transactions.show', $transaction->id) }}" class="transaction-card">
            @php $thumb = $transaction->thumb; @endphp
            <div class="transaction-thumb-wrap">
                @if ($thumb)
                @if (Str::startsWith($thumb, 'http'))
                <img class="transaction-thumb" src="{{ $thumb }}" alt="商品画像">
                @else
                <img class="transaction-thumb" src="{{ asset('storage/'.$thumb) }}" alt="商品画像">
                @endif
                @else
                <img class="transaction-thumb" src="{{ asset('storage/default.png') }}" alt="商品画像">
                @endif
            </div>

            <div class="transaction-meta">
                <div class="transaction-item-name">{{ $transaction->item->name }}</div>
                <div class="transaction-partner">{{ $transaction->partner->name }}</div>
                <div class="transaction-time">{{ \Carbon\Carbon::parse($transaction->last_message_at)->diffForHumans() }}</div>
            </div>

            @if ($transaction->unread_count > 0)
            <span class="transaction-badge">{{ $transaction->unread_count }}</span>
            @endif
        </a>
        @empty
        <p>取引中のやり取りはありません。</p>
        @endforelse
    </div>

    <!-- {{-- それ以外のタブ（従来の表示） --}} -->
    @else
    <div class="item-list">
        @forelse($items as $item)
        <div class="item">
            <a href="{{ route('item.show', ['item' => $item->id]) }}">
                @if ($item->images->isNotEmpty())
                @php $imagePath = $item->images->first()->file_path; @endphp
                @if (Str::startsWith($imagePath, 'http'))
                <img src="{{ $imagePath }}" alt="商品画像">
                @else
                <img src="{{ asset('storage/' . $imagePath) }}" alt="商品画像">
                @endif
                @else
                <img src="{{ asset('storage/default.png') }}" alt="商品画像">
                @endif
                <div class="item-name">{{ $item->name }}</div>
            </a>
        </div>
        @empty
        @if ($activeTab === 'buy')
        <p>購入した商品はまだありません。</p>
        @elseif ($activeTab === 'sell')
        <p>「出品した商品」はまだありません。</p>
        @endif
        @endforelse
    </div>
    @endif
    </div>
</main>
@endsection