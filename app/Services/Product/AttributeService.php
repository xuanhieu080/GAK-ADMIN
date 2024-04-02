<?php

namespace App\Services\Product;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Services\Media\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AttributeService
{
    public $model;

    /**
     * The service instance
     * @var Attribute
     */
    public function __construct()
    {
        $this->model = new Attribute();
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
        $data['is_color'] = filter_var(Arr::get($data, 'is_color'), FILTER_VALIDATE_BOOLEAN);

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
        $attribute->group_id = Arr::get($data, 'group_id', $attribute->group_id);
        $attribute->color = Arr::get($data, 'color', $attribute->color);
        $attribute->link = Arr::get($data, 'link', $attribute->link);
        $attribute->is_color = filter_var(Arr::get($data, 'is_color', $attribute->is_color), FILTER_VALIDATE_BOOLEAN);
        

        $attribute->save();
        return new AttributeResource($attribute);
    }

    /**
     * Deletes resource in the database
     * @param Attribute|Model $attribute
     * @return bool
     */
    public function delete(Attribute $attribute)
    {
        return $attribute->delete();
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
