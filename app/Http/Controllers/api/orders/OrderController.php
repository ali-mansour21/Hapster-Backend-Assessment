<?php

namespace App\Http\Controllers\api\orders;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Requests\orders\OrderStoreRequest;
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
    public function store(OrderStoreRequest $orderStoreRequest)
    {
        $requestData = $orderStoreRequest->validated();

        $order = $this->orderService->create($requestData);

        return $this->created($order, 'Order Created');
    }
    public function getOrderStats(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $stats = $this->orderService->getStats($from,$to);

        return $this->ok($stats);
    }
}
