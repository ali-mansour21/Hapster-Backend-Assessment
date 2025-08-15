<?php

namespace App\Services;

use App\Jobs\orders\ProcessOrderJob;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const INDEX_TTL = 300;   // 5 min
    private const SHOW_TTL  = 1800;  // 30 min
    public function paginate(int $page, int $limit): array
    {
        $key = "orders.index:p{$page}:l{$limit}";
        return Cache::tags(['orders', 'orders:index'])->remember($key, self::INDEX_TTL, function () use ($page, $limit) {
            $paginator = Order::query()
                ->paginate($limit, ['*'], 'page', $page);

            $meta = [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ];
            return compact('paginator', 'meta');
        });
    }
    public function show(Order $order)
    {
        $order = $order->load('items');
        $key = "orders.show:{$order->id}";
        return Cache::tags(['orders', "order:{$order->id}"])->remember($key, self::SHOW_TTL, fn() => $order);
    }
    public function create($data)
    {
        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'status'      => 'pending',
                'total_price' => 0,
            ]);
            foreach ($data['items'] as $it) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $it['product_id'],
                    'qty'        => $it['qty'],
                    'price'      => 0,
                ]);
            }

            return $order;
        });
        Cache::tags(['orders', 'orders:index'])->flush();
        Cache::tags(["order:{$order->id}"])->flush();

        ProcessOrderJob::dispatch($order->id);
        $order = $order->load('items');
        return $order;
    }
    public function stats(?string $from, ?string $to): array
    {
        $key = sprintf(
            'orders.stats:%s:%s',
            $from ?? 'null',
            $to   ?? 'null'
        );

        return Cache::tags(['orders', 'orders:stats'])->remember($key, self::INDEX_TTL, function () use ($from, $to) {
            $q = Order::query()->where('status', 'completed');

            if ($from) $q->whereDate('created_at', '>=', $from);
            if ($to)   $q->whereDate('created_at', '<=', $to);

            $row = $q->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_price), 0) as revenue')->first();

            return [
                'count'   => (int) $row->cnt,
                'revenue' => number_format((float) $row->revenue, 2, '.', ''),
                'range'   => ['from' => $from, 'to' => $to],
            ];
        });
    }
}
