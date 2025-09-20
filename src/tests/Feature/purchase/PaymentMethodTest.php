<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanSelectPaymentMethodAndItIsReflected()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 1. 支払い方法をPOSTで選択
        $response = $this->post(route('purchase.confirm', ['item' => $item->id]), [
            'payment_method' => 'カード払い',
        ]);
        $response->assertRedirect(route('purchase.confirm', ['item' => $item->id]));

        // 2. 確認画面をGETで表示
        $response = $this->get(route('purchase.confirm', ['item' => $item->id]));
        $response->assertOk();
        $response->assertSee('カード払い');
    }
}
