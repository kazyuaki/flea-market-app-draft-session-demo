<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionMessageController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TransactionDraftController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

# トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');



# ゲスト専用（会員登録／ログイン）
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'store'])->name('register');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

# 認証ユーザー専用
Route::middleware('auth', 'verified', 'profile.set')->group(function () {

    // マイページ・プロフィール
    Route::get('/mypage', [UserController::class, 'index'])->name('mypage');
    //初回プロフィール設定・保存
    Route::get('/mypage/profile/setup', [UserController::class, 'setup'])->name('profile.setup');
    Route::post('/mypage/profile/setup', [UserController::class, 'storeProfile'])->name('profile.store');
    //２回目以降プロフィール編集・更新
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    //いいね・コメント送信
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->name('items.favorite');
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

    //購入手続き画面
    Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::post('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm.store');

    //配送先変更
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    //決済画面、購入決定
    Route::post('/purchase/checkout/{item}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/complete/{item}', [PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::get('/purchase/cancel/{item}', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
    Route::post('/purchase/mock-complete/{item}', [PurchaseController::class, 'mockComplete']);

    // 取引チャット画面（購入者/出品者共通）
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // メッセージ投稿（US001/US002）
    Route::post('/transactions/{transaction}/messages', [TransactionMessageController::class, 'store'])
        ->name('transactions.messages.store');

    //メッセージ下書き保持
    Route::post('/transactions/{transaction}/messages/draft', [TransactionDraftController::class, 'store'])
        ->name('transactions.messages.draft.store');
    //メッセージ下書き削除
    Route::delete('/transactions/{transaction}/messages/draft', [TransactionDraftController::class, 'destroy'])
        ->name('transactions.messages.draft.destroy');

    // メッセージ編集開始（編集フォームに切替）※セッションで edit_message_id を入れて show に戻す
    Route::post('/transactions/{transaction}/messages/{message}/edit', [TransactionMessageController::class, 'edit'])
        ->name('transactions.messages.edit');

    // メッセージ更新（PATCH）
    Route::patch('/transactions/{transaction}/messages/{message}', [TransactionMessageController::class, 'update'])
        ->name('transactions.messages.update');

    // メッセージ削除（DELETE）
    Route::delete('/transactions/{transaction}/messages/{message}', [TransactionMessageController::class, 'destroy'])
        ->name('transactions.messages.destroy');

    // 既読処理（必要ならそのまま）
    Route::post('/transactions/{transaction}/read', [TransactionMessageController::class, 'markAllRead'])
        ->name('transactions.read');
    Route::post('/messages/{message}/read', [TransactionMessageController::class, 'markRead'])
        ->name('messages.read');

    // 取引完了（US004 → モーダルで評価へ）
    Route::post('/transactions/{transaction}/complete', [TransactionController::class, 'complete'])
        ->name('transactions.complete');

    // 評価保存（US004）
    Route::post('/transactions/{transaction}/ratings', [RatingController::class, 'store'])
        ->name('transactions.ratings.store');
});
