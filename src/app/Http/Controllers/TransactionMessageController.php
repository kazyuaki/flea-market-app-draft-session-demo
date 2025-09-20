<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionMessageRequest;
use App\Http\Requests\UpdateTransactionMessageRequest;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionMessageController extends Controller
{
    // 送信
    public function store(StoreTransactionMessageRequest $request, Transaction $transaction)
    {
        // 取引完了 or 片方が完了済みなら弾く
        if ($transaction->status === 'completed' )
         {
            return back()->withErrors([
                'body' => 'この取引は完了しているため、メッセージは送信できません。'
            ]);
        }
        
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

        $transaction->update(['last_message_at' => now()]);

        return redirect()
            ->route('transactions.show', $transaction->id)
            ->with('sent', true);
    }

    // 編集フォーム表示（セッションに対象IDを入れて show に戻す）
    public function edit(Transaction $transaction, TransactionMessage $message)
    {
        $this->assertBelongsTo($transaction, $message);
        $this->authorizeEditOrDelete($message);

        session(['edit_message_id' => $message->id]);

        return redirect()->route('transactions.show', $transaction->id);
    }

    // 更新
    public function update(UpdateTransactionMessageRequest $request, Transaction $transaction, TransactionMessage $message)
    {
        $this->assertBelongsTo($transaction, $message);
        $this->authorizeEditOrDelete($message);

        $data = $request->validated();

        $message->update([
            'body' => $data['body'],
        ]);

        session()->forget('edit_message_id');

        return redirect()->route('transactions.show', $transaction->id)
            ->with('updated', true);
    }

    // 削除
    public function destroy(Transaction $transaction, TransactionMessage $message)
    {
        $this->assertBelongsTo($transaction, $message);
        $this->authorizeEditOrDelete($message);

        // 画像があればストレージから消す
        if ($message->image_path && !str_starts_with($message->image_path, 'http')) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return redirect()->route('transactions.show', $transaction->id)
            ->with('deleted', true);
    }

    /* -------------------- 共通ヘルパ -------------------- */

    // そのメッセージが該当取引のものか
    private function assertBelongsTo(Transaction $transaction, TransactionMessage $message): void
    {
        abort_unless($message->transaction_id === $transaction->id, 404);
    }

    // 自分のメッセージか（編集/削除権限）
    private function authorizeEditOrDelete(TransactionMessage $message): void
    {
        abort_unless($message->user_id === Auth::id(), 403);
    }
}
