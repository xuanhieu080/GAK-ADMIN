<?php

namespace App\V1\Models;

use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\Variant;
use App\Supports\Support;
use App\V1\Resources\CategoryDetailResource;
use App\V1\Resources\CategoryHeaderResource;
use App\V1\Resources\CategoryResource;
use App\V1\Resources\CategorySearchAllResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
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
                $query->where('is_active', 1)
                    ->orderBy('order')
                    ->orderBy('name');
            },
            'descendants.products' => function ($query) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default')
                            ->where('collection_name', 'thumb');
                    });
            },
            'products'             => function ($query) {
                $query->where('products.is_active', 1)
                    ->whereHas('media', function ($query) {
                        $query->where('collection_name', 'default')
                            ->where('collection_name', 'thumb');
                    });
            },
        ])->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
        if (empty($item)) {
            return null;
        }

// Tạo một collection để giữ tất cả product ids
        $productIds = collect();

        $item->each(function ($category) use (&$productIds) {
            // Lấy các sản phẩm từ chính category này
            $categoryProducts = $category->products->pluck('id');

            // Thêm vào tổng collection của product ids
            $productIds = $productIds->concat($categoryProducts);

            // Tương tự, lập lại với mỗi descendant category
            foreach ($category->descendants as $descendant) {
                $descendantProductIds = $descendant->products->pluck('id');
                $productIds = $productIds->concat($descendantProductIds);
            }
        });

// Bây giờ $productIds sẽ chứa ids của tất cả các products mà bạn cần.
// Bạn có thể muốn loại bỏ các id trùng lặp
        $productIds = $productIds->unique();
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

    public function getSearchAll($input)
    {
        $categories = Category::with(['variants' => function ($query) {
            $query->whereIn('product_variants.code', [
                'F56YtbAEmYc7Wkp2',
                'iWpp9vK4V6EQAh2T',
                'AFL0P0UrtxOMQSRN',
                'OUtwzd8iobpOZq93',

                'hHC7X2NI6Y0e698p',
                '4dA6r88gKMAdLuMt',
                'js8YrUeeJtAfjYCJ',
                'oK43C3YcLTwe8Snc',

                'QEQpWwpfwAo1iyIq',
                'oCavhwBOC1RilkVh',
                'ORULu95P51kiWwGB',
                '5OclkuKf6gpuG9Y8',

                '1s9fzozMzvgpePsn',
                'NO2tv5wHtWeDJpir',
                '4kr8c560Q2tEB3Ie',
                'jM1Fme2PzC7OA50x',

                'bqCsv4kf4G4mIoNJ',
                'ZQ5H0zoh8bbqiTUl',
                'kd3WD1kFoRJrpkxg',
                'FCvTzECvyPxsRrJN',
            ]);
        }])
//            ->whereHas('variantMains')
            ->where('is_active', 1)
            ->whereIn('slug', [
                'ao-phan-quang-thun-2-ben',
                'ao-phan-quang-ha-noi',
                'ao-phan-quang-kieu-3m',
                'ao-phan-quang-palize',
                'dong-phuc-cong-nhan',
            ])
//            ->with('variantMains.media')
            ->get();
//            ->map(function ($category) {
//                // Giữ chỉ 4 variantMains cho mỗi category.
//                $category->variants = $category->variants->filter(function ($variants) {
//                    return $variants->media->contains(function ($media) {
//                        return in_array($media->collection_name, ['thumb']);
//                    });
//                })->take(4);
//
//                return $category;
//            });


        return CategorySearchAllResource::collection($categories);
    }

    public function getSearchAll1($input)
    {
        $limit = 4;
        $categories = Category::with([
            'products'              => function ($query) use ($limit) {
                $query->where('products.is_active', 1);
//                    ->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'default');
//                    })->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'thumb');
//                    })->limit($limit);
            },
            'products.variantMains' => function ($query) use ($limit) {
                $query->where('is_active', 1);
//                    ->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'default');
//                    })->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'thumb');
//                    })->limit($limit);
            },
        ])->where('categories.is_active', 1)
            ->whereIn('categories.slug', [
                'ao-phan-quang-thun-2-ben',
                'ao-phan-quang-ha-noi',
                'ao-phan-quang-kieu-3m',
                'ao-phan-quang-palize',
                'dong-phuc-cong-nhan',
            ])
            ->get();

// Đối với mỗi category, sẽ load thêm các products từ descendants
//        $categories->load([
//            'descendants.products' => function ($query) use ($limit) {
//                $query->where('is_active', 1)
//                    ->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'default');
//                    })->whereHas('media', function ($query) {
//                        $query->where('collection_name', 'thumb');
//                    })->limit($limit);
//            }
//        ])->each(function ($category) use ($limit) {
//            // Kết hợp các sản phẩm từ chính category này và các descendants một cách hiệu quả
//            $allProducts = collect();
//            foreach ($category->descendants as $descendant) {
//                $allProducts = $allProducts->concat($descendant->products);
//            }
//
//            $category->setRelation('products', $category->products->concat($allProducts)->take($limit));
//        });

        return CategoryResource::collection($categories);
    }

    public function getCategoryHeader($input)
    {
        $page = Arr::get($input, 'page', 1);
        if ($page == 1) {
            $cacheKey = 'category_dashboard_data';
            $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

            // Kiểm tra xem dữ liệu có trong cache không
            $categories = Cache::remember($cacheKey, $seconds, function () use ($input) {
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
            });

            return $categories;
        } else {
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
    }

    public function cacheCategoryHeader($input)
    {
        $cacheKey = 'category_dashboard_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

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

        Support::writeJsonFile('/home/DEV-GAK-UI/api/category_header.json',  CategoryHeaderResource::collection($categories));
        Cache::put($cacheKey, CategoryHeaderResource::collection($categories), $seconds);
    }

    public function getCategoryDashboard($input)
    {
        $page = Arr::get($input, 'page', 1);
        if ($page == 1) {
            $cacheKey = 'category_dashboard_data';
            $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

            // Kiểm tra xem dữ liệu có trong cache không
            $categories = Cache::remember($cacheKey, $seconds, function () use ($input) {
                $limit = Arr::get($input, 'limit', 5);
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
            });

            return $categories;
        } else {
            $limit = Arr::get($input, 'limit', 5);
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
                                    $q->orWhere(("REPLACE(REPLACE($field, 'Đ', 'd'), 'ư', 'u')"), "like", "%$data%");
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
