<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserInformationUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_image' => 'profile.png',
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都千代田区大手町1-1-1',
            'building_name'  => '大手町ビルディング',           
        ]);

        /** @var \App\Models\User $user */

        $this->actingAs($user);

        $response = $this->get("/mypage/profile");
        $response->assertStatus(200);
        $response->assertSee($user->profile_image);
        $response->assertSee($user->name);
        $response->assertSee($user->postal_code);
        $response->assertSee($user->address);
        $response->assertSee($user->building_name);
    }
}
