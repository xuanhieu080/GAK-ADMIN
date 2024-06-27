<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\OrderDetailService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Order\OrderService;

class OrderController extends Controller
{
    /**
     * The service instance
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * Constructor
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Order::class);

        return $this->orderService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Order::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
//        $this->authorize('create', Order::class);

        $input = $request->validated();
        $record = $this->orderService->store($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Order  $order
     *
     * @return OrderResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Order $order)
    {
//        $this->authorize('view', Order::class);

        $model = $this->orderService->get($order);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Order  $order
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Order $order)
    {
//        $this->authorize('edit', Order::class);

        return $this->show($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Order  $order
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Order $order)
    {
//        $this->authorize('edit', Order::class);
//
//        $data = $request->validated();
//        if ($this->orderService->update($order, $data)) {
//            return $this->responseUpdateSuccess(['model' => $order->fresh()]);
//        } else {
//            return $this->responseUpdateFail();
//        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Order  $order
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updateStatus(Request $request, Order $order)
    {
//        $this->authorize('edit', Order::class);
//
        $data = $request->validated();
        if ($this->orderService->updateStatus($order, $data['status'])) {
            return $this->responseUpdateSuccess(['model' => $order->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, Order $order)
    {
//        $this->authorize('delete', Order::class);

        if ($this->orderService->delete($order)) {
            return $this->responseDeleteSuccess(['model' => $order]);
        }

        return $this->responseDeleteFail();

    }

    /**
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }


    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function details($orderId, Request $request)
    {
//        $this->authorize('list', Order::class);
       $orderDetailService = new OrderDetailService();
       $input = $request->all();
       $input['order_id'] = $orderId;

        return $orderDetailService->index($input);
    }
}
