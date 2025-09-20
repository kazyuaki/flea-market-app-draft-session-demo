<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $user = Auth::user();
        $isBuyer = $user->id === $transaction->buyer_id;
        $partnerId = $isBuyer ? $transaction->seller_id : $transaction->buyer_id;

        // 1) 評価レコード保存（重複防止はユニーク制約 or 先に存在チェック）
        Rating::updateOrCreate(
            [
                'transaction_id' => $transaction->id,
                'rater_id'       => $user->id,
            ],
            [
                'ratee_id'       => $partnerId,
                'score'          => (int) $request->input('score'),
            ]
        );

        // 2) どちらが評価したかのフラグ
        if ($isBuyer) {
            $transaction->buyer_rated = 1;
            // 購入者側が進めた状態で、まだ出品者が評価していなければ buyer_completed のまま維持
            if ($transaction->status === 'ongoing') {
                $transaction->status = 'buyer_completed';
            }
        } else {
            $transaction->seller_rated = 1;
        }

        // 3) 両者の評価が揃ったら completed に遷移
        if ($transaction->buyer_rated && $transaction->seller_rated) {
            $transaction->status = 'completed';
        }

        $transaction->save();

        return redirect()->route('items.index')
            ->with('status', '評価を送信しました');    }
}

