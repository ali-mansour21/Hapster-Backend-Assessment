<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

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
}
