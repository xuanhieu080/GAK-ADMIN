<?php

namespace App\V1\Models;

use App\Models\Category;
use App\Models\Variant;
use App\V1\Resources\CategoryResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Category();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);

        $input['sort'] = ['id' => 'desc'];
        $result = $this->search($input, [], $limit);

        return CategoryResource::collection($result);
    }

    public function show(Category $item)
    {
        if (!$item->is_active) {
            return null;
        }

        $productIds = $item->products->where('is_active', 1)->pluck('id');
        $variants = Variant::whereIn('product_id', $productIds)->get()->unique()->groupBy('attribute_group_name');
        return response()->json(['item' => new CategoryResource($item), 'variants' => $variants]);
    }

    public function getCategoryHeader($input)
    {
        $limit = Arr::get($input, 'limit', 24);

        $input['sort'] = ['id' => 'desc'];
        $input['is_active'] = 1;
        $input['show_header'] = 1;

        $result = Category::whereIsRoot()->with([
            'descendants' => function ($query) use ($limit) {
                $query->where('show_header', 1)->limit($limit);
            }
        ])->where('show_header', 1)->get();

        return CategoryResource::collection($result);
    }

    public function getCategoryDashboard($input)
    {
        $limit = Arr::get($input, 'limit', 25);

        $input['sort'] = ['id' => 'desc'];
        $input['is_active'] = 1;
        $input['show_dashboard'] = 1;
        $result = $this->search($input, [
            'products' => function ($query) use ($limit) {
                $query->where('products.is_active', 1)->limit($limit);
            }
        ], $limit);

        return CategoryResource::collection($result);
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
}
