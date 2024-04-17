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
     * @param Post $post
     * @return PostResource
     */
    public function get(Post $post)
    {
        return new PostResource($post);
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

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));
        $data['author_id'] = \Auth::id();
        $data['is_active'] = filter_var(Arr::get($data, 'is_active'), FILTER_VALIDATE_BOOLEAN);
        $data['is_hot'] = filter_var(Arr::get($data, 'is_hot'), FILTER_VALIDATE_BOOLEAN);
        $data['is_new'] = filter_var(Arr::get($data, 'is_new'), FILTER_VALIDATE_BOOLEAN);
        $record = Post::query()->create($data);

        if (!empty($data['image'])) {
            $image = $data['image'];
            $record->addMedia($image)
                ->usingName($record->title)
                ->usingFileName($record->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                ->toMediaCollection();
        }
        if (!empty($record)) {
            return new PostResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Post|Model $post
     * @param array $data
     * @return PostResource
     */
    public function updateItem(Post $post, array $data)
    {
        $data = $this->clean($data);

        $post->meta_title = Arr::get($data, 'meta_title', $post->meta_title);
        $post->meta_key = Arr::get($data, 'meta_key', $post->meta_key);
        $post->meta_description = Arr::get($data, 'meta_description', $post->meta_description);
        $post->group_id = Arr::get($data, 'group_id', $post->group_id);;
        $post->title = Arr::get($data, 'title', $post->title);
        $post->slug = Arr::get($data, 'slug', $post->slug);
        $post->content = Arr::get($data, 'content', $post->content);
        $post->is_active = filter_var(Arr::get($data, 'is_active', $post->is_active), FILTER_VALIDATE_BOOLEAN);
        $post->is_hot = filter_var(Arr::get($data, 'is_hot', $post->is_hot), FILTER_VALIDATE_BOOLEAN);
        $post->is_new = filter_var(Arr::get($data, 'is_new', $post->is_new), FILTER_VALIDATE_BOOLEAN);
        $post->view = Arr::get($data, 'view', $post->view);

        $post->save();
        if (!empty($data['image'])) {
            $image = $data['image'];
            $media = $post->getMedia('default')->first();
            if (!empty($media)) {
                $media->delete();
            }
            $post->addMedia($image)
                ->usingName($post->title)
                ->usingFileName($post->slug . '-' . time() . Str::random(8) . '.' . $image->getClientOriginalExtension())
                ->toMediaCollection();
        }
        $post->refresh();
        return new PostResource($post);
    }

    /**
     * Deletes resource in the database
     * @param Post|Model $post
     * @return bool
     */
    public function delete(Post $post)
    {
        HasImage::deleteImage($post->image);
        return $post->delete();
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
