<?php

namespace App\Services\Customer;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\Media\MediaService;
use App\Supports\Support;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CustomerService
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
        $this->model = new Customer();
    }

    /**
     * Get a single resource from the database
     * @param  Customer $customer
     * @return CustomerResource
     */
    public function get(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $query = Customer::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            if ($data['sort_by'] == 'name') {
                $data['sort_by'] = 'first_name';
            }
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return CustomerResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     * @param  array  $data
     * @return Builder|\Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $data['email_verified_at'] = Carbon::now()->toDateTimeString();
        $firstName = strtok($data['name'], ' ');
        $lastName = strtok('');
        $data['first_name'] = trim($firstName);
        $data['last_name'] = trim($lastName);
        $data['code'] = Support::genCode('customers', 'code');

        $record = Customer::query()->create($data);
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
