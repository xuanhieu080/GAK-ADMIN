<?php

namespace App\V1\Controllers;


use App\Models\Order;
use App\V1\Models\OrderModel;
use App\V1\Requests\Orders\CheckStockRequest;
use App\V1\Requests\Orders\CreateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new OrderModel();
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Company::class);
        $input = $request->all();

        return $this->model->index($input);
    }

    public function show($order)
    {
//        $this->authorize('view', Company::class);

        $model = $this->model->show($order);
        if (empty($model)) {
            return $this->responseFail('Dữ liệu không tồn tại',[]);
        }
        return $this->responseDataSuccess(['data' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }



    /// Stock
    public function checkStock(Request $request)
    {
        return response()->json($request->all());
//        $this->authorize('list', Company::class);
        $input = $request->validated();

        return $this->model->checkStock($input);
    }


    public function create(CreateRequest $request)
    {
        $request->validated();
        return $this->responseDataSuccess(['model' => $this->properties(),'message' => 'Tạo hoá đơn thành công']);
    }
}
