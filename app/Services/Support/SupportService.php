<?php

namespace App\Services\Support;

use App\Http\Resources\SupportResource;
use App\Models\Support as SupportModel;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SupportService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new SupportModel();
    }

    /**
     * Get a single resource from the database
     * @param SupportModel $support
     * @return SupportResource
     */
    public function get(SupportModel $support)
    {
        return new SupportResource($support);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = SupportModel::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return SupportResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return SupportResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        if (!empty($data['file'])) {
            $data['image'] = HasImage::addImage($data['file'], SupportModel::path);
        }

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));

        $record = SupportModel::query()->create($data);
        if (!empty($record)) {
            return new SupportResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param SupportModel|Model $support
     * @param array $data
     * @return SupportResource
     */
    public function update(SupportModel $support, array $data)
    {
        $data = $this->clean($data);

        $support->name = Arr::get($data, 'name', $support->name);
        $support->phone = Arr::get($data, 'phone', $support->phone);
        $support->zalo = Arr::get($data, 'zalo', $support->zalo);
        $support->telegram = Arr::get($data, 'telegram', $support->telegram);
        $support->description = Arr::get($data, 'description', $support->description);
        if (!empty($data['file'])) {
            $support->image = HasImage::updateImage($data['file'], $support->image, SupportModel::path);
        }

        $support->save();
        return new SupportResource($support);
    }

    /**
     * Deletes resource in the database
     * @param SupportModel|Model $support
     * @return bool
     */
    public function delete(SupportModel $support)
    {
        HasImage::deleteImage($support->image);
        return $support->delete();
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
