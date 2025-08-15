<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    private const INDEX_TTL = 300;   // 5 min
    private const SHOW_TTL  = 1800;  // 30 min
    public function paginate(int $page, int $limit)
    {
        $key = "products.index:p{$page}:l{$limit}";
        return Cache::tags(['products', 'products:index'])->remember($key, self::INDEX_TTL, function () use ($page, $limit) {
            $paginator = Product::query()
                ->paginate($limit, ['*'], 'page', $page);

            $meta = [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ];
            return compact('data', 'meta');
        });
    }
}
