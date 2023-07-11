<?php

namespace App\Services\Customer;

use App\Http\Resources\CustomerRechargeResource;
use App\Models\Customer;
use App\Models\CustomerRecharge;
use App\Services\Media\MediaService;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class RechargeService
{

    /**
     * The service instance
     * @var MediaService
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = new CustomerRecharge();
    }

    /**
     * Get a single resource from the database
     * @param  Customer $customer
     * @return CustomerRechargeResource
     */
    public function get(CustomerRecharge $customer)
    {
        return new CustomerRechargeResource($customer);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $query = CustomerRecharge::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return CustomerRechargeResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     * @param  array  $data
     * @return Builder|\Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $data['code'] = Support::genCode('customer_recharges', 'code');
        $firstName = strtok($data['name'], ' ');
        $lastName = strtok('');
        $data['first_name'] = trim($firstName);
        $data['last_name'] = trim($lastName);
        $data['code'] = Support::genCode('customers', 'code');
        unset($data['name']);

        $record = CustomerRecharge::query()->create($data);
        if (!empty($record)) {
            return $record->fresh();
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param  Customer|Model  $customer
     * @param  array  $data
     * @return bool
     */
    public function update(Customer $customer, array $data)
    {
        $data = $this->clean($data);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $firstName = strtok($data['name'], ' ');
        $lastName = strtok('');
        $data['first_name'] = trim($firstName);
        $data['last_name'] = trim($lastName);
        unset($data['name']);
        unset($data['username']);

        return $customer->update($data);
    }

    /**
     * Deletes resource in the database
     * @param  Customer|Model  $customer
     * @return bool
     */
    public function delete(Customer $customer)
    {
        return $customer->delete();
    }

    /**
     * Clean the data
     * @param  array  $data
     * @return array
     */
    private function clean(array $data)
    {
        foreach ($data as $i => $row) {
            if ('null' === $row) {
                $data[$i] = null;
            }
        }
        return $data;
    }

    /**
     * Filter resources
     * @return void
     */
    private function filter(Builder &$query, $filters)
    {
        $query->filter(Arr::except($filters, []));
    }
}
