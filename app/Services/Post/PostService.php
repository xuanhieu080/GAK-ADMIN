<?php

namespace App\Services\Post;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PostService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Post();
    }

    /**
     * Get a single resource from the database
     * @param Post $blog
     * @return PostResource
     */
    public function get(Post $blog)
    {
        return new PostResource($blog);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Post::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return PostResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return PostResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $data['slug'] = Str::slug($data['title'], '-');
        if (!empty($data['file'])) {
            $data['image'] = HasImage::addImage($data['file'], Post::path);
        }

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));
        $data['author_id'] = \Auth::id();
        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

        $record = Post::query()->create($data);
        if (!empty($record)) {
            return new PostResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Post|Model $blog
     * @param array $data
     * @return PostResource
     */
    public function update(Post $blog, array $data)
    {
        $data = $this->clean($data);

        $title = Arr::get($data, 'title', $blog->title);
        $blog->title = $title;
        $blog->slug = Str::slug($title, '-');
        $blog->content = Arr::get($data, 'content', $blog->content);
        $blog->is_active = filter_var(Arr::get($data, 'is_active', $blog->is_active), FILTER_VALIDATE_BOOLEAN);

        if (!empty($data['file'])) {
            $blog->image = HasImage::updateImage($data['file'], $blog->image, Post::path);
        }
        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        $blog->save();
        return new PostResource($blog);
    }

    /**
     * Deletes resource in the database
     * @param Post|Model $blog
     * @return bool
     */
    public function delete(Post $blog)
    {
        HasImage::deleteImage($blog->image);
        return $blog->delete();
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
