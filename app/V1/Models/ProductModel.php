<?php

namespace App\V1\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\V1\Resources\En\CategorySearchAllResourceEn;
use App\V1\Resources\En\ProductDetailResourceEn;
use App\V1\Resources\En\ProductHotResourceEn;
use App\V1\Resources\En\ProductResourceEn;
use App\V1\Resources\Vi\CategorySearchAllResource;
use App\V1\Resources\Vi\ProductDetailResource;
use App\V1\Resources\Vi\ProductHotResource;
use App\V1\Resources\Vi\ProductResource;
use App\V1\Resources\Vi\ProductReviewResource;
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
        $input['is_active'] = 1;
        $limit = Arr::get($input, 'limit', 999);
        $lang = $input['lang'] ?? null;

        // Gán sort
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['sort'] = collect($sort)->mapWithKeys(function ($fields, $dir) {
            return is_array($fields) ? array_fill_keys($fields, $dir) : [];
        })->all() ?: ['id' => 'desc'];

        // Gán điều kiện tìm kiếm theo ngôn ngữ
        if (!empty($input['search'])) {
            $key = $lang === 'en' ? 'name_en' : 'name';
            $input[$key] = ['like' => $input['search']];
        }

        $result = $this->search($input, ['attributeVariants'], $limit);
        $resourceClass = $lang === 'en' ? ProductHotResourceEn::class : ProductHotResource::class;

        return $this->formatPaginatedResponse($resourceClass, $result);
    }


    public function hot($input)
    {
        $resource = !empty($input['lang']) && $input['lang'] === 'en'
            ? ProductHotResourceEn::class
            : ProductHotResource::class;

        $input['is_hot'] = 1;

        if (Arr::get($input, 'page', 1) == 1) {
            return $this->getProductCache('product_hot_data', 'product_hot_data_en', $input, $resource);
        }

        return $this->getProductResult($input, $resource);
    }

    public function new($input)
    {
        $resource = !empty($input['lang']) && $input['lang'] === 'en'
            ? ProductHotResourceEn::class
            : ProductHotResource::class;

        $input['is_new'] = 1;

        if (Arr::get($input, 'page', 1) == 1) {
            return $this->getProductCache('product_new_data', 'product_new_data_en', $input, $resource);
        }

        return $this->getProductResult($input, $resource);
    }

    public function upcoming($input)
    {
        $resource = !empty($input['lang']) && $input['lang'] === 'en'
            ? ProductHotResourceEn::class
            : ProductHotResource::class;

        $input['is_upcoming'] = 1;

        if (Arr::get($input, 'page', 1) == 1) {
            return $this->getProductCache('product_upcoming_data', 'product_upcoming_data_en', $input, $resource);
        }

        return $this->getProductResult($input, $resource);
    }

    public function uniform($input)
    {
        $resource = !empty($input['lang']) && $input['lang'] === 'en'
            ? ProductHotResourceEn::class
            : ProductHotResource::class;

        $input['is_uniform'] = 1;

        if (Arr::get($input, 'page', 1) == 1) {
            return $this->getProductCache('product_uniform_data', 'product_uniform_data_en', $input, $resource);
        }

        return $this->getProductResult($input, $resource);
    }

    public function cacheProductHot($input)
    {
        $input['is_hot'] = 1;

        //en
        $input['lang'] = 'en';
        $resource = ProductHotResourceEn::class;
        $this->cacheProductData('product_hot_data', 'product_hot_data_en', $input, $resource);

        //vi
        $input['lang'] = 'vi';
        $resource = ProductHotResource::class;
        $this->cacheProductData('product_hot_data', 'product_hot_data_en', $input, $resource);
    }

    public function cacheProductNew($input)
    {
        $input['is_new'] = 1;

        //en
        $input['lang'] = 'en';
        $resource = ProductHotResourceEn::class;
        $this->cacheProductData('product_new_data', 'product_new_data_en', $input, $resource);

        //vi
        $input['lang'] = 'vi';
        $resource = ProductHotResource::class;
        $this->cacheProductData('product_new_data', 'product_new_data_en', $input, $resource);
    }

    public function cacheProductUpcoming($input)
    {

        $input['is_upcoming'] = 1;

        //en
        $input['lang'] = 'en';
        $resource = ProductHotResourceEn::class;
        $this->cacheProductData('product_upcoming_data', 'product_upcoming_data_en', $input, $resource);

        //vi
        $input['lang'] = 'vi';
        $resource = ProductHotResource::class;
        $this->cacheProductData('product_upcoming_data', 'product_upcoming_data_en', $input, $resource);
    }

    public function cacheProductUniform($input)
    {
        $input['is_uniform'] = 1;

        //en
        $input['lang'] = 'en';
        $resource = ProductHotResourceEn::class;
        $this->cacheProductData('product_uniform_data', 'product_uniform_data_en', $input, $resource);

        //vi
        $input['lang'] = 'vi';
        $resource = ProductHotResource::class;
        $this->cacheProductData('product_uniform_data', 'product_uniform_data_en', $input, $resource);
    }

    public function cacheProductGhiLe($input)
    {
        $cacheKey = 'product_ghi_le';
        $cacheKeyEn = 'product_ghi_le_en';
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


        if (!empty($input['lang']) && $input['lang'] == 'en') {
            if (!empty($input['search'])) {
                $input['name_en'] = ['like' => $input['search']];
            }
            $result = $this->search($input,
                [
                    'attributeVariants',
                    //                'variants' => function ($query) {
                    //                    $query->where('qty', '>', 0);
                    //                }
                ], $limit);
            Cache::put($cacheKeyEn, ProductResourceEn::collection($result), $seconds);
        } else {
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
    }

    public function cacheProductAll()
    {
        $cacheKey = 'product_all';
        $cacheKeyEn = 'product_all_en';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        if (!empty($input['lang']) && $input['lang'] == 'en') {
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
                ->where('is_active', 1)
                ->whereNotNull('slug_en')
                ->whereIn('slug', [
                    'ao-phan-quang-thun-2-ben',
                    'ao-phan-quang-ha-noi',
                    'ao-phan-quang-kieu-3m',
                    'ao-phan-quang-palize',
                    'dong-phuc-cong-nhan',
                ])
                ->get();
            Cache::put($cacheKeyEn, CategorySearchAllResourceEn::collection($categories), $seconds);
        } else {
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
                ->where('is_active', 1)
                ->whereIn('slug', [
                    'ao-phan-quang-thun-2-ben',
                    'ao-phan-quang-ha-noi',
                    'ao-phan-quang-kieu-3m',
                    'ao-phan-quang-palize',
                    'dong-phuc-cong-nhan',
                ])
                ->get();
            Cache::put($cacheKey, CategorySearchAllResource::collection($categories), $seconds);
        }
    }

    public function search($input = [], $with = [], $limit = null)
    {
        $attributeColumn = 'slug';
        $lang = null;
        if (!empty($input['lang']) && $input['lang'] == 'en') {
            $attributeColumn = 'slug_en';
            $lang = '_en';
        }

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


        $query->whereNotNull($attributeColumn)
            ->whereHas('media', function ($query) {
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
            $query->whereHas('category', function ($query) use ($categoryName, $lang) {
                // Điều kiện cho hình ảnh
                $query->where(DB::raw(("REPLACE(REPLACE(name$lang, 'Đ', 'd'), 'ư', 'u')")), 'like', "%$categoryName%");
            });
        }

        if (!empty($categorySlug)) {
            $query->whereHas('category', function ($query) use ($categorySlug, $lang) {
                $category = Category::with(['descendants'])
                    ->where("slug$lang", $categorySlug)
                    ->orWhere("slug", $categorySlug)
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


    public function show($slug, $data = [])
    {
        $attributeColumn = 'slug';
        if (!empty($data['lang']) && $data['lang'] == 'en') {
            $attributeColumn = 'slug_en';
        }

        $item = Product::with([
            'attributeVariants',
            'variants'          => function ($query) {
                $query->where('qty', '>', 0);
            },
            'reviews'           => function ($query) {
                $query->orderByDesc('rate')->first();
            },
            'variantMainDetail' => function ($query) use ($data) {
                if (!empty($data['code'])) {
                    $query->whereHas('variants', function ($qr) use ($data) {
                        $qr->where('code', $data['code']);
                    });
                }
            }
        ])->where($attributeColumn, $slug)
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

        if (!empty($data['lang']) && $data['lang'] == 'en') {
            return new ProductDetailResourceEn($item);
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
                    $q->where('slug', $slug)
                        ->orWhere('slug_en', $slug);
                });
            })
            ->orderByDesc('date')
            ->paginate($limit);

        return ProductReviewResource::collection($query);
    }


    protected function formatPaginatedResponse($resourceClass, $result)
    {
        return [
            'data'  => $resourceClass::collection($result)->resolve(),
            'links' => [
                'first' => $result->url(1),
                'last'  => $result->url($result->lastPage()),
                'prev'  => $result->previousPageUrl(),
                'next'  => $result->nextPageUrl(),
            ],
            'meta'  => [
                'current_page' => $result->currentPage(),
                'from'         => $result->firstItem(),
                'last_page'    => $result->lastPage(),
                'links'        => $result->linkCollection(),
                'path'         => $result->path(),
                'per_page'     => $result->perPage(),
                'to'           => $result->lastItem(),
                'total'        => $result->total(),
            ],
        ];
    }


    protected function prepareSearchInput(array $input): array
    {
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $sorts = ['id' => 'desc'];

        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        $input['is_active'] = 1;

        $lang = $input['lang'] ?? null;
        if (!empty($input['search'])) {
            $key = $lang === 'en' ? 'name_en' : 'name';
            $input[$key] = ['like' => $input['search']];
        }

        return $input;
    }

    protected function getProductCache(string $cacheKey, string $cacheKeyEn, array $input, string $resourceClass)
    {
        $seconds = 365 * 24 * 60 * 60;
        $limit = Arr::get($input, 'limit', 999);
        $input = $this->prepareSearchInput($input);
        $lang = $input['lang'] ?? null;

        return Cache::remember($lang === 'en' ? $cacheKeyEn : $cacheKey, $seconds, function () use ($input, $limit, $resourceClass) {
            $result = $this->search($input, ['attributeVariants'], $limit);
            return $this->formatPaginatedResponse($resourceClass, $result);
        });
    }

    protected function cacheProductData(string $cacheKey, string $cacheKeyEn, array $input, string $resourceClass)
    {
        $seconds = 365 * 24 * 60 * 60;
        $limit = Arr::get($input, 'limit', 999);
        $input = $this->prepareSearchInput($input);
        $result = $this->search($input, ['attributeVariants'], $limit);
        $lang = $input['lang'] ?? null;

        Cache::put($lang === 'en' ? $cacheKeyEn : $cacheKey, $this->formatPaginatedResponse($resourceClass, $result), $seconds);
    }

    protected function getProductResult(array $input, string $resourceClass)
    {
        $limit = Arr::get($input, 'limit', 999);
        $input = $this->prepareSearchInput($input);
        $result = $this->search($input, ['attributeVariants'], $limit);

        return $this->formatPaginatedResponse($resourceClass, $result);;
    }
}
