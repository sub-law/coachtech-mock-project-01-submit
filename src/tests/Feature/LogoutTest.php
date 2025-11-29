<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;


class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログアウト処理が実行される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create([
            'email' => 'usera@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
