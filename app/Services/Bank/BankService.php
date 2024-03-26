<?php

namespace App\Services\Bank;

use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class BankService
{
    public $model;

    /**
     * The service instance
     */
    public function __construct()
    {
        $this->model = new Bank();
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Bank::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return BankResource::collection($query->paginate($per_page));
    }

    private function filter(Builder &$query, $filters)
    {
        $query->filter(Arr::except($filters, []));
    }
}
