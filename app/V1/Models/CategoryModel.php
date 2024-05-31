<?php

namespace App\V1\Models;

use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\Variant;
use App\V1\Resources\CategoryDetailResource;
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
        $item = Category::with([
            'descendants'          => function ($query) {
                $query->where('is_active', 1)->orderBy('name');
            },
            'descendants.products' => function ($query) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        // Điều kiện cho hình ảnh
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        // Điều kiện cho hình thu nhỏ
                        $query->where('collection_name', 'thumb');
                    });
            },
            'products'             => function ($query) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    });
            },
        ])->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
        if (empty($item)) {
            return null;
        }

        $item->load([
            'descendants.products' => function ($query) {
                $query->where('is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    });
            }
        ])->each(function ($category) {
            // Kết hợp các sản phẩm từ chính category này và các descendants một cách hiệu quả
            $allProducts = collect();
            foreach ($category->descendants as $descendant) {
                $allProducts = $allProducts->concat($descendant->products);
            }

            $category->setRelation('products', $category->products->concat($allProducts));
        });

        $productIds = $item->products->where('is_active', 1)->pluck('id');
//        $variants = Variant::whereIn('product_id', $productIds)->get()->unique()->groupBy('attribute_group_id')->toArray();
        $variants = AttributeGroup::with([
            'attributes' => function ($query) use ($productIds) {
                $query->whereHas('variants', function ($qr) use ($productIds) {
                    $qr->whereIn('product_id', $productIds);
                });
            }
        ])->whereHas('attributes', function ($qr) use ($productIds) {
            $qr->whereHas('variants', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
        })->orderByDesc('is_color')->get();

        return response()->json(['item' => new CategoryDetailResource($item), 'variants' => $variants]);
    }

    public function getAll($input)
    {
        $limit = Arr::get($input, 'limit', 4);
        $categories = Category::with([
            'descendants'          => function ($query) use ($limit) {
                $query->where('is_active', 1);
            },
            'descendants.products' => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        // Điều kiện cho hình ảnh
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        // Điều kiện cho hình thu nhỏ
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
            'products'             => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
        ])->where('is_active', 1)
            ->get();

// Đối với mỗi category, sẽ load thêm các products từ descendants
        $categories->load([
            'descendants.products' => function ($query) use ($limit) {
                $query->where('is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            }
        ])->each(function ($category) use ($limit) {
            // Kết hợp các sản phẩm từ chính category này và các descendants một cách hiệu quả
            $allProducts = collect();
            foreach ($category->descendants as $descendant) {
                $allProducts = $allProducts->concat($descendant->products);
            }

            $category->setRelation('products', $category->products->concat($allProducts)->take($limit));
        });

        return CategoryResource::collection($categories);
    }

    public function getCategoryHeader($input)
    {
        $limit = Arr::get($input, 'limit', 20);
        $categories = Category::with([
            'descendants'          => function ($query) use ($limit) {
                $query->where('is_active', 1);
            },
            'descendants.products' => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        // Điều kiện cho hình ảnh
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        // Điều kiện cho hình thu nhỏ
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
            'products'             => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
        ])->where('is_active', 1)
            ->where('show_header', 1)
            ->orderBy('order')
            ->get();

// Đối với mỗi category, sẽ load thêm các products từ descendants
        $categories->load([
            'descendants.products' => function ($query) use ($limit) {
                $query->where('is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            }
        ])->each(function ($category) use ($limit) {
            // Kết hợp các sản phẩm từ chính category này và các descendants một cách hiệu quả
            $allProducts = collect();
            foreach ($category->descendants as $descendant) {
                $allProducts = $allProducts->concat($descendant->products);
            }

            $category->setRelation('products', $category->products->concat($allProducts)->take($limit));
        });

        return CategoryHeaderResource::collection($categories);
    }

    public function getCategoryDashboard($input)
    {
        $limit = Arr::get($input, 'limit', 20);
        $categories = Category::with([
            'descendants'          => function ($query) {
                $query->where('is_active', 1);
            },
            'descendants.products' => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        // Điều kiện cho hình ảnh
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        // Điều kiện cho hình thu nhỏ
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
            'products'             => function ($query) use ($limit) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit($limit);
            },
        ])->where('is_active', 1)
            ->where('show_dashboard', 1)
            ->orderBy('name')
            ->get();

// Đối với mỗi category, sẽ load thêm các products từ descendants
        $categories->load([
            'descendants.products' => function ($query) {
                $query->where('is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default');
                    })->whereHas('media', function ($query) {
                        $query->where('collection_name', 'thumb');
                    })->limit(20);
            }
        ])->each(function ($category) use ($limit) {
            // Kết hợp các sản phẩm từ chính category này và các descendants một cách hiệu quả
            $allProducts = collect();
            foreach ($category->descendants as $descendant) {
                $allProducts = $allProducts->concat($descendant->products);
            }

            $category->setRelation('products', $category->products->concat($allProducts)->take($limit));
        });

        return CategoryHeaderResource::collection($categories);
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
