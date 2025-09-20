<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $sellerA = User::where('email', 'sellerA@example.com')->firstOrFail();
        $sellerB = User::where('email', 'sellerB@example.com')->firstOrFail();

        $items = [
            // sellerA の商品
            ['user_id' => null, 'name' => '腕時計', 'price' => 15000, 'detail' => 'スタイリッシュなデザインのメンズ腕時計', 'condition' => 1],
            ['user_id' => null, 'name' => 'HDD', 'price' => 5000, 'detail' => '高速で信頼性の高いハードディスク', 'condition' => 2],
            ['user_id' => null, 'name' => '玉ねぎ3束', 'price' => 300, 'detail' => '新鮮な玉ねぎ3束のセット', 'condition' => 3],
            ['user_id' => null, 'name' => '革靴', 'price' => 4000, 'detail' => 'クラシックなデザインの革靴', 'condition' => 4],
            ['user_id' => null, 'name' => 'ノートPC', 'price' => 45000, 'detail' => '高性能なノートパソコン', 'condition' => 1],

            // sellerB の商品
            ['user_id' => null, 'name' => 'マイク', 'price' => 8000, 'detail' => '高音質のレコーディング用マイク', 'condition' => 2],
            ['user_id' => null, 'name' => 'ショルダーバッグ', 'price' => 3500, 'detail' => 'おしゃれなショルダーバッグ', 'condition' => 3],
            ['user_id' => null, 'name' => 'タンブラー', 'price' => 500, 'detail' => '使いやすいタンブラー', 'condition' => 4],
            ['user_id' => null, 'name' => 'コーヒーミル', 'price' => 4000, 'detail' => '手動のコーヒーミル', 'condition' => 1],
            ['user_id' => null, 'name' => 'メイクセット', 'price' => 2500, 'detail' => '便利なメイクアップセット', 'condition' => 2],
        ];

        foreach ($items as $i => $item) {
            $ownerId = $i < 5 ? $sellerA->id : $sellerB->id;
            $payload = array_merge($item, ['user_id' => $ownerId]);

            Item::updateOrCreate(
                ['name' => $item['name']], // ← name をキーにする
                $payload
            );
        }
    }
}
