<?php

namespace App\Http\Controllers\api\orders;

use App\Http\Controllers\api\BaseApiController;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    public function __construct(private readonly OrderService $orderService) {}
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'limit' => 'required|integer|min:5|max:50'
        ]);
        $page = $request->input('page');
        $limit = $request->input('limit');
        $payload = $this->orderService->paginate($page, $limit);

        return $this->ok($payload['paginator'], null, $payload['meta']);
    }
    public function show(Order $order)
    {
        $payload = $this->orderService->show($order);
        return $this->ok($payload);
    }
}
