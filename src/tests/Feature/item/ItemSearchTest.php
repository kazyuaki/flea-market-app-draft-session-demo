<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductSearchByPartialName()
    {
        $user = User::factory()->create(['id' => 1]);

        $redItem1 = Item::factory()->create([
            'name' => 'RedShoes',
            'user_id' => $user->id
        ]);
        $redItem1->images()->create(['file_path' => 'test_image.png']);

        $redItem2 = Item::factory()->create([
            'name' => 'RedShoes',
            'user_id' => $user->id
        ]);
        $redItem2->images()->create(['file_path' => 'test_image.png']);

        $blueItem = Item::factory()->create([
            'name' => 'BlueHat',
            'user_id' => $user->id
        ]);
        $blueItem->images()->create(['file_path' => 'test_image.png']);

        $response = $this->get('/?keyword=Red');

        $response->assertSee('RedShoes');
        $response->assertDontSee('BlueHat');
    }
}
