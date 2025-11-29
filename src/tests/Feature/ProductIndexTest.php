<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesSeeder::class);
    }

    public function test_全商品を取得できる()
    {
        User::factory()->create([
        ]);

        $productA = Product::factory()->create([
            'name' => '商品A',
            'image_path' => 'test1-image.jpg',
        ]);
        $productB = Product::factory()->create([
            'name' => '商品B',
            'image_path' => 'test2-image.jpg',
        ]);


        $this->assertEquals(2, Product::count());

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeText($productA->name);
        $response->assertSeeText('商品A');
        $response->assertSee($productA->image_path);
        $response->assertSee('test1-image.jpg');
        $response->assertSeeText($productB->name);
        $response->assertSeeText('商品B');
        $response->assertSee($productB->image_path);
        $response->assertSee('test2-image.jpg');

        $this->assertStringContainsString('<img', $response->getContent());
    }

    public function test_購入済み商品には_Sold_ラベルが表示される()
    {
        $seller = User::factory()->create(['name' => '出品者']);
        $buyer  = User::factory()->create(['name' => '購入者']);

        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'sold',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'sold',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    public function test_自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create(['name' => '自分']);
        $otherUser = User::factory()->create(['name' => '他人']);

        $myProduct = Product::factory()->create([
            'seller_id' => $user->id,
            'name' => '自分の商品',
        ]);
        $otherProduct = Product::factory()->create([
            'seller_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);

        /** @var \App\Models\User $user */

        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee($myProduct->name);
        $response->assertDontSee('自分の商品');
        $response->assertSee($otherProduct->name);
        $response->assertSee('他人の商品');
    }
}
