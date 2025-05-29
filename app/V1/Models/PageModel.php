<?php

namespace App\V1\Models;

use App\Models\Page;
use App\Supports\Support;
use App\V1\Resources\En\PageResourceEn;
use App\V1\Resources\Vi\PageResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PageModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Page();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);

        $input['sort'] = ['id' => 'desc'];
        $result = $this->search($input, [], $limit);

        if (!empty($input['lang']) && $input['lang'] == 'en') {
            return PageResourceEn::collection($result);
        }
        return PageResource::collection($result);
    }

    public function show(Page $item, $input = [])
    {
        if (!$item->is_active) {
            return null;
        }

        if (!empty($input['lang']) && $input['lang'] == 'en') {
            return new PageResourceEn($item);
        }
        return new PageResource($item);
    }

    public function getItem($slug, $input = [])
    {
        $attributeColumn = 'slug';
        if (!empty($input['lang']) && $input['lang'] == 'en') {
            $attributeColumn = 'slug_en';
        }
        $item = Page::query()
            ->where($attributeColumn, $slug)
            ->where('is_active', 1)
            ->first();

        if (empty($item)) {
            return null;
        }
        if (!empty($input['lang']) && $input['lang'] == 'en') {
            return new PageResourceEn($item);
        }
        return new PageResource($item);
    }


    public function getPageHeader($input)
    {
        $page = Arr::get($input, 'page', 1);
        if ($page == 1) {
            $cacheKey = 'page_header_data';
            $cacheKeyEn = 'page_header_data';
            $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

            if (!empty($input['lang']) && $input['lang'] == 'en') {
                // Kiểm tra xem dữ liệu có trong cache không
                $pages = Cache::remember($cacheKeyEn, $seconds, function () use ($input) {
                    $limit = Arr::get($input, 'limit', 999);

                    $input['sort'] = ['id' => 'desc'];
                    $input['is_active'] = 1;
                    $input['show_header'] = 1;
                    $result = $this->search($input, [], $limit);

                    return PageResourceEn::collection($result);
                });
            } else {
                // Kiểm tra xem dữ liệu có trong cache không
                $pages = Cache::remember($cacheKey, $seconds, function () use ($input) {
                    $limit = Arr::get($input, 'limit', 999);

                    $input['sort'] = ['id' => 'desc'];
                    $input['is_active'] = 1;
                    $input['show_header'] = 1;
                    $result = $this->search($input, [], $limit);

                    return PageResource::collection($result);
                });
            }
            return $pages;
        } else {
            $limit = Arr::get($input, 'limit', 999);

            $input['sort'] = ['id' => 'desc'];
            $input['is_active'] = 1;
            $input['show_header'] = 1;
            $result = $this->search($input, [], $limit);

            if (!empty($input['lang']) && $input['lang'] == 'en') {
                return PageResourceEn::collection($result);
            } else {
                return PageResource::collection($result);
            }
        }
    }

    public function cachePageHeader($input)
    {
        $cacheKey = 'page_header_data';
        $cacheKeyEn = 'page_header_data_en';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 999);

        $input['sort'] = ['id' => 'desc'];
        $input['is_active'] = 1;
        $input['show_header'] = 1;
        $result = $this->search($input, [], $limit);

        if (!empty($input['lang']) && $input['lang'] == 'en') {
            Cache::put($cacheKeyEn, PageResourceEn::collection($result), $seconds);
        } else {
            Cache::put($cacheKey, PageResource::collection($result), $seconds);
        }
    }

    public function search($input = [], $with = [], $limit = null)
    {
        $attributeColumn = 'slug';
        if (!empty($input['lang']) && $input['lang'] == 'en') {
            $attributeColumn = 'slug_en';
        }

        $query = $this->make($with);
        $orWhere = Arr::get($input, 'orWhere', []);
        $this->sortBuilder($query, $input);
        $full_columns = $this->model->getFillable();

        $input = array_intersect_key($input, array_flip($full_columns));
        $orWhere = array_intersect_key($orWhere, array_flip($full_columns));

        foreach ($input as $field => $value) {
            if ($value === "") {
                continue;
            }
            if (is_array($value)) {
                $query->where(function ($q) use ($field, $value) {
                    foreach ($value as $action => $data) {
                        $action = strtoupper($action);
                        if ($data === "") {
                            continue;
                        }
                        switch ($action) {
                            case "LIKE":
                                $q->orWhere(DB::raw($field), "like", "%$data%");
                                break;
                            case "IN":
                                $q->orWhereIn(DB::raw($field), $data);
                                break;
                            case "NOT IN":
                                $q->orWhereNotIn(DB::raw($field), $data);
                                break;
                            case "NULL":
                                $q->orWhereNull(DB::raw($field));
                                break;
                            case "NOT NULL":
                                $q->orWhereNotNull(DB::raw($field));
                                break;
                            case "BETWEEN":
                                $q->orWhereBetween(DB::raw($field), $value);
                                break;
                            default:
                                $q->orWhere(DB::raw($field), $action, $data);
                                break;
                        }
                    }
                });
            } else {
                $query->where(DB::raw($field), $value);
            }
        }
        $query->where(function ($qr) use ($orWhere) {
            foreach ($orWhere as $field => $value) {
                if ($value === "") {
                    continue;
                }
                if (is_array($value)) {
                    $qr->orWhere(function ($q) use ($field, $value) {
                        foreach ($value as $action => $data) {
                            $action = strtoupper($action);
                            if ($data === "") {
                                continue;
                            }
                            switch ($action) {
                                case "LIKE":
                                    $q->orWhere(DB::raw($field), "like", "%$data%");
                                    break;
                                case "IN":
                                    $q->orWhereIn(DB::raw($field), $data);
                                    break;
                                case "NOT IN":
                                    $q->orWhereNotIn(DB::raw($field), $data);
                                    break;
                                case "NULL":
                                    $q->orWhereNull(DB::raw($field));
                                    break;
                                case "NOT NULL":
                                    $q->orWhereNotNull(DB::raw($field));
                                    break;
                                case "BETWEEN":
                                    $q->orWhereBetween(DB::raw($field), $value);
                                    break;
                                default:
                                    $q->orWhere(DB::raw($field), $action, $data);
                                    break;
                            }
                        }
                    });
                } else {
                    $qr->orwhere(DB::raw($field), $value);
                }
            }
        });

        $query->whereNotNull($attributeColumn);

        if ($limit) {
            if ($limit === 1) {
                return $query->first();
            } else {
                return $query->paginate($limit);
            }
        } else {
            return $query->get();
        }
    }
}
