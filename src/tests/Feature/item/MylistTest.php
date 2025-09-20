<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    //「いいね」商品の表示
    public function testMylistShowOnlyFavoriteItems()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $favoritedItem = Item::factory()->create(['name' => 'Favorited']);
        $favoritedItem->images()->create(['file_path' => 'test_image.png']);

        $notFavoritedItem = Item::factory()->create(['name' => 'Notfavorited']);
        $notFavoritedItem->images()->create(['file_path' => 'test_image.png']);

        $user->favorites()->attach($favoritedItem->id);

        $response = $this->get('/?page=mylist');
        $response->assertSee('Favorited');
        $response->assertDontsee('Notfavorited');
    }

    //「SOLD」表示
    public function testMylistShowsSoldLabelForPurchasedItems()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $item->images()->create(['file_path' => 'test_image.png']);

        $user->favorites()->attach($item->id);

        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'shipping_post_code' => '123-4567',
            'shipping_address' => 'テスト住所',
            'shipping_building' => 'テストビル',
        ]);

        $response = $this->get('/?page=mylist');
        $response->assertSee('SOLD');
    }

    //出品商品の非表示
    public function testMylistDoesNotShowOwnItems()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'MyOwnItem'
        ]);
        $ownItem->images()->create(['file_path' => 'test_image.png']);

        $otherItem = Item::factory()->create([
            'name' => 'OtherPersonsItem'
        ]);
        $otherItem->images()->create(['file_path' => 'test_image.png']);

        // お気に入り登録
        $user->favorites()->attach([$ownItem->id, $otherItem->id]);

        $response = $this->get('/?page=mylist');

        // 他人の商品は表示
        $response->assertSee($otherItem->name);

        // 自分の商品は表示しない
        $response->assertDontSee($ownItem->name);
    }

    //未認証の場合 何も表示されない
    public function testMylistShowsNothingForGuests()
    {
        $item = Item::factory()->create();
        $item->images()->create(['file_path' => 'test_image.png']);

        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertSee('ログインしてください。');
    }
}
