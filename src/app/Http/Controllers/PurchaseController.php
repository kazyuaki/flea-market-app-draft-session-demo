<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Carbon\Carbon;


class PurchaseController extends Controller
{
    public function confirm(PurchaseRequest $request, Item $item)
    {
        if ($request->isMethod('post')) {
            session(['payment_method' => $request->input('payment_method')]);

            return redirect()->route('purchase.confirm', ['item' => $item->id]);
        }

        $user = auth()->user();
        $payment_method = session('payment_method', '未選択');

        return view('purchase.confirm', compact('item', 'user', 'payment_method'));
    }

    public function editAddress(Item $item)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return view('purchase.address', compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->update([
            'post_code' => $request->post_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        return redirect()
            ->route('purchase.confirm', ['item' => $item->id])
            ->with('status', '住所情報を更新しました')
            ->with('payment_method', '未選択'); // ← セッションに入れて渡す
    }

    public function checkout(Request $request, Item $item, StripeService $stripeService)
    {
        $user = auth()->user();

        // 表示名 → Stripe用のコード変換
        $methodMap = [
            'コンビニ払い' => 'konbini',
            'カード払い' => 'card'
        ];

        // 支払い方法（セッションから取得）
        $payment_method_str = session('payment_method', '未選択');
        $payment_method_type = $methodMap[$payment_method_str] ?? null;

        // 数値化（DB保存用）→ "カード払い" = 2, "コンビニ払い" = 1
        $payment_method_code = array_search($payment_method_type, $methodMap) === 'コンビニ払い' ? 1 : 2;

        if ($payment_method_type === null) {
            return redirect()->back()->withErrors('支払い方法が無効です');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = $stripeService->createCheckoutSession($user, $item, $payment_method_type, $payment_method_code);
        return redirect($session->url);
    }

    public function complete(Request $request, Item $item)
    {

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $payment_method = $request->query('method'); // 1 or 2 を受け取る

        if (!$payment_method) {
            return redirect()->route('purchase.confirm', ['item' => $item->id])
                ->withErrors('支払い方法が無効です');
        }


        // すでに誰かが購入済みならガード（任意）
        // if ($item->is_sold) { return back()->withErrors('この商品は既に購入されています'); }

        $transaction = DB::transaction(function () use ($user, $item, $payment_method) {
            $user->orders()->create([
                'item_id' => $item->id,
                'payment_method' => $payment_method,
                'shipping_post_code' => $user->post_code,
                'shipping_address' => $user->address,
                'shipping_building' => $user->building_name,
            ]);

            $transaction = Transaction::firstOrCreate(
                [
                    'item_id'   => $item->id,
                    'buyer_id'  => $user->id,
                    'seller_id' => $item->user_id,
                    'status'    => 'ongoing',
                ],
                [
                    'last_message_at' => Carbon::now(), // 最初の基準時刻にしておく
                ]
            );
            $item->update(['status' => 'sold']);

            return $transaction;
        });

        // 取引チャットへ遷移
        return redirect()
            ->route('transactions.show', $transaction->id)
            ->with('status', '購入が完了しました！取引メッセージでやり取りを開始できます。');
    }

    public function cancel(Request $request, Item $item)
    {
        return redirect()->route('purchase.confirm', ['item' => $item->id])
            ->with('error', '決済がキャンセルされました。');
    }
}
