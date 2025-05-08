<?php

namespace App\Services\Page;

use App\Http\Resources\PageResource;
use App\Models\Page;
use App\V1\Models\PageGroupModel;
use App\V1\Models\PageModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class PageService
{
    public $model, $pageGroupModel, $pageModel;
    /**
     * The service instance
     * @var Page
     */
    public function __construct()
    {
        $this->model = new Page();
        $this->pageGroupModel = new PageGroupModel();
        $this->pageModel = new PageModel();
    }

    /**
     * Get a single resource from the database
     * @param Page $page
     * @return PageResource
     */
    public function get(Page $page)
    {
        return new PageResource($page);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Page::query();
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

        return PageResource::collection($query->paginate($per_page));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return PageResource
     */
    public function create(array $data)
    {
        $data = $this->clean($data);

        $full_columns = $this->model->getFillable();
        $data = array_intersect_key($data, array_flip($full_columns));
        $data['is_active'] = filter_var(Arr::get($data, 'is_active'), FILTER_VALIDATE_BOOLEAN);
        $data['show_header'] = filter_var(Arr::get($data, 'show_header'), FILTER_VALIDATE_BOOLEAN);
        $data['is_button'] = filter_var(Arr::get($data, 'is_button'), FILTER_VALIDATE_BOOLEAN);

        $record = Page::query()->create($data);
        if (!empty($data['image'])) {
            $record->addMedia($data['image'])
                ->usingName($record->name)
                ->usingFileName($record->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                ->toMediaCollection();
        }
        $this->pageGroupModel->cacheIndex();
        $this->pageModel->cachePageHeader([]);
        if (!empty($record)) {
            return new PageResource($record);
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Page|Model $page
     * @param array $data
     * @return PageResource
     */
    public function updateItem(Page $page, array $data)
    {
        $data = $this->clean($data);

        $name = Arr::get($data, 'name', $page->name);
        $page->name = $name;
        $page->name_en = Arr::get($data, 'name_en', $page->name_en);;
        $page->title = Arr::get($data, 'title', $page->title);
        $page->title_en = Arr::get($data, 'title_en', $page->title_en);
        $page->group_id = Arr::get($data, 'group_id', $page->group_id);
        $page->user_id = Arr::get($data, 'user_id', $page->user_id);
        $page->description = Arr::get($data, 'description', $page->description);
        $page->description_en = Arr::get($data, 'description_en', $page->description_en);
        $page->description_short = Arr::get($data, 'description_short', $page->description_short);
        $page->description_short_en = Arr::get($data, 'description_short_en', $page->description_short_en);
        $page->slug = Arr::get($data, 'slug', $page->slug);
        $page->slug_en = Arr::get($data, 'slug_en', $page->slug_en);
        $page->meta_title = Arr::get($data, 'meta_title', $page->meta_title);
        $page->meta_title_en = Arr::get($data, 'meta_title_en', $page->meta_title_en);
        $page->meta_key = Arr::get($data, 'meta_key', $page->meta_key);
        $page->meta_key_en = Arr::get($data, 'meta_key_en', $page->meta_key_en);
        $page->meta_description = Arr::get($data, 'meta_description', $page->meta_description);
        $page->meta_description_en = Arr::get($data, 'meta_description_en', $page->meta_description_en);
        $page->link = Arr::get($data, 'link', $page->link);
        $page->link_en = Arr::get($data, 'link_en', $page->link_en);
        $page->is_active = filter_var(Arr::get($data, 'is_active', $page->is_active), FILTER_VALIDATE_BOOLEAN);
        $page->is_button = filter_var(Arr::get($data, 'is_button', $page->is_button), FILTER_VALIDATE_BOOLEAN);
        $page->show_header = filter_var(Arr::get($data, 'show_header', $page->show_header), FILTER_VALIDATE_BOOLEAN);

        if (!empty($data['image'])) {
            $page->clearMediaCollection();
            $page->addMedia($data['image'])
                ->usingName($name)
                ->usingFileName($page->slug . '-' . time() . '.' . $data['image']->getClientOriginalExtension())
                ->toMediaCollection();
        }
        $page->save();
        $this->pageGroupModel->cacheIndex();
        $this->pageModel->cachePageHeader([]);
        return new PageResource($page);
    }

    /**
     * Deletes resource in the database
     * @param Page|Model $page
     * @return bool
     */
    public function delete(Page $page)
    {
        $bool = $page->delete();
        $this->pageGroupModel->cacheIndex();
        $this->pageModel->cachePageHeader([]);
        return $bool;
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
