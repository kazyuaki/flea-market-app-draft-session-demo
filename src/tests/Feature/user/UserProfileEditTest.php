<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


    public function testProfileEditFormShowsPreviousValues()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'post_code' => '123-4567',
            'address' => '大阪市中央区',
            'building_name' => 'ビルディング101',
            'profile_image' => 'profiles/test.jpg',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('テスト太郎');
        $response->assertSee('123-4567');
        $response->assertSee('大阪市中央区');
        $response->assertSee('ビルディング101');
        $response->assertSee('profiles/test.jpg');
    }

}

