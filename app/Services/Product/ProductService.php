<?php

namespace App\Services\Product;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VariantResource;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Product();
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
            $data['price_discount'] = filter_var(Arr::get($data, 'price'), FILTER_VALIDATE_INT) - filter_var(Arr::get($data, 'discount'), FILTER_VALIDATE_INT);


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
            $discount = Arr::get($data, 'discount', $product->discount);

            $product->name = Arr::get($data, 'name', $product->name);
            $product->description = Arr::get($data, 'description', $product->description);
            $product->price = $price;
            $product->discount = $discount;
            $product->price_discount = $price - $discount;
            $product->category_id = Arr::get($data, 'category_id', $product->category_id);
            $product->qty = Arr::get($data, 'qty', $product->qty);
            $product->is_active = filter_var(Arr::get($data, 'is_active', $product->is_active), FILTER_VALIDATE_BOOLEAN);
            $product->is_hot = filter_var(Arr::get($data, 'is_hot', $product->is_hot), FILTER_VALIDATE_BOOLEAN);
            $product->priority = Arr::get($data, 'priority', $product->priority);
            $product->slug = Arr::get($data, 'slug', $product->slug);
            $product->meta_title = Arr::get($data, 'meta_title', $product->meta_title);
            $product->meta_description = Arr::get($data, 'meta_description', $product->meta_description);
            $product->meta_key = Arr::get($data, 'meta_key', $product->meta_key);
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
            $keys = ['attribute_id', 'attribute_group_id', 'product_id'];
            $values = [];
            $result = [];

            foreach ($detailCurrents as $subArray) {
                $subArray['product_id'] = $product->id;
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
                Variant::upsert($result, ['id'], ['attribute_id', 'attribute_group_id', 'product_id']);
            }

            $params = [];
            $details = Arr::get($data, 'details', []);
            foreach ($details as $detail) {
                $params[] = [
                    'attribute_id'       => $detail['attribute_id'],
                    'attribute_group_id' => $detail['attribute_group_id'],
                    'product_id'         => $product->id,
                ];
            }

            $keys = ['attribute_id', 'attribute_group_id', 'product_id'];
            $values = [];
            $result = [];

            foreach ($params as $subArray) {
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
                Variant::upsert($result, ['attribute_id', 'attribute_group_id', 'product_id']);
            }

            $this->sync($product);
            DB::commit();
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
        return $product->delete();
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
            ->orderBy('attribute_group_id')
            ->orderBy('attribute_id')
            ->get();

        return VariantResource::collection($data);
    }

    public function productVariantSync(Product $product)
    {
        try {
            DB::beginTransaction();
            $this->sync($product);
            DB::commit();
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
                        'qty'                      => $item['product']['qty'],
                        'discount'                 => $item['product']['discount'],
                        'price_discount'           => $item['product']['price_discount'],
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
            $attributes = [];
            foreach ($itemGroup as $key => $item) {
                $attributes[] = $item['attribute_id'];

                $name .= sprintf(', %s: %s', $item['attribute_group_name'], $item['attribute_name']);
            }

            sort($attributes);
            return [
                'name'             => "$product->name$name",
                'product_id'       => $product->id,
                'option_name'      => trim($name, ', '),
                'options'          => json_encode($attributes),
                'description'      => $product->description,
                'meta_description' => $product->meta_description,
                'meta_key'         => $product->meta_key,
                'meta_title'       => $product->meta_title,
                'price'            => $product->price,
                'qty'              => $product->qty,
                'discount'         => $product->discount,
                'price_discount'   => $product->price_discount,
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
        $productVariants = ProductVariant::query()->whereIn('id', $ids)->get();
        foreach ($productVariants as $productVariant) {
            $productVariant->delete();
        }
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

    public function updateProductVariant(Product $product, $input)
    {
        try {
            DB::beginTransaction();
            $details = Arr::get($input, 'details', []);
            foreach ($details as $data) {
                $data = $this->clean($data);
                $productVariant = ProductVariant::find($data['id']);

                $price = Arr::get($data, 'price', $productVariant->price);
                $discount = Arr::get($data, 'discount', $productVariant->discount);
                $productVariant->name = Arr::get($data, 'name', $productVariant->name);
                $productVariant->description = Arr::get($data, 'description', $productVariant->description);
                $productVariant->price = $price;
                $productVariant->discount = $discount;
                $productVariant->price_discount = $price - $discount;
                $productVariant->qty = Arr::get($data, 'qty', $productVariant->qty);
                $productVariant->is_active = filter_var(Arr::get($data, 'is_active', $productVariant->is_active), FILTER_VALIDATE_BOOLEAN);
//                $productVariant->priority = Arr::get($data, 'priority', $productVariant->priority);
//                $productVariant->slug = Arr::get($data, 'slug', $productVariant->slug);
                $productVariant->meta_title = Arr::get($data, 'meta_title', $productVariant->meta_title);
                $productVariant->meta_description = Arr::get($data, 'meta_description', $productVariant->meta_description);
                $productVariant->meta_key = Arr::get($data, 'meta_key', $productVariant->meta_key);
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
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        return true;
    }
}
