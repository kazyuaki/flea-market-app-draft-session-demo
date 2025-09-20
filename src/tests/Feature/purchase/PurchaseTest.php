<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\StripeService;
use App\Models\User;
use App\Models\Item;
use Mockery;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testUserCanPurchaseItemAndSeeItInHistory()
    {
        /** @var \App\Models\User $user */

        // Arrange
        $user = User::factory()->create(['is_profile_set' => true]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // StripeServiceをモック
        $mockService = Mockery::mock(StripeService::class);
        $mockService->shouldReceive('createCheckoutSession')
            ->once()
            ->andReturn((object)['url' => 'https://example.com']);

        $this->app->instance(StripeService::class, $mockService);

        // Act
        $response = $this
            ->withSession(['payment_method' => 'カード払い'])
            ->post(route('purchase.checkout', [
                'item' => $item->id
            ]));


        // Assert
        $response->assertRedirect('https://example.com');
    }
}
