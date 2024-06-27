<?php

namespace App\Http\Controllers;

use App\Models\CustomerRecharge;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerRechargeResource;
use App\Http\Requests\Customers\RechargeRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Customer\RechargeService;

class CustomerRechargeController extends Controller
{
    /**
     * The service instance
     * @var RechargeService
     */
    private RechargeService $rechargeService ;

    /**
     * Constructor
     */
    public function __construct(RechargeService $rechargeService )
    {
        $this->rechargeService = $rechargeService ;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', CustomerRecharge::class);

        return $this->rechargeService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', CustomerRecharge::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RechargeRequest  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(RechargeRequest $request)
    {
//        $this->authorize('create', CustomerRecharge::class);

        $input = $request->validated();
        $record = $this->rechargeService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  CustomerRecharge  $recharge
     *
     * @return CustomerRechargeResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(CustomerRecharge $recharge)
    {
//        $this->authorize('view', CustomerRecharge::class);

        $model = $this->rechargeService->get($recharge);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  CustomerRecharge  $recharge
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(CustomerRecharge $recharge)
    {
//        $this->authorize('edit', CustomerRecharge::class);

        return $this->show($recharge);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  CustomerRecharge  $recharge
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, CustomerRecharge $recharge)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, CustomerRecharge $recharge)
    {
//        $this->authorize('delete', CustomerRecharge::class);

        if ($this->rechargeService->delete($recharge)) {
            return $this->responseDeleteSuccess(['model' => $recharge]);
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
}
