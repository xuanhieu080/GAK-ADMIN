<?php

namespace App\V1\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\V1\Resources\ProductDetailResource;
use App\V1\Resources\ProductResource;
use App\V1\Resources\ProductReviewResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Product();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['is_active'] = 1;
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        if (!empty($input['search'])) {
            $input['name'] = ['like' => $input['search']];
        }
        $result = $this->search($input,
            [
                'attributeVariants',
                //                'variants' => function ($query) {
                //                    $query->where('qty', '>', 0);
                //                }
            ], $limit);

        return ProductResource::collection($result);
    }

    public function hot($input)
    {
        $cacheKey = 'product_hot_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        // Kiểm tra xem dữ liệu có trong cache không
        $products = Cache::remember($cacheKey, $seconds, function () use ($input) {
            $limit = Arr::get($input, 'limit', 999);
            $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
            $input['is_active'] = 1;
            $sorts = ['id' => 'desc'];
            if (!empty($sort['desc']) && is_array($sort['desc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
            } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
            }

            $input['sort'] = $sorts;
            if (!empty($input['search'])) {
                $input['name'] = ['like' => $input['search']];
            }
            $result = $this->search($input,
                [
                    'attributeVariants',
                    //                'variants' => function ($query) {
                    //                    $query->where('qty', '>', 0);
                    //                }
                ], $limit);

            return ProductResource::collection($result);
        });

        return $products;
    }

    public function cacheProductHot($input)
    {
        $cacheKey = 'product_hot_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['is_active'] = 1;
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        if (!empty($input['search'])) {
            $input['name'] = ['like' => $input['search']];
        }
        $result = $this->search($input,
            [
                'attributeVariants',
                //                'variants' => function ($query) {
                //                    $query->where('qty', '>', 0);
                //                }
            ], $limit);

        Cache::put($cacheKey, ProductResource::collection($result), $seconds);
    }

    public function upcoming($input)
    {
        $cacheKey = 'product_upcoming_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        // Kiểm tra xem dữ liệu có trong cache không
        $products = Cache::remember($cacheKey, $seconds, function () use ($input) {
            $limit = Arr::get($input, 'limit', 999);
            $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
            $input['is_active'] = 1;
            $sorts = ['id' => 'desc'];
            if (!empty($sort['desc']) && is_array($sort['desc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
            } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
            }

            $input['sort'] = $sorts;
            if (!empty($input['search'])) {
                $input['name'] = ['like' => $input['search']];
            }
            $result = $this->search($input,
                [
                    'attributeVariants',
                    //                'variants' => function ($query) {
                    //                    $query->where('qty', '>', 0);
                    //                }
                ], $limit);

            return ProductResource::collection($result);
        });

        return $products;
    }

    public function cacheProductUpcoming($input)
    {
        $cacheKey = 'product_upcoming_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['is_active'] = 1;
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        if (!empty($input['search'])) {
            $input['name'] = ['like' => $input['search']];
        }
        $result = $this->search($input,
            [
                'attributeVariants',
                //                'variants' => function ($query) {
                //                    $query->where('qty', '>', 0);
                //                }
            ], $limit);

        Cache::put($cacheKey, ProductResource::collection($result), $seconds);
    }

    public function uniform($input)
    {
        $cacheKey = 'product_uniform_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        // Kiểm tra xem dữ liệu có trong cache không
        $products = Cache::remember($cacheKey, $seconds, function () use ($input) {
            $limit = Arr::get($input, 'limit', 999);
            $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
            $input['is_active'] = 1;
            $sorts = ['id' => 'desc'];
            if (!empty($sort['desc']) && is_array($sort['desc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
            } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
            }

            $input['sort'] = $sorts;
            if (!empty($input['search'])) {
                $input['name'] = ['like' => $input['search']];
            }
            $result = $this->search($input,
                [
                    'attributeVariants',
                    //                'variants' => function ($query) {
                    //                    $query->where('qty', '>', 0);
                    //                }
                ], $limit);

            return ProductResource::collection($result);
        });

        return $products;
    }

    public function cacheProductUniform($input)
    {
        $cacheKey = 'product_uniform_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['is_active'] = 1;
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        if (!empty($input['search'])) {
            $input['name'] = ['like' => $input['search']];
        }
        $result = $this->search($input,
            [
                'attributeVariants',
                //                'variants' => function ($query) {
                //                    $query->where('qty', '>', 0);
                //                }
            ], $limit);

        Cache::put($cacheKey, ProductResource::collection($result), $seconds);
    }

    public function search($input = [], $with = [], $limit = null)
    {
        $attributes = (array)Arr::get($input, 'attributes');
        $categoryName = Arr::get($input, 'category_name');
        $categorySlug = Arr::get($input, 'category_slug');
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

        $query->whereHas('media', function ($query) {
            // Điều kiện cho hình ảnh
            $query->where('collection_name', 'default');
        })->whereHas('media', function ($query) {
            // Điều kiện cho hình thu nhỏ
            $query->where('collection_name', 'thumb');
        });

        if (!empty($attributes)) {
            $query->whereHas('variants', function ($qr) use ($attributes) {
                $attributeQuery = function ($query) use ($attributes) {
                    foreach ($attributes as $attribute) {
                        $query->whereJsonContains("options", (int)$attribute);
                    }
                };

                $qr->when($attributes, function ($q) use ($attributeQuery) {
                    $q->where($attributeQuery);
                });
            });
        }

        if (!empty($categoryName)) {
            $query->whereHas('category', function ($query) use ($categoryName) {
                // Điều kiện cho hình ảnh
                $query->where('name', 'like', "%$categoryName%");
            });
        }

        if (!empty($categorySlug)) {
            $query->whereHas('category', function ($query) use ($categorySlug) {
                $category = Category::with(['descendants'])
                    ->where('slug', $categorySlug)
                    ->first();
                if ($category) {
                    // Lấy ID của tất cả descendants và thêm ID của cha vào đầu mảng
                    $ids = $category->descendants->pluck('id')->prepend($category->id);

                    $query->whereIn('id', $ids);
                }
            });
        }

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


    public function show($slug)
    {
        $item = Product::with([
            'attributeVariants',
            'variants' => function ($query) {
                $query->where('qty', '>', 0);
            },
            'reviews'  => function ($query) {
                $query->orderByDesc('rate')->first();
            }
        ])->where('slug', $slug)
            ->where('is_active', 1)
            ->whereHas('media', function ($query) {
                // Điều kiện cho hình ảnh
                $query->where('collection_name', 'default');
            })->whereHas('media', function ($query) {
                // Điều kiện cho hình thu nhỏ
                $query->where('collection_name', 'thumb');
            })
            ->first();
        if (empty($item)) {
            return null;
        }

        return new ProductDetailResource($item);
    }

    public function getReview($input)
    {
        $limit = Arr::get($input, 'limit', 10);
        $slug = Arr::get($input, 'slug');
        $query = ProductReview::query()
            ->whereHas('product', function ($query) use ($slug) {
                $query->when($slug, function ($q, $slug) {
                    $q->where('slug', $slug);
                });
            })
            ->orderByDesc('date')
            ->paginate($limit);

        return ProductReviewResource::collection($query);
    }
}
