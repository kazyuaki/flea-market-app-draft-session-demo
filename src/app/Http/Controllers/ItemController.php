<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    //商品一覧
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $page = $request->query('page');
        $keyword = $request->query('keyword');

        if ($page === 'mylist') {
            if (!auth()->check()) {
                $items = collect();
            } else {
                $items = $user->favorites()
                    ->where('items.user_id', '!=', $user->id)
                    ->when($keyword, function ($query, $keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->with('images')
                    ->latest()
                    ->get();
            }
            $activeTab = 'mylist';
        } else {
            $items = Item::when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
                ->when(auth()->check(), function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id);
                })
                ->with('images')
                ->latest()
                ->get();
            $activeTab = 'recommend';
        }

        //購入したitem_id一覧
        $purchasedItemIds = auth()->check()
            ? $user->orders()->pluck('item_id')->toArray()
            : [];

        return view('items.index', compact('items', 'activeTab', 'purchasedItemIds'));
    }

    //商品詳細画面の表示
    public function show(Item $item)
    {
        $item->load(['categories', 'favorites', 'comments.user', 'images', 'order']);

        $isLoggedIn = Auth::check();
        $isOwner    = false;
        $isSold     = $item->orders()->exists(); 
        $myOngoingTransaction = null;

        if ($isLoggedIn) {
            $user   = Auth::user();
            $userId = $user->id;

            $isOwner = $item->user_id === $userId;

            $myOngoingTransaction = Transaction::where('item_id', $item->id)
                ->where('status', 'ongoing')
                ->where(function ($q) use ($userId) {
                    $q->where('buyer_id', $userId)
                        ->orWhere('seller_id', $userId);
                })
                ->latest('last_message_at')
                ->first();
        }

        return view('items.show', compact(
            'item',
            'isLoggedIn',
            'isOwner',
            'isSold',
            'myOngoingTransaction'
        ));
    }

    //商品出品画面の表示
    public function create()
    {
        $categories = Category::all();

        return view('items.create', compact('categories'));
    }
    //出品商品の情報を保存
    public function store(ExhibitionRequest $request)
    {
        $item = Item::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'detail' => $request->detail,
            'condition' => $request->condition,
        ]);
        // カテゴリーの中間テーブルへ登録
        $item->categories()->sync($request->categories);

        // 画像を保存
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('items', 'public');
                $item->images()->create(['file_path' => $path]);
            }
        }

        return redirect('/')->with('status', '商品を出品しました！');
    }
}
