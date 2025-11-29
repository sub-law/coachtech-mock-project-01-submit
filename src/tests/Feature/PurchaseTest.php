<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_「購入する（コンビニ払い）」ボタンを押下すると購入が完了する()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->post("/purchase/{$product->id}", [
            'payment_method' => 'convenience',
        ]);

        $response->assertRedirect(route('index'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'payment_method' => 'convenience',
            'postal_code' => $buyer->postal_code,
            'address' => $buyer->address,
            'building_name' => $buyer->building_name,
        ]);
    }

    public function test_「購入した商品は商品一覧画面にて「sold」と表示される()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post("/purchase/{$product->id}", [
            'payment_method' => 'convenience',
        ]);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Sold');
    }

    public function test_「プロフィールの購入した商品一覧」に追加されている()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'name' => 'テスト商品A',
            'image_path' => 'test-image.jpg',
        ]);


        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        $response = $this->get("/purchase/{$product->id}");
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post("/purchase/{$product->id}", [
            'payment_method' => 'convenience',
        ]);

        $response = $this->get("/mypage");
        $response->assertStatus(200);

        $html = $response->getContent();

        $this->assertStringContainsString('<div class="product-row purchased-list">', $html);
        $this->assertStringContainsString('Sold', $html);
        $response->assertSee('テスト商品A');
        $response->assertSee('test-image.jpg');
    }
}
