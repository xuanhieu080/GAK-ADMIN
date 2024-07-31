<?php

namespace App\Services\Product;

use App\Http\Resources\AttributeGroupResource;
use App\Models\AttributeGroup;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use App\V1\Models\CategoryModel;
use App\V1\Models\ProductModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AttributeGroupService
{
    public $model, $categoryModel, $productModel;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new AttributeGroup();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get a single resource from the database
     * @param AttributeGroup $attributeGroup
     * @return AttributeGroupResource
     */
    public function get(AttributeGroup $attributeGroup)
    {
        return new AttributeGroupResource($attributeGroup);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = AttributeGroup::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return AttributeGroupResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return AttributeGroupResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));
        $data['is_color'] = filter_var(Arr::get($data, 'is_color'), FILTER_VALIDATE_BOOLEAN);
        $data['is_main'] = filter_var(Arr::get($data, 'is_main'), FILTER_VALIDATE_BOOLEAN);

        $record = AttributeGroup::query()->create($data);
        if (!empty($record)) {
            return new AttributeGroupResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param AttributeGroup|Model $attributeGroup
     * @param array $data
     * @return AttributeGroupResource
     */
    public function update(AttributeGroup $attributeGroup, array $data)
    {
        $data = $this->clean($data);

        $attributeGroup->name = Arr::get($data, 'name', $attributeGroup->name);
        $attributeGroup->priority = Arr::get($data, 'priority', $attributeGroup->priority);
        $attributeGroup->link = Arr::get($data, 'link', $attributeGroup->link);
        $attributeGroup->is_color = filter_var(Arr::get($data, 'is_color',$attributeGroup->is_color), FILTER_VALIDATE_BOOLEAN);
        $attributeGroup->is_main = filter_var(Arr::get($data, 'is_main',$attributeGroup->is_main), FILTER_VALIDATE_BOOLEAN);

        $attributeGroup->save();
        $attributeGroup->refresh();
        $this->categoryModel->cacheCategoryHeader([]);
        $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
        $this->productModel->cacheProductAll();
        $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
        $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
        $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
        $this->productModel->cacheProductUniform(['category_name' => 'dong phuc', 'limit' => 4]);
        return new AttributeGroupResource($attributeGroup);
    }

    /**
     * Deletes resource in the database
     * @param AttributeGroup|Model $attributeGroup
     * @return bool
     */
    public function delete(AttributeGroup $attributeGroup)
    {
        $bool = $attributeGroup->delete();
        $this->categoryModel->cacheCategoryHeader([]);
        $this->productModel->cacheProductHot(['is_hot' => 1, 'limit' => 20]);
        $this->productModel->cacheProductAll();
        $this->productModel->cacheProductGhiLe(['category_slug' => 'ao-ghi-le', 'limit' => 4]);
        $this->productModel->cacheProductNew(['is_new' => 1, 'limit' => 20]);
        $this->productModel->cacheProductUpcoming(['is_upcoming' => 1, 'limit' => 4]);
        $this->productModel->cacheProductUniform(['category_name' => 'dong phuc', 'limit' => 4]);
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
