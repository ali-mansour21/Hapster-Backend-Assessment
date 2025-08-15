<?php

namespace App\Jobs\orders;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderReceiptJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

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
        $order = Order::find($this->orderId);
        if (!$order) return;

        Log::info('SendOrderReceiptJob: simulated receipt sent', [
            'order_id'    => $order->id,
            'status'      => $order->status,
            'total_price' => (string) $order->total_price,
        ]);
    }
}
