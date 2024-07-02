<?php

namespace App\V1\Models;

use App\Models\PageGroup;
use App\V1\Resources\PageGroupResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PageGroupModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new PageGroup();
        parent::__construct($model);
    }

    public function index($input)
    {
        $cacheKey = 'page_group_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        // Kiểm tra xem dữ liệu có trong cache không
        $pages = Cache::remember($cacheKey, $seconds, function () use ($input) {
            $pages = PageGroup::with(['details' => function ($query) {
                $query->where('pages.is_active', 1);
            }])->whereHas('details', function ($query) {
                $query->selectRaw('name,title,slug')
                    ->where('pages.is_active', 1);
            })->where('page_groups.is_active', 1)
                ->get()
                ->groupBy('column');

            return PageGroupResource::collection($pages);
        });

        return $pages;
    }


    public function cacheIndex()
    {
        $cacheKey = 'page_group_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

        $pages = PageGroup::with(['details' => function ($query) {
            $query->where('pages.is_active', 1);
        }])->whereHas('details', function ($query) {
            $query->selectRaw('name,title,slug')
                ->where('pages.is_active', 1);
        })->where('page_groups.is_active', 1)
            ->get()
            ->groupBy('column');

        Cache::put($cacheKey, PageGroupResource::collection($pages), $seconds);
    }

    public function search($input = [], $with = [], $limit = null)
    {
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
