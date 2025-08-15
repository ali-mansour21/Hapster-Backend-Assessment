<?php

namespace Tests\Feature\orders;

use App\Jobs\orders\ProcessOrderJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_creates_a_pending_order_and_dispatches_processing_job(): void
    {
        Queue::fake();

        $p1 = Product::create(['name' => 'A', 'sku' => 'A-1', 'price' => 24.90, 'stock' => 100]);
        $p2 = Product::create(['name' => 'B', 'sku' => 'B-1', 'price' => 79.00, 'stock' => 50]);

        $payload = [
            'items' => [
                ['product_id' => $p1->id, 'qty' => 10],
                ['product_id' => $p2->id, 'qty' => 5],
            ],
        ];

        $res = $this->postJson('/api/orders', $payload);

        $res->assertCreated()
            ->assertJson([
                'status'  => 'success',
                'message' => 'Order Created',
            ])
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'status', 'total_price', 'items'],
                'message',
            ]);

        $orderId = $res->json('data.id');

        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'pending', 'total_price' => 0]);
        $this->assertDatabaseCount('order_items', 2);

        Queue::assertPushed(ProcessOrderJob::class, function ($job) use ($orderId) {
            return (int)$job->orderId === (int)$orderId;
        });
    }
    #[Test]
    public function it_validates_items_array_and_each_row(): void
    {
        $res = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => 9999, 'qty' => 0],
            ],
        ]);

        $res->assertUnprocessable()
            ->assertJson([
                'status'  => 'failed',
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure(['errors' => ['items.0.product_id', 'items.0.qty']]);
    }
}
