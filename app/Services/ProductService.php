<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    private const INDEX_TTL = 300;   // 5 min
    private const SHOW_TTL  = 1800;  // 30 min
    public function paginate(int $page, int $limit): array
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
    public function show(Product $product): Product
    {
        $key = "products.show:{$product->id}";
        return Cache::tags(['products', "product:{$product->id}"])->remember($key, self::SHOW_TTL, fn() => $product);
    }
    public function create(array $data): Product
    {
        $product = Product::create($data);
        $this->bustListAndOne($product->id);
        return $product;
    }
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        $this->bustListAndOne($product->id);
        return $product;
    }
    public function delete(Product $product): void
    {
        $id = $product->id;
        $product->delete();
        $this->bustListAndOne($id);
    }


    private function bustListAndOne(int $productId): void
    {
        Cache::tags(['products', 'products:index'])->flush();
        Cache::tags(["product:{$productId}"])->flush();
    }
}
