<?php

namespace App\Services\Product;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        Category::fixTree();
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Category::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['category_id'])) {
            $query = $query->whereNotDescendantOf($data['category_id'])
                ->where('id', '<>', $data['category_id']);
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
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $data['code'] = Support::genCode('categories', 'code');
//            $data['image'] = HasImage::addImage($data['image'], Category::path);

            $full_columns = $this->model->getFillable();
            $data = array_intersect_key($data, array_flip($full_columns));
            $data['show_header'] = filter_var($data['show_header'], FILTER_VALIDATE_BOOLEAN);
            $record = Category::query()->create($data);
            $record->addMedia($data['image'])
                ->usingName($record->name)
                ->usingFileName($record->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                ->toMediaCollection();
            DB::commit();
            if (!empty($record)) {
                return new CategoryResource($record);
            } else {
                return null;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
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
        try {
            DB::beginTransaction();
            $data = $this->clean($data);
            $name = Arr::get($data, 'name', $category->name);

            $category->name = $name;
            $category->description = Arr::get($data, 'description', $category->description);
            $category->slug = Arr::get($data, 'slug', $category->slug);
            $category->meta_title = Arr::get($data, 'meta_title', $category->meta_title);
            $category->meta_key = Arr::get($data, 'meta_key', $category->meta_key);
            $category->meta_description = Arr::get($data, 'meta_description', $category->meta_description);

            if (!empty($data['image'])) {
                $category->clearMediaCollection();
                $category->addMedia($data['image'])
                    ->usingName($name)
                    ->usingFileName($category->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                    ->toMediaCollection();
            }
            if (!empty($data['parent_id'])) {
                $node = Category::find($data['parent_id']);
                $bool = $node->isDescendantOf($category);
                if ($bool) {
                    throw new \Exception("Không thể chọn danh mục con làm danh mục cha");
                }
                $category->parent_id = $data['parent_id'];
            }

            $category->show_header = filter_var($data['show_header'], FILTER_VALIDATE_BOOLEAN);
            $category->show_dashboard = filter_var($data['show_dashboard'], FILTER_VALIDATE_BOOLEAN);
            $category->save();
            $category->refresh();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }

        return new CategoryResource($category);
    }

    /**
     * Deletes resource in the database
     * @param Category|Model $category
     * @return bool
     */
    public function delete(Category $category)
    {
        Product::query()
            ->where('category_id', $category->id)
            ->update(['category_id' => null]);
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
