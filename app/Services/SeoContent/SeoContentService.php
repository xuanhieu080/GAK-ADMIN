<?php

namespace App\Services\SeoContent;

use App\Http\Resources\SeoContentResource;
use App\Models\SeoContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SeoContentService
{
    public $model;

    /**
     * The service instance
     * @var SeoContent
     */
    public function __construct()
    {
        $this->model = new SeoContent();
    }

    /**
     * Get a single resource from the database
     * @param SeoContent $seoContent
     * @return SeoContentResource
     */
    public function get(SeoContent $seoContent)
    {
        return new SeoContentResource($seoContent);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = SeoContent::query();
        if (!empty($data['search'])) {
            $query = $query->where('name','like', '%'.$data['search'].'%');
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return SeoContentResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return SeoContentResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));
        $data['is_active'] = filter_var(Arr::get($data, 'is_active'), FILTER_VALIDATE_BOOLEAN);
        $data['user_id'] = \Auth::id();

        $record = SeoContent::query()->create($data);

        if (!empty($record)) {
            return new SeoContentResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param SeoContent|Model $seoContent
     * @param array $data
     * @return SeoContentResource
     */
    public function updateItem(SeoContent $seoContent, array $data)
    {
        $data = $this->clean($data);
        $seoContent->link = Arr::get($data, 'link', $seoContent->link);
        $seoContent->description = Arr::get($data, 'content', $seoContent->description);
        $seoContent->is_active = filter_var(Arr::get($data, 'is_active', $seoContent->is_active), FILTER_VALIDATE_BOOLEAN);
        $seoContent->user_id = \Auth::id();
        $seoContent->save();
        return new SeoContentResource($seoContent);
    }

    /**
     * Deletes resource in the database
     * @param SeoContent|Model $seoContent
     * @return bool
     */
    public function delete(SeoContent $seoContent)
    {
        return $seoContent->delete();
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
