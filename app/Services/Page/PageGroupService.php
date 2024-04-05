<?php

namespace App\Services\Page;

use App\Http\Resources\PageGroupResource;
use App\Models\PageGroup;
use App\Services\Media\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class PageGroupService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new PageGroup();
    }

    /**
     * Get a single resource from the database
     * @param PageGroup $pageGroup
     * @return PageGroupResource
     */
    public function get(PageGroup $pageGroup)
    {
        return new PageGroupResource($pageGroup);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = PageGroup::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return PageGroupResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return PageGroupResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));

        $record = PageGroup::query()->create($data);
        if (!empty($record)) {
            return new PageGroupResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param PageGroup|Model $pageGroup
     * @param array $data
     * @return PageGroupResource
     */
    public function update(PageGroup $pageGroup, array $data)
    {
        $data = $this->clean($data);

        $pageGroup->name = Arr::get($data, 'name', $pageGroup->name);
        $pageGroup->column = Arr::get($data, 'column', $pageGroup->column);

        $pageGroup->save();
        $pageGroup->refresh();
        return new PageGroupResource($pageGroup);
    }

    /**
     * Deletes resource in the database
     * @param PageGroup|Model $pageGroup
     * @return bool
     */
    public function delete(PageGroup $pageGroup)
    {
        return $pageGroup->delete();
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
