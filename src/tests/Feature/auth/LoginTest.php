<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    //メールアドレス バリデーションテスト
    public function testLoginFailsWhenEmailIsMissing()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    //パスワード未入力 バリデーションテスト
    public function testLoginFailsWhenPasswordIsMissing()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    //入力情報の誤り バリデーションテスト
    public function testLoginFailsWithInvalidCredentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalidpass'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }

    //ログイン成功 テスト
    public function testLoginSucceedsWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }
}
