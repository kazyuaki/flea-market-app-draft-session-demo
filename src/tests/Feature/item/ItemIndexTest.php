<?php

namespace Tests\Feature\Item;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    //全商品取得
    public function testAllItemsAreDisplayed()
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(3)->create(['user_id' => $user->id]);


        // 画像も登録
        foreach ($items as $item) {
            $item->images()->create(['file_path' => 'test_image.png']);
        }

        $response = $this->get('/');

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    //「SOLD」表示
    public function testPurchasedItemIsMarkedAsSold()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['id' => 2]);
        $displayUser = User::factory()->create(['id' => 1]);

        $this->actingAs($user);

        $item = Item::factory()->create(['user_id' => $displayUser->id]);
        $item->images()->create(['file_path' => 'test_image.png']);

        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'shipping_post_code' => '123-4567',
            'shipping_address' => 'テスト住所',
            'shipping_building' => 'テストビル',
        ]);

        $response = $this->get('/');
        $response->assertSee('SOLD');
    }

    //出品商品の非表示
    public function testUserCannotSeeOwnItems()
    {
        /** @var \App\Models\User $user */

        // 他人の出品（id = 2 のユーザーを新規作成しておく）
        $loggedInUser = User::factory()->create(['id' => 2]); // ログインユーザー（表示されない）
        $displayUser = User::factory()->create(['id' => 1]);  // 表示対象ユーザー（ダミーデータ）

        $this->actingAs($loggedInUser);

        $ownItem = Item::factory()->create([
            'user_id' => $loggedInUser->id,
            'name' => 'MyOwnItemForTest123'
        ]);        $ownItem->images()->create(['file_path' => 'test_image.png']);

        $otherItem = Item::factory()->create(['user_id' => $displayUser->id]);
        $otherItem->images()->create(['file_path' => 'test_image.png']);

        $response = $this->get('/');
        $response->assertDontSee('MyOwnItemForTest123');
        $response->assertSee($otherItem->name);
    }
}
