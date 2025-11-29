<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class DeliveryAddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->get("/purchase/address/$buyer->id");
        $response->assertStatus(200);

        $newAddress = [
            'postal_code' => '100-0005',
            'address' => '東京都千代田区丸の内3-1-1',
            'building_name' => '丸の内国際ビルディング101',
        ];

        $this->put("/purchase/address/{$buyer->id}", $newAddress)
            ->assertStatus(302);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);
        $response->assertSeeText($newAddress['postal_code']);
        $response->assertSee('100-0005');
        $response->assertSeeText($newAddress['address']);
        $response->assertSee('東京都千代田区丸の内3-1-1');
        $response->assertSeeText($newAddress['building_name']);
        $response->assertSee('丸の内国際ビルディング101');
    }

    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->get("/purchase/address/$buyer->id");
        $response->assertStatus(200);

        $newAddress = [
            'postal_code' => '100-0005',
            'address' => '東京都千代田区丸の内3-1-1',
            'building_name' => '丸の内国際ビルディング201',
        ];

        $this->put("/purchase/address/{$buyer->id}", $newAddress)
            ->assertStatus(302);
        $this->assertEquals('東京都千代田区丸の内3-1-1', $buyer->fresh()->address);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->post("/purchase/{$product->id}", [
            'payment_method' => 'convenience',
        ]);

        $response->assertRedirect(route('index'));
        $response->assertStatus(302);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'payment_method' => 'convenience',
            'postal_code' => $buyer->postal_code,
            'address' => $buyer->address,
            'building_name' => $buyer->building_name,
        ]);
    }
}
