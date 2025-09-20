<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //プロフィール情報が見える
    public function testProfilePageShowUserBasicInfo()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'profile_image' => 'profile/test.jpg',
        ]);


        $this->actingAs($user);

        $response = $this->get(route('mypage'));

        $response->assertOk();
        $response->assertSee('テスト太郎');
        $response->assertSee('profile/test.jpg');
    }
    //出品アイテムが表示される
    public function testProfilePageShowsListedItems()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品アイテム名',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage', ['page' => 'sell']));

        $response->assertOk();
        $response->assertSee('出品アイテム名');
    }

    //購入アイテムが表示される
    public function testProfilePageShowsPurchasedItems()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '購入アイテム名',
        ]);

        $user->orders()->create([
            'item_id' => $item->id,
            'payment_method' => 2,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '購入先住所',
            'shipping_building' => '購入ビル',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage', ['page' => 'buy']));

        $response->assertOk();
        $response->assertSee('購入アイテム名');
    }
}
