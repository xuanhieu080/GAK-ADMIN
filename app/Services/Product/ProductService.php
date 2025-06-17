<?php

namespace App\Services\Product;

use App\Http\Resources\ProductDetailMainResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductReviewResource;
use App\Http\Resources\VariantResource;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductVariantMain;
use App\Models\Variant;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use App\V1\Models\CategoryModel;
use App\V1\Models\ProductModel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public $model, $categoryModel, $productModel;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Product();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get a single resource from the database
     * @param Product $product
     * @return ProductResource
     */
    public function get(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Get a single resource from the database
     * @param Product $product
     * @return ProductResource
     */
    public function getReviewItem(Product $product, ProductReview $productReview)
    {
        if ($productReview->product_id != $product->id) {
            return null;
        }
        return new ProductReviewResource($productReview);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $query = Product::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return ProductResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return ProductResource
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $data['code'] = Support::genCode('products', 'code');
            $image = $data['image'];
            $thumbImage = Arr::get($data, 'thumb_image', []);
            $full_columns = $this->model->getFillable();
            $data = array_intersect_key($data, array_flip($full_columns));

            $data['is_active'] = filter_var(Arr::get($data, 'is_active'), FILTER_VALIDATE_BOOLEAN);
            $data['is_hot'] = filter_var(Arr::get($data, 'is_hot'), FILTER_VALIDATE_BOOLEAN);
            $data['is_upcoming'] = filter_var(Arr::get($data, 'is_upcoming'), FILTER_VALIDATE_BOOLEAN);
            $data['is_new'] = filter_var(Arr::get($data, 'is_new'), FILTER_VALIDATE_BOOLEAN);
            $data['is_uniform'] = filter_var(Arr::get($data, 'is_uniform'), FILTER_VALIDATE_BOOLEAN);
            $data['price_discount'] = filter_var(Arr::get($data, 'price'), FILTER_VALIDATE_INT) - filter_var(Arr::get($data, 'discount'), FILTER_VALIDATE_INT);
            $data['price_discount_en'] = filter_var(Arr::get($data, 'price_en'), FILTER_VALIDATE_INT) - filter_var(Arr::get($data, 'discount_en'), FILTER_VALIDATE_INT);

            $record = Product::query()->create($data);
            $record->addMedia($image)
                ->usingName($record->name)
                ->usingFileName($record->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                ->toMediaCollection();

            foreach ($thumbImage as $key => $file) {
                $record->addMedia($file)
                    ->usingName($record->name)
                    ->usingFileName($record->slug . '-' . $key . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('thumb');
            }
            DB::commit();
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return new ProductResource($record);
    }

    /**
     * Updates resource in the database
     * @param Product|Model $product
     * @param array $data
     * @return ProductResource
     */
    public function updateItem(Product $product, array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);
            $price = Arr::get($data, 'price', $product->price);
            $priceEn = Arr::get($data, 'price_en', $product->price_en);
            $discount = Arr::get($data, 'discount', $product->discount);
            $discountEn = Arr::get($data, 'discount_en', $product->discount_en);

            $product->name = Arr::get($data, 'name', $product->name);
            $product->name_en = Arr::get($data, 'name_en', $product->name_en);
            $product->description = Arr::get($data, 'description', $product->description);
            $product->description_en = Arr::get($data, 'description_en', $product->description_en);
            $product->price = $price;
            $product->price_en = $priceEn;
            $product->discount = $discount;
            $product->discount_en = $discountEn;
            $product->price_discount = $price - $discount;
            $product->price_discount_en = $priceEn - $discountEn;
            $product->category_id = Arr::get($data, 'category_id', $product->category_id);
            $product->qty = Arr::get($data, 'qty', $product->qty);
            $product->is_active = filter_var(Arr::get($data, 'is_active', $product->is_active), FILTER_VALIDATE_BOOLEAN);
            $product->is_hot = filter_var(Arr::get($data, 'is_hot', $product->is_hot), FILTER_VALIDATE_BOOLEAN);
            $product->is_upcoming = filter_var(Arr::get($data, 'is_upcoming', $product->is_upcoming), FILTER_VALIDATE_BOOLEAN);
            $product->is_new = filter_var(Arr::get($data, 'is_new', $product->is_new), FILTER_VALIDATE_BOOLEAN);
            $product->is_uniform = filter_var(Arr::get($data, 'is_uniform', $product->is_uniform), FILTER_VALIDATE_BOOLEAN);
            $product->priority = Arr::get($data, 'priority', $product->priority);
            $product->slug = Arr::get($data, 'slug', $product->slug);
            $product->slug_en = Arr::get($data, 'slug_en', $product->slug_en);
            $product->meta_title = Arr::get($data, 'meta_title', $product->meta_title);
            $product->meta_title_en = Arr::get($data, 'meta_title_en', $product->meta_title_en);
            $product->meta_description = Arr::get($data, 'meta_description', $product->meta_description);
            $product->meta_description_en = Arr::get($data, 'meta_description_en', $product->meta_description_en);
            $product->meta_key = Arr::get($data, 'meta_key', $product->meta_key);
            $product->meta_key_en = Arr::get($data, 'meta_key_en', $product->meta_key_en);
            $product->video_link = Arr::get($data, 'video_link', $product->video_link);
            if (!empty($data['image'])) {
                $image = $data['image'];
                $media = $product->getMedia('default')->first();
                if (!empty($media)) {
                    $media->delete();
                }
                $product->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection();
            }

            foreach ((array)Arr::get($data, 'thumb_image', []) as $image) {
                $product->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('thumb');
            }

            foreach ((array)Arr::get($data, 'thumb_image_remove', []) as $file) {
                $media = $product->getMedia('thumb')->where('file_name', $file)->first();
                if (!empty($media)) {
                    $media->delete();
                }
            }

            $product->save();
            $product->refresh();
            DB::commit();

            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return new ProductResource($product);
    }

    /**
     * Updates resource in the database
     * @param Product|Model $product
     * @param array $data
     * @return ProductResource
     */
    public function attribute(Product $product, array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $detailCurrents = Arr::get($data, 'detail_currents', []);

            $valueIds = Arr::pluck($detailCurrents, 'id');

            Variant::where('product_id', $product->id)
                ->whereNotIn('id', $valueIds)
                ->delete();
            $keys = ['attribute_id', 'attribute_group_id', 'product_id', 'is_hot'];
            $values = [];
            $result = [];

            foreach ($detailCurrents as $subArray) {
                $subArray['product_id'] = $product->id;
                $subArray['is_hot'] = filter_var(Arr::get($subArray, 'is_hot'), FILTER_VALIDATE_BOOLEAN);
                $subArray['is_main'] = filter_var(Arr::get($subArray, 'is_main'), FILTER_VALIDATE_BOOLEAN);

                $hash = '';
                foreach ($keys as $key) {
                    $hash .= $subArray[$key] . '|';
                }

                if (!isset($values[$hash])) {
                    $values[$hash] = true;
                    $result[] = $subArray;
                }
            }

            if (!empty($result)) {
                Variant::upsert($result, ['id'], ['attribute_id', 'attribute_group_id', 'product_id', 'is_hot', 'is_main']);
            }

            $params = [];
            $details = Arr::get($data, 'details', []);
            foreach ($details as $detail) {
                $params[] = [
                    'attribute_id'       => $detail['attribute_id'],
                    'attribute_group_id' => $detail['attribute_group_id'],
                    'product_id'         => $product->id,
                    'is_hot'             => filter_var(Arr::get($detail, 'is_hot'), FILTER_VALIDATE_BOOLEAN),
                    'is_main'            => filter_var(Arr::get($detail, 'is_main'), FILTER_VALIDATE_BOOLEAN),
                ];
            }

            $keys = ['attribute_id', 'attribute_group_id', 'product_id', 'is_hot', 'is_main'];
            $values = [];
            $result = [];

            foreach ($params as $subArray) {
                $subArray['is_hot'] = filter_var(Arr::get($subArray, 'is_hot'), FILTER_VALIDATE_BOOLEAN);
                $subArray['is_main'] = filter_var(Arr::get($subArray, 'is_main'), FILTER_VALIDATE_BOOLEAN);
                $hash = '';
                foreach ($keys as $key) {
                    $hash .= $subArray[$key] . '|';
                }

                if (!isset($values[$hash])) {
                    $values[$hash] = true;
                    $result[] = $subArray;
                }
            }
            if (!empty($result)) {
                Variant::upsert($result, ['attribute_id', 'attribute_group_id', 'product_id', 'is_hot', 'is_main']);
            }

            $this->syncProductVariantMain($product);
            $this->sync($product);
            DB::commit();
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return new ProductResource($product);
    }

    /**
     * Deletes resource in the database
     * @param Product|Model $product
     * @return bool
     */
    public function delete(Product $product)
    {
        HasImage::deleteImage($product->image);
        $bool = $product->delete();

        $this->categoryModel->cacheCategoryHeader([]);
        $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
        $this->productModel->cacheProductAll();
        $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
        $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
        $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
        $this->productModel->cacheProductUniform([ 'limit' => 4]);
        return $bool;
    }

    /**
     * Clean the data
     * @param array $data
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
     * Clean the data
     * @param array $data
     * @return array
     */
    private function deleteNull(array $data)
    {
        foreach ($data as $i => $row) {
            if (is_null($row)) {
                unset($data[$i]);
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

    // Hàm callback để xoá key trong mảng
    function removeKey($array, $keyToRemove)
    {
        return array_map(function ($item) use ($keyToRemove) {
            return array_filter($item, function ($key) use ($keyToRemove) {
                return $key !== $keyToRemove;
            }, ARRAY_FILTER_USE_KEY);
        }, $array);
    }

    public function getAttribute(Product $product)
    {
        $data = Variant::query()
            ->where('product_id', $product->id)
            ->orderByDesc('is_hot')
            ->orderByDesc('is_main')
            ->orderBy('attribute_group_id')
            ->orderBy('attribute_id')
            ->get();

        return VariantResource::collection($data);
    }

    public function productVariantSync(Product $product)
    {
        try {
            DB::beginTransaction();
            $this->syncProductVariantMain($product);
            $this->sync($product);
            DB::commit();

            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()]);
//            throw new \Exception($exception->getMessage());
        }

        return response()->json(['message' => 'Đồng bộ thành công']);
    }

    public function sync(Product $product): void
    {
        $variants = Variant::with(['product', 'attribute', 'attributeGroup'])
            ->where('product_id', $product->id)
            ->get()
            ->groupBy('attribute_group_id')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'attribute_group_id'       => $item['attributeGroup']['id'],
                        'attribute_group_name'     => $item['attributeGroup']['name'],
                        'attribute_group_priority' => $item['attributeGroup']['priority'],
                        'attribute_id'             => $item['attribute']['id'],
                        'attribute_name'           => $item['attribute']['name'],
                        'product_id'               => $item['product']['id'],
                        'product_name'             => $item['product']['name'],
                        'price'                    => $item['product']['price'],
                        'price_en'                    => $item['product']['price_en'],
                        'qty'                      => $item['product']['qty'],
                        'discount'                 => $item['product']['discount'],
                        'discount_en'                 => $item['product']['discount_en'],
                        'price_discount'           => $item['product']['price_discount'],
                        'price_discount_en'           => $item['product']['price_discount_en'],
                        'description'              => $item['product']['description'],
                        'meta_description'         => $item['product']['meta_description'],
                        'meta_key'                 => $item['product']['meta_key'],
                        'meta_title'               => $item['product']['meta_title'],
                    ];
                })->sortBy('attribute_name');
            })
            ->sortBy(function ($values, $key) {
                return $values->first()['attribute_group_priority']; // Sắp xếp theo attribute group name
            })
            ->values()
            ->toArray();

        $result = $this->combining($variants);

        $details = array_map(function ($itemGroup) use ($product) {
            $name = '';
            $params = '';
            $attributes = [];
            $attributeAll = [];
            $attributeGroup = [];
            foreach ($itemGroup as $key => $item) {
                $attributes[] = $item['attribute_id'];
                $attributeGroup[] = $item['attribute_group_id'];
                $attributeAll[] = [
                    'attribute_group_id' => $item['attribute_group_id'],
                    'attribute_id'       => $item['attribute_id'],
                ];

                $name .= sprintf(', %s: %s', $item['attribute_group_name'], $item['attribute_name']);
                $params .= sprintf('&%s=%s', Str::slug($item['attribute_group_name']), Str::slug($item['attribute_name']));
            }

            sort($attributes);
            return [
                'name'             => "$product->name$name",
                'code'             => Support::genCode('product_variants', 'code', 16),
                'product_id'       => $product->id,
                'option_name'      => trim($name, ', '),
                'params'           => trim($params, '&'),
                'options'          => json_encode($attributes),
                'option_all'       => json_encode($attributeAll),
                'option_group'     => json_encode($attributeGroup),
                'description'      => $product->description,
                'meta_description' => $product->meta_description,
                'meta_key'         => $product->meta_key,
                'meta_title'       => $product->meta_title,
                'price'            => $product->price,
                'price_en'            => $product->price_en,
                'qty'              => $product->qty,
                'discount'         => $product->discount,
                'discount_en'         => $product->discount_en,
                'price_discount'   => $product->price_discount,
                'price_discount_en'   => $product->price_discount_en,
            ];
        }, $result);

        $data = ProductVariant::query()
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                $item->options = json_encode($item->options);
                return $item;
            })
            ->toArray();

        $differences = array_udiff($details, $data, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });

        $differenceRemove = array_udiff($data, $differences, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });

        $differenceRemove = array_udiff($differenceRemove, $details, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });

        $ids = array_map(function ($item) {
            return $item['id'];
        }, $differenceRemove);

        ProductVariant::query()->insert($differences);
        ProductVariant::query()->whereIn('id', $ids)->delete();

        $productMains = ProductVariantMain::query()->where('product_id', $product->id)
            ->pluck('options', 'id')
            ->toArray();

        $productVariants = ProductVariant::query()
            ->where('product_id', $product->id)
            ->get();

        // Lặp qua từng `variant` một lần duy nhất.
        foreach ($productVariants as $variant) {
            // Lặp qua các `productMains` và kiểm tra điều kiện.
            foreach ($productMains as $productVariantId => $productMain) {
                if (empty(array_diff($productMain, $variant['options']))) {
                    // Nếu `options` của `productMain` đều có trong `variant`, thêm `variant` vào kết quả.
                    $variant->product_main_id = $productVariantId;
                    $variant->save();
                    // Chỉ cần phù hợp với một `productMain` là đủ, không cần kiểm tra thêm.
                    break;
                }
            }
        }
    }

    public function syncProductVariantMain(Product $product): void
    {
//        try {
        $variants = Variant::with(['product', 'attribute', 'attributeGroup'])
            ->where('product_id', $product->id)
            ->where('is_main', 1)
            ->get()
            ->groupBy('attribute_group_id')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'attribute_group_id'       => $item['attributeGroup']['id'],
                        'attribute_group_name'     => $item['attributeGroup']['name'],
                        'attribute_group_priority' => $item['attributeGroup']['priority'],
                        'attribute_id'             => $item['attribute']['id'],
                        'attribute_name'           => $item['attribute']['name'],
                        'product_id'               => $item['product']['id'],
                        'product_name'             => $item['product']['name'],
                        'price'                    => $item['product']['price'],
                        'price_en'                    => $item['product']['price_en'],
                        'qty'                      => $item['product']['qty'],
                        'discount'                 => $item['product']['discount'],
                        'discount_en'                 => $item['product']['discount_en'],
                        'price_discount'           => $item['product']['price_discount'],
                        'price_discount_en'           => $item['product']['price_discount_en'],
                        'description'              => $item['product']['description'],
                        'meta_description'         => $item['product']['meta_description'],
                        'meta_key'                 => $item['product']['meta_key'],
                        'meta_title'               => $item['product']['meta_title'],
                    ];
                })->sortBy('attribute_name');
            })
            ->sortBy(function ($values, $key) {
                return $values->first()['attribute_group_priority']; // Sắp xếp theo attribute group name
            })
            ->values()
            ->toArray();

        $result = $this->combining($variants);

        $details = array_map(function ($itemGroup) use ($product) {
            $name = '';
            $params = '';
            $attributes = [];
            $attributeAll = [];
            $attributeGroup = [];
            foreach ($itemGroup as $key => $item) {
                $attributes[] = $item['attribute_id'];
                $attributeGroup[] = $item['attribute_group_id'];
                $attributeAll[] = [
                    'attribute_group_id' => $item['attribute_group_id'],
                    'attribute_id'       => $item['attribute_id'],
                ];

                $name .= sprintf(', %s: %s', $item['attribute_group_name'], $item['attribute_name']);
                $params .= sprintf('&%s=%s', Str::slug($item['attribute_group_name']), Str::slug($item['attribute_name']));
            }

            sort($attributes);
            return [
                'name'             => "$product->name$name",
                'code'             => Support::genCode('product_variant_mains', 'code', 16),
                'product_id'       => $product->id,
                'option_name'      => trim($name, ', '),
                'params'           => trim($params, '&'),
                'options'          => json_encode($attributes),
                'option_all'       => json_encode($attributeAll),
                'option_group'     => json_encode($attributeGroup),
                'description'      => $product->description,
                'meta_description' => $product->meta_description,
                'meta_key'         => $product->meta_key,
                'meta_title'       => $product->meta_title,
            ];
        }, $result);

        $data = ProductVariantMain::query()
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                $item->options = json_encode($item->options);
                return $item;
            })
            ->toArray();
        $differences = array_udiff($details, $data, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });

        $differenceRemove = array_udiff($data, $differences, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });


        $differenceRemove = array_udiff($differenceRemove, $details, function ($item1, $item2) {
            return $item1['options'] <=> $item2['options'];
        });

        $ids = array_map(function ($item) {
            return $item['id'];
        }, $differenceRemove);

        ProductVariantMain::query()->insert($differences);
        $productVariants = ProductVariantMain::query()->whereIn('id', $ids)->get();
        foreach ($productVariants as $productVariant) {
            $productVariant->delete();
        }
