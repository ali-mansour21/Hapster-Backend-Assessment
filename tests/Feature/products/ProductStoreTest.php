<?php

namespace Tests\Feature\products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductStoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    #[Test]
    public function it_creates_a_product_and_returns_unified_success_response(): void
    {
        $payload = [
            'name'  => 'LED Desk Lamp',
            'sku'   => 'SKU-LMP-007',
            'price' => 29.90,
            'stock' => 70,
        ];

        $res = $this->postJson('/api/products', $payload);

        $res->assertCreated()
            ->assertJson([
                'status'  => 'success',
                'message' => 'Product created.',
            ])
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'name', 'sku', 'price', 'stock', 'created_at', 'updated_at'],
                'message',
            ]);

        $this->assertDatabaseHas('products', [
            'sku'   => 'SKU-LMP-007',
            'name'  => 'LED Desk Lamp',
            'stock' => 70,
        ]);
    }
    #[Test]
    public function it_validates_unique_sku_and_required_fields(): void
    {
        $this->postJson('/api/products', [
            'name'  => 'A',
            'sku'   => 'DUP-001',
            'price' => 10,
            'stock' => 1,
        ])->assertCreated();

        $res = $this->postJson('/api/products', [
            'name'  => '',
            'sku'   => 'DUP-001',
            'price' => -1,
            'stock' => -5,
        ]);

        $res->assertUnprocessable()
            ->assertJson([
                'status'  => 'failed',
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure(['errors' => ['name', 'sku', 'price', 'stock']]);
    }
}
