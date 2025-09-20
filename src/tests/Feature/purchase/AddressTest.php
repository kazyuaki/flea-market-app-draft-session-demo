<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanChangeAddressAndItIsReflectedOnConfirm()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create([
            'post_code' => '000-0000',
            'address' => '旧住所',
            'building_name' => '旧建物',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        // 1. 住所をPOSTで更新
        $response = $this->post(route('purchase.address.update', ['item' => $item->id]), [
            'post_code' => '123-4567',
            'address' => '新しい住所',
            'building_name' => '新しい建物',
        ]);

        $response->assertRedirect(route('purchase.confirm', ['item' => $item->id]));

        // 2. ユーザーレコードを最新で取り直して確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'post_code' => '123-4567',
            'address' => '新しい住所',
            'building_name' => '新しい建物',
        ]);

        // 3. confirm画面をGET
        $this->actingAs($user);
        $response = $this->get(route('purchase.confirm', ['item' => $item->id]));
        $response->assertOk();
        $response->assertSee('123-4567');
        $response->assertSee('新しい住所');
        $response->assertSee('新しい建物');
    }
}
