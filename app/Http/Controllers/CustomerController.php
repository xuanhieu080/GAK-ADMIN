<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\Customers\StoreRequest;
use App\Http\Requests\Customers\UpdateRequest;
use App\Services\Customer\CustomerService;

class CustomerController extends Controller
{
    /**
     * The service instance
     * @var CustomerService
     */
    private CustomerService $customerService;

    /**
     * Constructor
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', Customer::class);

        return $this->customerService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Customer::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', Customer::class);

        $input = $request->validated();
        $record = $this->customerService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Customer  $customer
     *
     * @return CustomerResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', Customer::class);

        $model = $this->customerService->get($customer);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer  $customer
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Customer $customer)
    {
        $this->authorize('edit', Customer::class);

        return $this->show($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Customer  $customer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Customer $customer)
    {
        $this->authorize('edit', Customer::class);

        $data = $request->validated();
        if ($this->customerService->update($customer, $data)) {
            return $this->responseUpdateSuccess(['model' => $customer->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Customer  $customer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function recharge(UpdateRequest $request, Customer $customer)
    {
        $this->authorize('edit', Customer::class);

        $data = $request->validated();
        if ($this->customerService->update($customer, $data)) {
            return $this->responseUpdateSuccess(['model' => $customer->fresh()]);
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
    public function destroy(Request $request, Customer $customer)
    {
        $this->authorize('delete', Customer::class);

        if ($this->customerService->delete($customer)) {
            return $this->responseDeleteSuccess(['model' => $customer]);
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
