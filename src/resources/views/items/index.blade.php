 @extends('layouts.app')

 @section('css')
 <link rel="stylesheet" href="{{ asset('css/items/index.css' )}}">
 @endsection

 @section('content')
 <div class="tab-menu">
     <a href="{{ route('items.index') }}" class="tab-link {{ $activeTab === 'recommend' ? 'active' : '' }}">おすすめ</a>
     <a href="{{ route('items.index', ['page' => 'mylist']) }}" class="tab-link {{ $activeTab === 'mylist' ? 'active' : '' }}">マイリスト</a>
 </div>

 <div class="item-list">
     @forelse($items as $item)
     <div class="item">
         <a href="{{ route('item.show', ['item' => $item->id]) }}">
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
             <div class="item-name">{{ $item->name }}</div>
         </a>
         @if(isset($purchasedItemIds) && in_array($item->id, $purchasedItemIds))
         <div class="sold-label">
             <p>SOLD</p>
         </div>
         @endif
     </div>
     @empty
     <p>
         @if(auth()->check())
         @if($activeTab === 'recommend')
         お気に入りにした登録はありません。
         @else
         「いいね」した商品はありません。
         @endif
         @else
         <a href="{{ route('login') }}">ログインしてください。</a>
         @endif
     </p>
     @endforelse
 </div>
 @endsection