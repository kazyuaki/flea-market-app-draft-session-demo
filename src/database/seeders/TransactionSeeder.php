<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TransactionMessage;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sellerA = User::where('email', 'sellerA@example.com')->firstOrFail();
        $sellerB = User::where('email', 'sellerB@example.com')->firstOrFail();
        $viewer  = User::where('email', 'viewer@example.com')->firstOrFail();

        $item1 = Item::where('name', '腕時計')->firstOrFail();
        $item6 = Item::where('name', 'マイク')->firstOrFail();

        // ====== 取引1（腕時計） ======
        // Orderを先に作成（buyerはsellerB）
        Order::updateOrCreate(
            ['item_id' => $item1->id],
            [
                'user_id'             => $sellerB->id,
                'payment_method'      => 1,
                'shipping_post_code'  => $sellerB->post_code ?? '000-0000',
                'shipping_address'    => $sellerB->address ?? '東京都テスト区1-2-3',
                'shipping_building'   => $sellerB->building_name,
            ]
        );

        $t1 = Transaction::updateOrCreate(
            [
                'item_id' => $item1->id,
                'buyer_id' => $sellerB->id,
            ],
            [
                'seller_id'       => $sellerA->id,
                'status'          => 'ongoing',
                'last_message_at' => now()->subMinutes(10)
            ]
        );

        // ====== 取引2（マイク） ======
        // Orderを先に作成（buyerはsellerA）
        Order::updateOrCreate(
            ['item_id' => $item6->id],
            [
                'user_id'             => $sellerA->id,
                'payment_method'      => 1,
                'shipping_post_code'  => $sellerA->post_code ?? '000-0000',
                'shipping_address'    => $sellerA->address ?? '大阪府テスト市4-5-6',
                'shipping_building'   => $sellerA->building_name,
            ]
        );

        $t2 = Transaction::updateOrCreate(
            [
                'item_id' => $item6->id,
                'buyer_id' => $sellerA->id
            ],
            [
                'seller_id'       => $sellerB->id,
                'status'          => 'ongoing',
                'last_message_at' => now()->subMinutes(30),
            ]
        );

        // ===== メッセージ投入（未読・既読を混ぜる） =====
        $this->seedMessages($t1, [
            // [user, body, 分前, 既読か]
            [$sellerA, 'はじめまして！よろしくお願いします。', 12, false],
            [$sellerB, '購入希望です。いつ発送可能でしょうか？', 11, true],
            [$sellerA, '明日には発送可能です！', 10, null], // 自分側の既読表示テスト用にnullでもOK
        ]);

        $this->seedMessages($t2, [
            [$sellerA, 'こちらのマイク購入希望です。', 45, false],
            [$sellerB, 'ありがとうございます。動作問題ありません。', 40, true],
        ]);
    }

    /**
     * 取引にメッセージを投入し、last_message_at を最新に更新
     */
    private function seedMessages(Transaction $t, array $rows): void
    {
        $lastAt = null;

        foreach ($rows as [$user, $body, $minutesAgo, $isRead]) {
            $created = Carbon::now()->subMinutes($minutesAgo);

            $msg = TransactionMessage::updateOrCreate(
                [
                    'transaction_id' => $t->id,
                    'user_id'        => $user->id,
                    'body'           => $body,
                    'created_at'     => $created, // 冪等化のため created_at もキーに
                ],
                [
                    'image_path' => null,
                    'read_at'    => $isRead === true ? Carbon::now() : null,
                    'updated_at' => $created,
                ]
            );

            $lastAt = is_null($lastAt) ? $created : max($lastAt, $created);
        }

        if ($lastAt) {
            $t->update(['last_message_at' => $lastAt]);
        }
    }
}
