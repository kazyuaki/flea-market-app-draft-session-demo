<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionMessageRequest;
use App\Mail\TransactionCompletedMail;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $user    = Auth::user();
        $partner = $transaction->seller_id === $user->id ? $transaction->buyer : $transaction->seller;

        $messages = $transaction->messages()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        // 自分以外が送った未読を既読化
        $transaction->messages()
            ->whereNull('read_at')
            ->where('user_id', '!=', $user->id)
            ->update(['read_at' => Carbon::now()]);

        // サイドバー（進行中 or 購入者完了待ち）、現在の取引は除外
        $sidebarTransactions = Transaction::with(['item.images', 'seller', 'buyer'])
            ->where(fn($q) => $q->where('seller_id', $user->id)->orWhere('buyer_id', $user->id))
            ->whereIn('status', ['ongoing', 'buyer_completed'])
            ->where('id', '!=', $transaction->id)
            ->withCount([
                'messages as unread_count' => fn($q) =>
                $q->whereNull('read_at')->where('user_id', '!=', $user->id),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        // 購入者が「進行中」のときだけ完了ボタン表示
        $canFinishBuyer = ($transaction->status === 'ongoing' && $transaction->buyer_id === $user->id);

        // 出品者が「購入者完了済」を開いたら評価モーダルを自動オープン（未評価のみ）
        $autoOpenRatingModal = (bool) (
            $user->id === $transaction->seller_id &&
            $transaction->status === 'buyer_completed' &&
            !$transaction->seller_rated
        );

        return view('transactions.show', compact(
            'transaction',
            'partner',
            'messages',
            'sidebarTransactions',
            'autoOpenRatingModal',
            'canFinishBuyer'
        ));
    }

    // 購入者の「取引完了」 → ステータス変更＆出品者へメール → #complete-modal 付きで戻す
    public function complete(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $user    = Auth::user();
        $isBuyer = ($transaction->buyer_id === $user->id);

        if ($isBuyer && $transaction->status === 'ongoing') {
            $transaction->update(['status' => 'buyer_completed']);

            // 出品者に通知メール
            if ($transaction->seller && $transaction->seller->email) {
                Mail::to($transaction->seller->email)->send(new TransactionCompletedMail($transaction));
            }

            // モーダルを開いた状態で戻す
            return redirect()
                ->route('transactions.show', $transaction->id)
                ->withFragment('complete-modal');
        }

        return redirect()->route('transactions.show', $transaction->id);
    }

    public function store(StoreTransactionMessageRequest $request, Transaction $transaction)
    {
        $this->authorize('message', $transaction);

        $data = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'user_id'        => Auth::id(),
            'body'           => $data['body'],
            'image_path'     => $imagePath,
        ]);

        $transaction->update(['last_message_at' => Carbon::now()]);

        $request->session()->forget('draft.transaction.' . $transaction->id . '.user.' . Auth::id() . '.body');

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('sent', true);
    }
}
