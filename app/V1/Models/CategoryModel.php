<?php

namespace App\V1\Models;

use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\Variant;
use App\V1\Resources\CategoryHeaderResource;
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

    public function show($slug)
    {
        $limit = 99;
        $item = Category::with([
            'descendants'          => function ($query) use ($limit) {
                $query->where('show_header', 1)->limit($limit);
            },
            'descendants.products' => function ($query) {
                $query->where('products.is_active', 1);
            },
            'products'             => function ($query) {
                $query->where('products.is_active', 1);
            },
        ])->where('show_header', 1)
            ->get()
            ->map(function ($category) {
                $products = $category->products->concat($category->descendants->pluck('products')->flatten());
                $category->setRelation('products', $products);
                return $category;
            })->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
        if (empty($item)) {
            return null;
        }

        $productIds = $item->products->where('is_active', 1)->pluck('id');
//        $variants = Variant::whereIn('product_id', $productIds)->get()->unique()->groupBy('attribute_group_id')->toArray();
        $variants = AttributeGroup::with(['attributes' => function ($query) use ($productIds) {
            $query->whereHas('variants', function ($qr) use ($productIds) {
                $qr->whereIn('product_id', $productIds);
            });
        }])->whereHas('attributes', function ($qr) use ($productIds) {
            $qr->whereHas('variants', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
        })->orderByDesc('is_color')->get();

        return response()->json(['item' => new CategoryResource($item), 'variants' => $variants]);
    }

    public function getCategoryHeader($input)
    {
        $limit = Arr::get($input, 'limit', 4);

        $input['sort'] = ['id' => 'desc'];
        $input['is_active'] = 1;
        $input['show_header'] = 1;

        $result = Category::whereIsRoot()
            ->with([
                'descendants'          => function ($query) use ($limit) {
                    $query->where('show_header', 1)->limit(99);
                },
                'descendants.products' => function ($query) {
                    $query->where('products.is_active', 1);
                },
                'products'             => function ($query) {
                    $query->where('products.is_active', 1);
                },
            ])->where('show_header', 1)
            ->paginate($limit);
        $result->getCollection()->transform(function ($category) use ($limit) {
            $products = $category->products->concat($category->descendants->pluck('products')->flatten());

            // Có thể cần áp dụng phân trang cho sản phẩm ở đây, tùy thuộc vào yêu cầu:
            // $products = $products->slice(0, $limit);

            $category->setRelation('products', $products);

            return $category;
        });

        return CategoryHeaderResource::collection($result);
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
