<?php

namespace App\Services\Product;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CategoryService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Category();
    }

    /**
     * Get a single resource from the database
     * @param Category $category
     * @return CategoryResource
     */
    public function get(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Category::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return CategoryResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return CategoryResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $data['code'] = Support::genCode('categories', 'code');
        $data['image'] = HasImage::addImage($data['file'], Category::path);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));

        $record = Category::query()->create($data);
        if (!empty($record)) {
            return new CategoryResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Category|Model $category
     * @param array $data
     * @return CategoryResource
     */
    public function update(Category $category, array $data)
    {
        $data = $this->clean($data);

        $category->name = Arr::get($data, 'name', $category->name);
        $category->description = Arr::get($data, 'description', $category->description);
        if (!empty($data['file'])) {
            $category->image = HasImage::updateImage($data['file'], $category->image, Category::path);
        }

        $category->save();
        return new CategoryResource($category);
    }

    /**
     * Deletes resource in the database
     * @param Category|Model $category
     * @return bool
     */
    public function delete(Category $category)
    {
        HasImage::deleteImage($category->image);
        return $category->delete();
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
