<?php

namespace App\Services\Config;

use App\Http\Resources\ConfigResource;
use App\Models\Config;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use \Illuminate\Support\Facades\Config as AppConfig;

class ConfigService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Config();
    }

    /**
     * Get a single resource from the database
     * @param Config $config
     * @return ConfigResource
     */
    public function get(Config $config)
    {
        return new ConfigResource($config);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Config::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return ConfigResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return ConfigResource
     */
    public function create(array $data)
    {
//        $data = $this->clean($data);
//
//        if (!empty($data['file'])) {
//            $data['image'] = HasImage::addImage($data['file'], Config::path);
//        }
//
//        $full_columns = $this->model->getFillable();
//        $data = array_intersect_key($data, array_flip($full_columns));
//
//        $record = Config::query()->create($data);
//        if (!empty($record)) {
//            return new ConfigResource($record);
//        } else {
            return null;
//        }
    }

    /**
     * Updates resource in the database
     * @param Config|Model $config
     * @param array $data
     * @return ConfigResource
     */
    public function update(Config $config, array $data)
    {
        $data = $this->clean($data);

        if ($config->is_file == 1) {
            if (!empty($data['file'])) {
                $image = HasImage::updateImage($data['file'], $config->image, Config::path);
                $config->image = $image;
                $config->description = $image;
            }
        } else {
            $config->value = Arr::get($data, 'value', $config->value);
            $config->description = Arr::get($data, 'value', $config->value);
        }np

        $config->save();

        return new ConfigResource($config);
    }

    /**
     * Deletes resource in the database
     * @param Config|Model $config
     * @return bool
     */
    public function delete(Config $config)
    {
//        HasImage::deleteImage($config->image);
        return true;
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
