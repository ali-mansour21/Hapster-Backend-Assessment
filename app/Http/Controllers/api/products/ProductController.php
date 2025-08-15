<?php

namespace App\Http\Controllers\api\products;

use App\Http\Controllers\api\BaseApiController;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function __construct(private readonly ProductService $productService) {}
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'limit' => 'required|integer|min:5|max:50'
        ]);
        $page = $request->input('page');
        $limit = $request->input('limit');
        $payload = $this->productService->paginate($page, $limit);
        return $this->ok($payload['data'], null, $payload['meta']);
    }
    public function show()
    {
        //
    }
    public function store()
    {
        //
    }
    public function update()
    {
        //
    }
    public function destroy()
    {
        //
    }
}
