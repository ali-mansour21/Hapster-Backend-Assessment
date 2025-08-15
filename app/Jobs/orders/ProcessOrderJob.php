<?php

namespace App\Jobs\orders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable, Dispatchable, SerializesModels, InteractsWithQueue;

    public int $tries   = 3;
    public int $backoff = 5;
    /**
     * Create a new job instance.
     */
    public function __construct(public int $orderId)
    {
        //
    }

    public function middleware(): array
    {
        // Avoid concurrent processing of the same order id on the queue
        return [new WithoutOverlapping("order:{$this->orderId}")];
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::query()->with('items')->findOrFail($this->orderId);

        if (in_array($order->status, ['completed', 'failed'], true)) {
            return;
        }
        $order->update(['status' => 'processing']);

        try {
            $productIds = [];
            DB::transaction(function () use ($order) {
                $total = 0;

                foreach ($order->items as $item) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->findOrFail($item->product_id);
                    if ($product->stock < $item->qty) {
                        throw new \RuntimeException("Insufficient stock for product {$product->id}");
                    }
                    $product->decrement('stock', $item->qty);
                    $item->price = $product->price;
                    $item->save();

                    $line = bcmul((string)$product->price, (string)$item->qty, 2);
                    $total = bcadd($total, $line, 2);

                    $productIds[] = $product->id;
                }
                $order->total_price = $total;
                $order->status = 'completed';
                $order->save();

                $this->flushProductCaches($productIds);
                $this->flushOrderCaches($order->id);
            });
        } catch (\Throwable $th) {
            $order->update(['status' => 'failed']);

            $this->flushOrderCaches($order->id);

            Log::error("Error while proccessing the order", [
                'error' => $th->getMessage()
            ]);
            throw $th;
        }
    }

    private function flushOrderCaches(int $orderId): void
    {
        Cache::tags(['orders', 'orders:index', 'orders:stats'])->flush();
        Cache::tags(["order:{$orderId}"])->flush();
    }

    private function flushProductCaches(array $productIds): void
    {
        Cache::tags(['products', 'products:index'])->flush();
        foreach (array_unique($productIds) as $pid) {
            Cache::tags(["product:{$pid}"])->flush();
        }
    }
}
