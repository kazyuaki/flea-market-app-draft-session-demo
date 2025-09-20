<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Transaction;


class UserController extends Controller
{
    //プロフィール未設定の場合　初期設定画面にリダイレクト　設定済の場合、トップページもしくは、直前の挙動ページへ
    public function setup()
    {
        $user = Auth::user();

        if ($user->is_profile_set) {
            return redirect('/');
        }

        return view('user.setup', compact('user'));
    }


    //初回プロフィール設定を保存する
    public function storeProfile(ProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->name = $request->name;
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building_name = $request->building_name;

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->is_profile_set = true;
        $user->save();

        return redirect('/')->with('status', 'プロフィールを設定しました！');
    }


    // プロフィール画面（閲覧用） /mypage
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $ratingsCount = $user->ratingsReceived()->count();
        $ratingAvg     = (int) round((float) $user->ratingsReceived()->avg('score') ?? 0);

        $page = $request->query('page', 'sell');

        $activeTab    = $page;
        $items        = collect();
        $transactions = collect();

        // 取引ごとの未読件数を集計して合計に
        $unreadTotal = Transaction::query()
            ->where(function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->withCount([
                'messages as unread_count' => function ($q) use ($user) {
                    $q->whereNull('read_at')
                        ->where('user_id', '!=', $user->id);
                }
            ])
            ->get()
            ->sum('unread_count');

        if ($page === 'buy') {
            $items = $user->purchasedItems()
                ->with('images')
                ->latest()
                ->get();
        } elseif ($page === 'sell') {
            $items = $user->items()
                ->with('images')
                ->latest()
                ->get();
        } elseif ($page === 'transactions') {
            $transactions = Transaction::with(['item.images', 'seller', 'buyer'])
                ->where(function ($q) use ($user) {
                    $q->where('seller_id', $user->id)
                        ->orWhere('buyer_id', $user->id);
                })
                ->whereIn('status', ['ongoing', 'buyer_completed'])
                ->withCount([
                    'messages as unread_count' => function ($q) use ($user) {
                        $q->whereNull('read_at')
                            ->where('user_id', '!=', $user->id);
                    },
                ])
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($transaction) use ($user) {
                    // 相手ユーザーとサムネを便利プロパティとして付与
                    $transaction->partner = $transaction->seller_id === $user->id
                        ? $transaction->buyer
                        : $transaction->seller;

                    $transaction->thumb = optional($transaction->item->images->first())->file_path;

                    return $transaction;
                });
        } else {
            // 想定外の値なら sell にフォールバック
            $activeTab = 'sell';
            $items = $user->items()
                ->with('images')
                ->latest()
                ->get();
        }

        return view('user.index', compact('user', 'items', 'activeTab', 'transactions', 'ratingAvg', 'ratingsCount', 'unreadTotal'));
    }

    // プロフィール編集フォーム
    public function edit()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    // プロフィール更新処理（POST） /mypage/edit
    public function update(ProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->fill($request->only([
            'name',
            'post_code',
            'address',
            'building_name',
        ]));

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->is_profile_set = true;
        $user->save();

        return redirect('/')->with('status', 'プロフィールを更新しました！');
    }
}
