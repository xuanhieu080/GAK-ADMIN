<?php

namespace App\Services\Post;

use App\Http\Resources\PostGroupResource;
use App\Models\PostGroup;
use App\Services\Media\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostGroupService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new PostGroup();
    }

    /**
     * Get a single resource from the database
     * @param PostGroup $postGroup
     * @return PostGroupResource
     */
    public function get(PostGroup $postGroup)
    {
        return new PostGroupResource($postGroup);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = PostGroup::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }

        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return PostGroupResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return PostGroupResource
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);

            $full_columns = $this->model->getFillable();
            $data = array_intersect_key($data, array_flip($full_columns));
            $data['user_id'] = Auth::id();
            $data['is_active'] = filter_var(Arr::get($data, 'is_active'), FILTER_VALIDATE_BOOLEAN);
            $data['is_hot'] = filter_var(Arr::get($data, 'is_hot'), FILTER_VALIDATE_BOOLEAN);
            $record = PostGroup::query()->create($data);
            $record->addMedia($data['image'])
                ->usingName($record->name)
                ->usingFileName($record->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                ->toMediaCollection();
            DB::commit();
            if (!empty($record)) {
                return new PostGroupResource($record);
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
     * @param PostGroup|Model $postGroup
     * @param array $data
     * @return PostGroupResource
     */
    public function update(PostGroup $postGroup, array $data)
    {
        try {
            DB::beginTransaction();
            $data = $this->clean($data);
            $name = Arr::get($data, 'name', $postGroup->name);

            $postGroup->name = $name;
            $postGroup->name_en = Arr::get($data, 'name_en', $postGroup->name_en);
            $postGroup->description = Arr::get($data, 'description', $postGroup->description);
            $postGroup->description_en = Arr::get($data, 'description_en', $postGroup->description_en);
            $postGroup->slug = Arr::get($data, 'slug', $postGroup->slug);
            $postGroup->slug_en = Arr::get($data, 'slug_en', $postGroup->slug_en);
            $postGroup->meta_title = Arr::get($data, 'meta_title', $postGroup->meta_title);
            $postGroup->meta_title_en = Arr::get($data, 'meta_title_en', $postGroup->meta_title_en);
            $postGroup->meta_key = Arr::get($data, 'meta_key', $postGroup->meta_key);
            $postGroup->meta_key_en = Arr::get($data, 'meta_key_en', $postGroup->meta_key_en);
            $postGroup->meta_description = Arr::get($data, 'meta_description', $postGroup->meta_description);
            $postGroup->meta_description_en = Arr::get($data, 'meta_description_en', $postGroup->meta_description_en);
            $postGroup->is_active = filter_var(Arr::get($data, 'is_active', $postGroup->is_active), FILTER_VALIDATE_BOOLEAN);
            $postGroup->is_hot = filter_var(Arr::get($data, 'is_hot', $postGroup->is_hot), FILTER_VALIDATE_BOOLEAN);
            if (!empty($data['image'])) {
                $postGroup->clearMediaCollection();
                $postGroup->addMedia($data['image'])
                    ->usingName($name)
                    ->usingFileName($postGroup->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                    ->toMediaCollection();
            }

            $postGroup->save();
            $postGroup->refresh();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }

        return new PostGroupResource($postGroup);
    }

    /**
     * Deletes resource in the database
     * @param PostGroup|Model $postGroup
     * @return bool
     */
    public function delete(PostGroup $postGroup)
    {
        return $postGroup->delete();
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
