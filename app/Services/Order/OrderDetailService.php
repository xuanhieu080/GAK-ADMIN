<?php

namespace App\Services\Order;

use App\Http\Resources\OrderDetailResource;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class OrderDetailService
{
    public $model;

    /**
     * The service instance
     */
    public function __construct()
    {
        $this->model = new OrderDetail();
    }


    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $query = OrderDetail::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return OrderDetailResource::collection($query->paginate(10));
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
