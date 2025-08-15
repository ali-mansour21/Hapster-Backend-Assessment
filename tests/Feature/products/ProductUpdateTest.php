<?php

namespace Tests\Feature\products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_updates_a_product_and_clears_caches_via_service(): void
    {
        $p = Product::create([
            'name'  => 'Mouse',
            'sku'   => 'SKU-MOU-001',
            'price' => 20.00,
            'stock' => 50,
        ]);

        $res = $this->putJson("/api/products/{$p->id}", [
            'price' => 24.90,
            'stock' => 60,
        ]);

        $res->assertOk()
            ->assertJson([
                'status'  => 'success',
                'message' => 'Product updated.',
            ])
            ->assertJsonPath('data.price', '24.90')
            ->assertJsonPath('data.stock', 60);

        $this->assertDatabaseHas('products', [
            'id'    => $p->id,
            'price' => 24.90,
            'stock' => 60,
        ]);
    }
    #[Test]
    public function it_validates_update_fields(): void
    {
        $p = Product::create([
            'name'  => 'Keyboard',
            'sku'   => 'SKU-KBD-001',
            'price' => 79.00,
            'stock' => 10,
        ]);

        $res = $this->putJson("/api/products/{$p->id}", [
            'stock' => -1,
        ]);

        $res->assertUnprocessable()
            ->assertJson([
                'status'  => 'failed',
                'message' => 'Validation failed.',
            ]);
    }
}
