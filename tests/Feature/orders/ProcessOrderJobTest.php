<?php

namespace Tests\Feature\orders;

use App\Jobs\orders\ProcessOrderJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProcessOrderJobTest extends TestCase
{
    use RefreshDatabase;
    public function it_decrements_stock_sets_item_prices_and_completes_order_with_correct_total(): void
    {
        $p1 = Product::create(['name' => 'A', 'sku' => 'A-1', 'price' => 24.90, 'stock' => 100]);
        $p2 = Product::create(['name' => 'B', 'sku' => 'B-1', 'price' => 79.00, 'stock' => 50]);

        $order = Order::create(['status' => 'pending', 'total_price' => 0]);

        OrderItem::create(['order_id' => $order->id, 'product_id' => $p1->id, 'qty' => 10, 'price' => 0]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $p2->id, 'qty' => 5, 'price' => 0]);

        // Run the job synchronously
        (new ProcessOrderJob($order->id))->handle();

        $order->refresh();
        $this->assertSame('completed', $order->status);
        $this->assertSame('644.00', number_format((float)$order->total_price, 2, '.', ''));

        $this->assertDatabaseHas('products', ['id' => $p1->id, 'stock' => 90]);
        $this->assertDatabaseHas('products', ['id' => $p2->id, 'stock' => 45]);

        // Items should have snapshot prices
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $p1->id, 'price' => 24.90]);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $p2->id, 'price' => 79.00]);
    }
    public function it_marks_failed_if_stock_is_insufficient(): void
    {
        $p = Product::create(['name' => 'A', 'sku' => 'A-1', 'price' => 10.00, 'stock' => 1]);

        $order = Order::create(['status' => 'pending', 'total_price' => 0]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $p->id, 'qty' => 5, 'price' => 0]);

        try {
            (new ProcessOrderJob($order->id))->handle();
        } catch (\Throwable $e) {
        }

        $order->refresh();
        $this->assertSame('failed', $order->status);
        $this->assertDatabaseHas('products', ['id' => $p->id, 'stock' => 1]);
    }
}
