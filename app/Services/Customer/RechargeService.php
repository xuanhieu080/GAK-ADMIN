<?php

namespace App\Services\Customer;

use App\Http\Resources\CustomerRechargeResource;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\CustomerRecharge;
use App\Services\Media\MediaService;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
     * @param CustomerRecharge $customerRecharge
     * @return CustomerRechargeResource
     */
    public function get(CustomerRecharge $customerRecharge)
    {
        return new CustomerRechargeResource($customerRecharge);
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
     * @param array $data
     * @return Builder|\Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $data['code'] = Support::genCode('customer_recharges', 'code');
            $bank = Bank::find($data['bank_id']);
            $data['description'] = "$bank->code";
            $data['status'] = "SUCCESS";
            $data['date'] = date('Y-m-d H:i:s', time());

            $record = CustomerRecharge::query()->create($data);

            $customer = Customer::find($data['customer_id']);
            $customer->balance += $record->amount;
            $customer->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
        return $record->fresh();
    }

    /**
     * Deletes resource in the database
     * @param Customer|Model $customerRecharge
     * @return bool
     */
    public function delete(CustomerRecharge $customerRecharge)
    {
//        return $customerRecharge->delete();
    }

    /**
     * Clean the data
     * @param array $data
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