//        } catch (\Exception $e) {
//            dd($e);
//        }
    }


    function combining($arrays, $index = 0, $result = array())
    {
        if ($index === count($arrays)) {
            return $result;
        }

        $return = array();
        if (empty($result)) {
            foreach ($arrays[$index] as $element) {
                $return[] = array($element);
            }
        } else {
            foreach ($result as $res) {
                foreach ($arrays[$index] as $element) {
                    $temp = $res;
                    $temp[] = $element;
                    $return[] = $temp;
                }
            }
        }
        return $this->combining($arrays, $index + 1, $return);
    }

    public function productVariant(Product $product)
    {
        return response()->json(['model' => new ProductDetailResource($product)]);
    }

    public function productVariantMain(Product $product)
    {
        return response()->json(['model' => new ProductDetailMainResource($product)]);
    }

    public function updateProductVariant(Product $product, $input)
    {
        try {
            DB::beginTransaction();
            $details = Arr::get($input, 'details', []);
            foreach ($details as $data) {
                $data = $this->clean($data);
                $productVariant = ProductVariant::find($data['id']);

                $price = Arr::get($data, 'price', $productVariant->price);
                $priceEn = Arr::get($data, 'price_en', $productVariant->price_en);
                $discount = Arr::get($data, 'discount', $productVariant->discount);
                $discountEn = Arr::get($data, 'discount_en', $productVariant->discount_en);
                $productVariant->name = Arr::get($data, 'name', $productVariant->name);
                $productVariant->description = Arr::get($data, 'description', $productVariant->description);
                $productVariant->price = $price;
                $productVariant->price_en = $priceEn;
                $productVariant->discount = $discount;
                $productVariant->discount_en = $discountEn;
                $productVariant->price_discount = $price - $discount;
                $productVariant->price_discount_en = $priceEn - $discountEn;
                $productVariant->qty = Arr::get($data, 'qty', $productVariant->qty);
                $productVariant->is_active = filter_var(Arr::get($data, 'is_active', $productVariant->is_active), FILTER_VALIDATE_BOOLEAN);
//                $productVariant->priority = Arr::get($data, 'priority', $productVariant->priority);
//                $productVariant->slug = Arr::get($data, 'slug', $productVariant->slug);
                $productVariant->meta_title = Arr::get($data, 'meta_title', $productVariant->meta_title);
                $productVariant->meta_description = Arr::get($data, 'meta_description', $productVariant->meta_description);
                $productVariant->meta_key = Arr::get($data, 'meta_key', $productVariant->meta_key);
                $productVariant->params = Arr::get($data, 'params', $productVariant->params);
                if (!empty($data['image'])) {
                    $image = $data['image'];
                    $mediaItem = $productVariant->getMedia('default')->first();
                    if (!empty($mediaItem)) {
                        $mediaItem->delete();
                    }
                    $productVariant->addMedia($image)
                        ->usingName($productVariant->name)
                        ->usingFileName("$productVariant->slug-$productVariant->id" . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                        ->toMediaCollection();
                }

                foreach ((array)Arr::get($data, 'thumb_image_remove', []) as $file) {
                    $media = $productVariant->getMedia('thumb')->where('file_name', $file)->first();
                    if (!empty($media)) {
                        $media->delete();
                    }
                }

                foreach ((array)Arr::get($data, 'thumb_image', []) as $image) {
                    $productVariant->addMedia($image)
                        ->usingName($product->name)
                        ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                        ->toMediaCollection('thumb');
                }

                $productVariant->save();
            }
            DB::commit();
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    public function createReview(Product $product, $input)
    {
        try {
            DB::beginTransaction();

            $data = $this->clean($input);
            $param = [
                'customer_name' => $data['customer_name'],
                'description'   => $data['description'],
                'rate'          => $data['rate'],
                'reply'         => Arr::get($data, 'reply'),
                'date'          => Carbon::parse($data['date']),
                'product_id'    => $product->id,
                'product_code'  => $product->code,
            ];
            if (!empty($data['product_variant_id'])) {
                $productVariant = ProductVariant::find($data['product_variant_id']);
                $param['product_variant_id'] = $productVariant->id;
                $param['product_variant_code'] = $productVariant->code;
                $param['option_name'] = $productVariant->option_name;
            }

            $item = ProductReview::create($param);
            foreach ((array)Arr::get($data, 'thumb_image', []) as $image) {
                $item->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('thumb');
            }

            $product->rate += $data['rate'];
            $product->rate_count += 1;
            $product->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    public function updateReview(Product $product, ProductReview $productReview, $input)
    {
        try {
            DB::beginTransaction();

            $data = $this->clean($input);
            if ($data['rate'] != $productReview->rate) {
                $product->rate += $data['rate'] - $productReview->rate;
                $product->save();
            }
            $productReview->customer_name = Arr::get($data, 'customer_name', $productReview->customer_name);
            $productReview->description = Arr::get($data, 'description', $productReview->description);
            $productReview->rate = Arr::get($data, 'rate', $productReview->rate);
            $productReview->reply = Arr::get($data, 'reply', $productReview->reply);
            $productReview->date = Carbon::parse($data['date']);


            if (!empty($data['product_variant_id'])) {
                $productVariant = ProductVariant::find($data['product_variant_id']);
                $productReview->product_variant_id = $productVariant->id;
                $productReview->product_variant_code = $productVariant->code;
                $productReview->option_name = $productVariant->option_name;
            } else {
                $productReview->product_variant_id = null;
                $productReview->product_variant_code = null;
                $productReview->option_name = null;
            }

            $productReview->save();
            foreach ((array)Arr::get($data, 'thumb_image_remove', []) as $file) {
                $media = $productReview->getMedia('thumb')->where('file_name', $file)->first();
                if (!empty($media)) {
                    $media->delete();
                }
            }

            foreach ((array)Arr::get($data, 'thumb_image', []) as $image) {
                $productReview->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('thumb');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    public function deleteReview(Product $product, ProductReview $productReview)
    {
        $product->rate -= $productReview->rate;
        $product->rate_count -= 1;
        $product->save();
        return $productReview->delete();
    }

    public function getReview(Product $product, $input)
    {
        $customerName = Arr::get($input, 'customer_name');
        $limit = Arr::get($input, 'limit', 10);
        $query = ProductReview::query()
            ->where('product_id', $product->id)
            ->when($customerName, function ($query, string $customerName) {
                return $query->where('customer_name', 'like', "%$customerName%");
            })
            ->orderByDesc('date')
            ->paginate($limit);

        return ProductReviewResource::collection($query);
    }

    public function updateProductVariantItem(ProductVariant $productVariant, $input)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($input);

            $price = Arr::get($data, 'price', $productVariant->price);
            $priceEn = Arr::get($data, 'price_en', $productVariant->price_en);
            $discount = Arr::get($data, 'discount', $productVariant->discount);
            $discountEn = Arr::get($data, 'discount_en', $productVariant->discount_en);
            $productVariant->name = Arr::get($data, 'name', $productVariant->name);
            $productVariant->code = Arr::get($data, 'code', $productVariant->code);
            $productVariant->description = Arr::get($data, 'description', $productVariant->description);
            $productVariant->price = $price;
            $productVariant->price_en = $priceEn;
            $productVariant->discount = $discount;
            $productVariant->discount_en = $discountEn;
            $productVariant->price_discount = $price - $discount;
            $productVariant->price_discount_en = $priceEn - $discountEn;
            $productVariant->qty = Arr::get($data, 'qty', $productVariant->qty);
//                $productVariant->is_active = filter_var(Arr::get($data, 'is_active', $productVariant->is_active), FILTER_VALIDATE_BOOLEAN);
            $productVariant->save();
            DB::commit();
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    public function updateProductVariantMainItem(Product $product, ProductVariantMain $productVariant, $input)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($input);

            $productVariant->name = Arr::get($data, 'name', $productVariant->name);
            $productVariant->name_en = Arr::get($data, 'name_en', $productVariant->name_en);
            $productVariant->description = Arr::get($data, 'description', $productVariant->description);
            $productVariant->description_en = Arr::get($data, 'description_en', $productVariant->description_en);

            $productVariant->is_active = filter_var(Arr::get($data, 'is_active', $productVariant->is_active), FILTER_VALIDATE_BOOLEAN);
//                $productVariant->priority = Arr::get($data, 'priority', $productVariant->priority);
//                $productVariant->slug = Arr::get($data, 'slug', $productVariant->slug);
            $productVariant->meta_title = Arr::get($data, 'meta_title', $productVariant->meta_title);
            $productVariant->meta_title_en = Arr::get($data, 'meta_title_en', $productVariant->meta_title_en);
            $productVariant->meta_description = Arr::get($data, 'meta_description', $productVariant->meta_description);
            $productVariant->meta_description_en = Arr::get($data, 'meta_description_en', $productVariant->meta_description_en);
            $productVariant->meta_key = Arr::get($data, 'meta_key', $productVariant->meta_key);
            $productVariant->meta_key_en = Arr::get($data, 'meta_key_en', $productVariant->meta_key_en);
            $productVariant->params = Arr::get($data, 'params', $productVariant->params);
            if (!empty($data['image'])) {
                $image = $data['image'];
                $mediaItem = $productVariant->getMedia('default')->first();
                if (!empty($mediaItem)) {
                    $mediaItem->delete();
                }
                $productVariant->addMedia($image)
                    ->usingName($productVariant->name)
                    ->usingFileName("$productVariant->slug-$productVariant->id" . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection();
            }

            foreach ((array)Arr::get($data, 'thumb_image_remove', []) as $file) {
                $media = $productVariant->getMedia('thumb')->where('file_name', $file)->first();
                if (!empty($media)) {
                    $media->delete();
                }
            }

            foreach ((array)Arr::get($data, 'thumb_image', []) as $image) {
                $productVariant->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName($product->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('thumb');
            }

            $productVariant->save();
            DB::commit();
            $this->categoryModel->cacheCategoryHeader([]);
            $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
            $this->productModel->cacheProductAll();
            $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
            $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
            $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
            $this->productModel->cacheProductUniform([ 'limit' => 4]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return true;
    }


    public function updateHighlight(Product $product, $input)
    {
        try {
            DB::beginTransaction();

            $data = $this->clean($input);
            $product->highlight = Arr::get($data, 'highlight', $product->highlight);
            $product->highlight_en = Arr::get($data, 'highlight_en', $product->highlight_en);
            if (!empty($data['highlight_image'])) {
                $image = $data['highlight_image'];
                $mediaItem = $product->getMedia('highlight')->first();
                if (!empty($mediaItem)) {
                    $mediaItem->delete();
                }
                $product->addMedia($image)
                    ->usingName($product->name)
                    ->usingFileName("$product->slug-$product->id" . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                    ->toMediaCollection('highlight');
            } elseif (filter_var(Arr::get($data, 'highlight_image_remove'), FILTER_VALIDATE_BOOLEAN)) {
                $mediaItem = $product->getMedia('highlight')->first();
                if (!empty($mediaItem)) {
                    $mediaItem->delete();
                }
            }


            $product->save();
            $product->load(['media']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return new ProductResource($product);
    }

}
