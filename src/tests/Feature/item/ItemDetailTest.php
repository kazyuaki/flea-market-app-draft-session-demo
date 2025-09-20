<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductDetailShowsAllInformation()
    {
        /** @var \App\Models\User $user */
        // 1️⃣ 関連データを用意
        $user = User::factory()->create(['name' => 'TestUser']);
        $category1 = Category::factory()->create(['content' => 'CategoryA']);
        $category2 = Category::factory()->create(['content' => 'CategoryB']);

        // 2️⃣ 商品を作成してカテゴリを紐づけ
        $item = Item::factory()->create([
            'name' => 'TestItem',
            'brand' => 'TestBrand',
            'price' => 5000,
            'detail' => 'This is a test description',
            'user_id' => $user->id,
            'condition' => 1
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);
        $item->images()->create(['file_path' => 'test_image.png']);

        // 3️⃣ お気に入り（いいね数用）
        $item->favorites()->attach($user->id);

        // 4️⃣ コメント
        $commenter = User::factory()->create(['name' => 'CommentUser']);
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commenter->id,
            'content' => 'Great item!'
        ]);

        // 5️⃣ 詳細ページを取得
        $response = $this->get('/item/' . $item->id);

        // 6️⃣ 各情報が含まれていることを確認
        $response->assertSee('TestItem')
            ->assertSee('TestBrand')
            ->assertSee('¥5,000')
            ->assertSee('This is a test description')
            ->assertSee('良好')
            ->assertSee('CategoryA')
            ->assertSee('CategoryB')
            ->assertSee('Great item!')
            ->assertSee('CommentUser');
    }
}
