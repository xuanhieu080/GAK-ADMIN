<?php

namespace App\V1\Models;

use App\Models\Order;
use App\Models\Product;
use App\V1\Resources\OrderResource;
use App\V1\Resources\ProductStockResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Order();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        $result = $this->search($input, [], $limit);

        return OrderResource::collection($result);
    }

    public function search($input = [], $with = [], $limit = null)
    {
        $query = $this->make($with);
        $orWhere = Arr::get($input, 'orWhere', []);
        $this->sortBuilder($query, $input);
        $full_columns = $this->model->getFillable();

        $input = array_intersect_key($input, array_flip($full_columns));
        $orWhere = array_intersect_key($orWhere, array_flip($full_columns));

        foreach ($input as $field => $value) {
            if ($value === "") {
                continue;
            }
            if (is_array($value)) {
                $query->where(function ($q) use ($field, $value) {
                    foreach ($value as $action => $data) {
                        $action = strtoupper($action);
                        if ($data === "") {
                            continue;
                        }
                        switch ($action) {
                            case "LIKE":
                                $q->orWhere(DB::raw($field), "like", "%$data%");
                                break;
                            case "IN":
                                $q->orWhereIn(DB::raw($field), $data);
                                break;
                            case "NOT IN":
                                $q->orWhereNotIn(DB::raw($field), $data);
                                break;
                            case "NULL":
                                $q->orWhereNull(DB::raw($field));
                                break;
                            case "NOT NULL":
                                $q->orWhereNotNull(DB::raw($field));
                                break;
                            case "BETWEEN":
                                $q->orWhereBetween(DB::raw($field), $value);
                                break;
                            default:
                                $q->orWhere(DB::raw($field), $action, $data);
                                break;
                        }
                    }
                });
            } else {
                $query->where(DB::raw($field), $value);
            }
        }
        $query->where(function ($qr) use ($orWhere) {
            foreach ($orWhere as $field => $value) {
                if ($value === "") {
                    continue;
                }
                if (is_array($value)) {
                    $qr->orWhere(function ($q) use ($field, $value) {
                        foreach ($value as $action => $data) {
                            $action = strtoupper($action);
                            if ($data === "") {
                                continue;
                            }
                            switch ($action) {
                                case "LIKE":
                                    $q->orWhere(DB::raw($field), "like", "%$data%");
                                    break;
                                case "IN":
                                    $q->orWhereIn(DB::raw($field), $data);
                                    break;
                                case "NOT IN":
                                    $q->orWhereNotIn(DB::raw($field), $data);
                                    break;
                                case "NULL":
                                    $q->orWhereNull(DB::raw($field));
                                    break;
                                case "NOT NULL":
                                    $q->orWhereNotNull(DB::raw($field));
                                    break;
                                case "BETWEEN":
                                    $q->orWhereBetween(DB::raw($field), $value);
                                    break;
                                default:
                                    $q->orWhere(DB::raw($field), $action, $data);
                                    break;
                            }
                        }
                    });
                } else {
                    $qr->orwhere(DB::raw($field), $value);
                }
            }
        });

        if ($limit) {
            if ($limit === 1) {
                return $query->first();
            } else {
                return $query->paginate($limit);
            }
        } else {
            return $query->get();
        }
    }

    public function show($code)
    {
        $item = Order::where('code', $code)
            ->first();
        if (empty($item)) {
            return null;
        }

        return new OrderResource($item);
    }

    public function checkStock($input)
    {
        $nonNullDetails = array_filter($input['items'], function ($condition) {
            return !is_null($condition['product_variant_id']);
        });

        $nullProductIds = array_reduce($input['items'], function ($carry, $condition) {
            if (is_null($condition['product_variant_id'])) {
                $carry[] = $condition['product_id'];
            }
            return $carry;
        }, []);

        $products = Product::with(['variants' => function ($query) use ($nonNullDetails) {
            $query->where(function ($query) use ($nonNullDetails) {
                foreach ($nonNullDetails as $conditions) {
                    $query->orWhere(function ($query) use ($conditions) {
                        $query->where('product_id', $conditions['product_id'])
                            ->where('id', $conditions['product_variant_id']);
                    });
                }
            })->orderBy('name');
        }])->where(function ($query) use ($nonNullDetails, $nullProductIds) {
            foreach ($nonNullDetails as $conditions) {
                $query->orWhere(function ($query) use ($conditions) {
                    $query->where('id', $conditions['product_id'])
                        ->whereHas('variants', function ($query) use ($conditions) {
                            $query->where('id', $conditions['product_variant_id']);
                        });
                });
            }
            if (!empty($nullProductIds)) {
                $query->orWhereIn('id', $nullProductIds);
            }
        })->orderBy('name')->get();

        return ProductStockResource::collection($products);
    }
}
