<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 管理者
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'post_code' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building_name' => 'テックビル301',
            'is_profile_set' => true,
        ]);

        // 出品者A（CO01〜CO05の商品を出品する想定）
        User::create([
            'name' => 'Seller A',
            'email' => 'sellerA@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'post_code' => '111-1111',
            'address' => '東京都品川区1-1-1',
            'building_name' => 'Aマンション101',
            'is_profile_set' => true,
        ]);

        // 出品者B（CO06〜CO10の商品を出品する想定）
        User::create([
            'name' => 'Seller B',
            'email' => 'sellerB@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'post_code' => '222-2222',
            'address' => '東京都世田谷区2-2-2',
            'building_name' => 'Bハイツ202',
            'is_profile_set' => true,
        ]);

        // 閲覧ユーザー（商品出品なし）
        User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'post_code' => '333-3333',
            'address' => '東京都新宿区3-3-3',
            'building_name' => 'Cビル303',
            'is_profile_set' => true,
        ]);
    }
}
