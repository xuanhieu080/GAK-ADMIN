<?php

namespace App\Services\Post;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CommentService
{
    public $model;

    /**
     * The service instance
     */
    public function __construct()
    {
        $this->model = new Comment();
    }

    /**
     * Get a single resource from the database
     * @param Comment $comment
     * @return CommentResource
     */
    public function get(Comment $comment)
    {
        return new CommentResource($comment);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Comment::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return CommentResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return CommentResource
     */
    public function create(array $data)
    {
//        $data = $this->clean($data);
//
//        $data['slug'] = Str::slug($data['title'], '-');
//
//        $full_columns = $this->model->getFillable();
//        $data = array_intersect_key($data, array_flip($full_columns));
//        $data['author_id'] = \Auth::id();
//        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
//
//        $record = Comment::query()->create($data);
//        if (!empty($record)) {
//            return new CommentResource($record);
//        } else {
//            return null;
//        }
    }

    /**
     * Updates resource in the database
     * @param Comment|Model $comment
     * @param array $data
     * @return CommentResource
     */
    public function update(Comment $comment, array $data)
    {
//        $data = $this->clean($data);
//
//        $title = Arr::get($data, 'title', $comment->title);
//        $comment->title = $title;
//        $comment->slug = Str::slug($title, '-');
//        $comment->content = Arr::get($data, 'content', $comment->content);
//        $comment->is_active = filter_var(Arr::get($data, 'is_active', $comment->is_active), FILTER_VALIDATE_BOOLEAN);
//
//        if (!empty($data['file'])) {
//            $comment->image = HasImage::updateImage($data['file'], $comment->image, Comment::path);
//        }
//        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
//        $comment->save();
//        return new CommentResource($comment);
    }

    /**
     * Deletes resource in the database
     * @param Comment|Model $comment
     * @return bool
     */
    public function delete(Comment $comment)
    {
        return $comment->delete();
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
