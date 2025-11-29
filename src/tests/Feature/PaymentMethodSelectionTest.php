<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_小計画面で変更が反映される()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::factory()->create(['seller_id' => $seller->id]);

        /** @var \App\Models\User $buyer */

        $this->actingAs($buyer);

        // コンビニ払いを選択
        $response = $this->get("/purchase/{$product->id}?payment_method=convenience");
        $response->assertStatus(200);
        $response->assertSeeText('コンビニ払い');

        // カード払いを選択
        $response = $this->get("/purchase/{$product->id}?payment_method=credit");
        $response->assertStatus(200);
        $response->assertSeeText('カード支払い');
    }
}
