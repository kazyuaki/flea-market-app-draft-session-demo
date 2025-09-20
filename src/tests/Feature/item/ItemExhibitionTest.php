<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemExhibitionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user);

        $file = \Illuminate\Http\UploadedFile::fake()->create('item.jpg', 100);

        $response = $this->post(route('item.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
            'detail' => 'これはテスト用の商品説明です。',
            'condition' => 2,
            'images' => [$file],
            'categories' => [$category->id],
        ]);


        $response->assertRedirect();

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
            'detail' => 'これはテスト用の商品説明です。',
            'condition' => 2,
            'user_id' => $user->id,
        ]);
    }
}
