<?php

namespace App\Services\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
            if (!empty($data['file'])) {
                $data['image'] = HasImage::addImage($data['file'], Product::path);
            }

            $details = Arr::get($data, 'details', []);
            $details = array_unique($details);
            $details = $this->deleteNull($details);
            $full_columns = $this->model->getFillable();
            $data = array_intersect_key($data, array_flip($full_columns));

            $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
            $data['qty'] = count($details);

            $record = Product::query()->create($data);
            $params = [];

            foreach ($details as $detail) {
                $params[] = [
                    'description' => $detail,
                    'product_id'  => $record->id,
                    'is_active'   => true,
                    'status'      => 'PENDING',
                ];
            }
            $details = $this->deleteNull($details);
            if (!empty($details)) {
                $record->details()->createMany($params);
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
    public function update(Product $product, array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $product->name = Arr::get($data, 'name', $product->name);
            $product->description = Arr::get($data, 'description', $product->description);
            $product->price = Arr::get($data, 'price', $product->price);
            $product->category_id = Arr::get($data, 'category_id', $product->category_id);
            $product->is_active = filter_var(Arr::get($data, 'is_active', $product->is_active), FILTER_VALIDATE_BOOLEAN);
            $product->priority = Arr::get($data, 'priority', $product->priority);
            if (!empty($data['file'])) {
                $product->image = HasImage::updateImage($data['file'], $product->image, Product::path);
            }

            $product->save();
            $detailCurrents = Arr::get($data, 'detail_currents', []);
            $valueIds = array_column($detailCurrents, 'id');
            ProductDetail::where('product_id', $product->id)
                ->whereNotIn('id',$valueIds)
                ->where('status','PENDING')
                ->delete();

            $keyToRemove = 'title';
            $detailCurrents = $this->removeKey($detailCurrents, $keyToRemove);
            ProductDetail::upsert($detailCurrents,['id'],['description']);

            $details = Arr::get($data, 'details', []);
            $details = array_unique($details);
            $params = [];

            $values = array_column($detailCurrents, 'description');
            foreach ($details as $detail) {
                if (!empty($detail) && !in_array($detail, $values)) {
                    $params[] = [
                        'description' => $detail,
                        'product_id'  => $product->id,
                        'is_active'   => true,
                        'status'      => 'PENDING',
                    ];
                }
            }
            dd(count($detailCurrents),count($details));
            $params = $this->deleteNull($params);
            if (!empty($params)) {
                $product->details()->createMany($params);
            }

            $product->load(['details']);
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
}
