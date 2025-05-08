<?php

namespace App\Services\Product;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Services\Media\MediaService;
use App\V1\Models\CategoryModel;
use App\V1\Models\ProductModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AttributeService
{
    public $model, $categoryModel, $productModel;

    /**
     * The service instance
     * @var Attribute
     */
    public function __construct()
    {
        $this->model = new Attribute();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get a single resource from the database
     * @param Attribute $attribute
     * @return AttributeResource
     */
    public function get(Attribute $attribute)
    {
        return new AttributeResource($attribute);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Attribute::query();
        if (!empty($data['search'])) {
            $query = $query->where('name','like', '%'.$data['search'].'%');
        }
        if (!empty($data['group_id'])) {
            $query = $query->where('group_id', $data['group_id']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return AttributeResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return AttributeResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));

        $record = Attribute::query()->create($data);
        if (!empty($record)) {
            return new AttributeResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Attribute|Model $attribute
     * @param array $data
     * @return AttributeResource
     */
    public function update(Attribute $attribute, array $data)
    {
        $data = $this->clean($data);

        $attribute->name = Arr::get($data, 'name', $attribute->name);
        $attribute->name_en = Arr::get($data, 'name_en', $attribute->name_en);
        $attribute->group_id = Arr::get($data, 'group_id', $attribute->group_id);
        $attribute->color = Arr::get($data, 'color', $attribute->color);

        $attribute->save();

        $this->categoryModel->cacheCategoryHeader([]);
        $this->productModel->cacheProductHot(['is_hot' => 1,'limit'  => 20]);
        $this->productModel->cacheProductNew(['is_new' => 1,'limit'  => 20]);
        $this->productModel->cacheProductUpcoming(['is_upcoming' => 1,'limit'  => 4]);
        $this->productModel->cacheProductUniform(['is_uniform' => 1,'limit'  => 20]);
        $this->productModel->cacheProductUniform(['is_uniform' => 1,'limit'  => 20]);
        return new AttributeResource($attribute);
    }

    /**
     * Deletes resource in the database
     * @param Attribute|Model $attribute
     * @return bool
     */
    public function delete(Attribute $attribute)
    {
        $bool = $attribute->delete();
        $this->categoryModel->cacheCategoryHeader([]);
        $this->productModel->cacheProductHot(['is_hot' => 1,'limit'  => 20]);
        $this->productModel->cacheProductNew(['is_new' => 1,'limit'  => 20]);
        $this->productModel->cacheProductUpcoming(['is_upcoming' => 1,'limit'  => 4]);
        $this->productModel->cacheProductUniform(['is_uniform' => 1,'limit'  => 20]);
        $this->productModel->cacheProductUniform(['is_uniform' => 1,'limit'  => 20]);

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
     * Filter resources
     * @return void
     */
    private function filter(Builder &$query, $filters)
    {
        $query->filter(Arr::except($filters, []));
    }
}
