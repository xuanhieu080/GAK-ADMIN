<?php

namespace App\V1\Models;

use App\Models\Post;
use App\V1\Resources\Vi\PostResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Post();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $input['is_active'] = 1;
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        if (isset($input['is_hot'])) {
            $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($input['is_new'])) {
            $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($input['not_id'])) {
            $input['id'] = ['<>' => $input['not_id']];
        }

        $input['sort'] = $sorts;
        $result = $this->search($input, [], $limit);

        return PostResource::collection($result);
    }

    public function hot($input)
    {
        $page = Arr::get($input, 'page', 1);
        $input['is_active'] = 1;
        if ($page == 1) {
            $cacheKey = 'post_hot_data';
            $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

            // Kiểm tra xem dữ liệu có trong cache không
            $posts = Cache::remember($cacheKey, $seconds, function () use ($input) {
                $limit = Arr::get($input, 'limit', 10);
                $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
                $sorts = ['id' => 'desc'];
                if (!empty($sort['desc']) && is_array($sort['desc'])) {
                    $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
                } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                    $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
                }

                if (isset($input['is_hot'])) {
                    $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($input['is_new'])) {
                    $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
                }

                $input['sort'] = $sorts;
                $result = $this->search($input, [], $limit);

                return PostResource::collection($result);
            });

            return $posts;
        } else {
            $limit = Arr::get($input, 'limit', 10);
            $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
            $sorts = ['id' => 'desc'];
            if (!empty($sort['desc']) && is_array($sort['desc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
            } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
            }

            if (isset($input['is_hot'])) {
                $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($input['is_new'])) {
                $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
            }

            $input['sort'] = $sorts;
            $result = $this->search($input, [], $limit);

            return PostResource::collection($result);
        }
    }

    public function cachePostHot($input)
    {
        $cacheKey = 'post_hot_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 10);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        if (isset($input['is_hot'])) {
            $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($input['is_new'])) {
            $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
        }

        $input['sort'] = $sorts;
        $result = $this->search($input, [], $limit);

        Cache::put($cacheKey, PostResource::collection($result), $seconds);
    }

    public function new($input)
    {
        $page = Arr::get($input, 'page', 1);
        $input['is_active'] = 1;
        if ($page == 1) {
            $cacheKey = 'post_new_data';
            $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm

            // Kiểm tra xem dữ liệu có trong cache không
            $posts = Cache::remember($cacheKey, $seconds, function () use ($input) {
                $limit = Arr::get($input, 'limit', 10);
                $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
                $sorts = ['id' => 'desc'];
                if (!empty($sort['desc']) && is_array($sort['desc'])) {
                    $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
                } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                    $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
                }

                if (isset($input['is_hot'])) {
                    $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
                }

                if (isset($input['is_new'])) {
                    $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
                }

                $input['sort'] = $sorts;
                $result = $this->search($input, [], $limit);

                return PostResource::collection($result);
            });

            return $posts;
        } else {
            $limit = Arr::get($input, 'limit', 10);
            $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
            $sorts = ['id' => 'desc'];
            if (!empty($sort['desc']) && is_array($sort['desc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
            } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
                $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
            }

            if (isset($input['is_hot'])) {
                $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($input['is_new'])) {
                $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
            }

            $input['sort'] = $sorts;
            $result = $this->search($input, [], $limit);

            return PostResource::collection($result);
        }
    }

    public function cachePostNew($input)
    {
        $cacheKey = 'post_new_data';
        $seconds = 365 * 24 * 60 * 60; // 31.536.000 giây cho 1 năm
        $limit = Arr::get($input, 'limit', 10);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $sorts = ['id' => 'desc'];

        $input['is_active'] = 1;

        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        if (isset($input['is_hot'])) {
            $input['is_hot'] = filter_var($input['is_hot'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($input['is_new'])) {
            $input['is_new'] = filter_var($input['is_new'], FILTER_VALIDATE_BOOLEAN);
        }

        $input['sort'] = $sorts;
        $result = $this->search($input, [], $limit);

        Cache::put($cacheKey, PostResource::collection($result), $seconds);
    }

    public function search($input = [], $with = [], $limit = null)
    {
        $groupSlug = Arr::get($input, 'group_slug');
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

        if (!empty($groupSlug)) {
            $query->whereHas('group', function ($q) use ($groupSlug) {
                $q->where('slug', $groupSlug);
            });
        }

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

    public function show($slug)
    {
        $item = Post::with(['group'])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
        if (empty($item)) {
            return null;
        }

        return new PostResource($item);
    }
}
