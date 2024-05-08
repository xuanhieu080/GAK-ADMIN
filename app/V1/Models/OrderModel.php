<?php

namespace App\V1\Models;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Supports\GAK_ERROR;
use App\Supports\Support;
use App\V1\Resources\OrderResource;
use App\V1\Resources\ProductStockResource;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Order();
        parent::__construct($model);
    }

    public function index($input)
    {
        $limit = Arr::get($input, 'limit', 999);
        $sort = Arr::get($input, 'sort', ['desc' => ['id']]);
        $sorts = ['id' => 'desc'];
        if (!empty($sort['desc']) && is_array($sort['desc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['desc'], 'desc'));
        } elseif (!empty($sort['asc']) && is_array($sort['asc'])) {
            $sorts = array_merge($sorts, array_fill_keys($sort['asc'], 'asc'));
        }

        $input['sort'] = $sorts;
        $result = $this->search($input, [], $limit);

        return OrderResource::collection($result);
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

    public function show($code)
    {
        $item = Order::where('code', $code)
            ->first();
        if (empty($item)) {
            return null;
        }

        return new OrderResource($item);
    }

    public function checkStock($input)
    {
        $nonNullDetails = array_filter($input['items'], function ($condition) {
            return !is_null($condition['product_variant_id']);
        });

        $nullProductIds = array_reduce($input['items'], function ($carry, $condition) {
            if (is_null($condition['product_variant_id'])) {
                $carry[] = $condition['product_id'];
            }
            return $carry;
        }, []);

        $products = Product::with(['variants' => function ($query) use ($nonNullDetails) {
            $query->where(function ($query) use ($nonNullDetails) {
                foreach ($nonNullDetails as $conditions) {
                    $query->orWhere(function ($query) use ($conditions) {
                        $query->where('product_id', $conditions['product_id'])
                            ->where('id', $conditions['product_variant_id']);
                    });
                }
            })->orderBy('name');
        }])->where(function ($query) use ($nonNullDetails, $nullProductIds) {
            foreach ($nonNullDetails as $conditions) {
                $query->orWhere(function ($query) use ($conditions) {
                    $query->where('id', $conditions['product_id'])
                        ->whereHas('variants', function ($query) use ($conditions) {
                            $query->where('id', $conditions['product_variant_id']);
                        });
                });
            }
            if (!empty($nullProductIds)) {
                $query->orWhereIn('id', $nullProductIds);
            }
        })->whereHas('media', function ($query) {
            // Điều kiện cho hình ảnh
            $query->where('collection_name', 'default');
        })->whereHas('media', function ($query) {
            // Điều kiện cho hình thu nhỏ
            $query->where('collection_name', 'thumb');
        })->orderBy('name')->get();

        return ProductStockResource::collection($products);
    }

    public function store($input)
    {
            try {
                DB::transaction(function () use ($input) {
                    $params = [
                        "code" => Support::genCode('orders',16),
                        "date" => Carbon::now(),
//                        "user_id" => \Auth::id(),
//                        "user_name" => \Auth::user()->name,
                        "customer_name" => $input['customer_name'],
                        "customer_email" => $input['customer_email'],
                        "customer_phone" => $input['customer_phone'],
                        "address" => $input['address'],
                        "province_id" => $input['province_id'],
                        "district_id" => $input['district_id'],
                        "ward_id" => $input['ward_id'],
                        "note" => Arr::get($input, 'note'),
                    ];


                    $order = Order::create($params);
                    $items = $input['items'];
                    $data = [];
                    $total = 0;
                    $product = null;
                    foreach ($items as $index => $detail) {
                        $productId = $detail['product_id'];
                        if (!empty($detail['product_variant_id'])) {
                            $productVariantId = $detail['product_variant_id'];

                            $item = ProductVariant::with(['product'])
                                ->whereHas('product')
                                ->where('id', $productVariantId)->where('product_id', $productId)->lockForUpdate()->first();
                            if (empty($item)) {
                                throw new \Exception('Sản phẩm không tồn tại');
                            }
                            if ($item->qty < $detail['qty']) {
                                throw new \Exception('Số lượng sản phẩm trong kho không đủ');
                            }

                            $product = $item->product;
                            $data[$index]['product_variant_id'] = $item->id;
                            $data[$index]['product_variant_code'] = $item->code;
                            $data[$index]['product_variant_name'] = $item->name;
                            $data[$index]['option_name'] = $item->option_name;
                        }
                        if (empty($detail['product_variant_id'])) {
                            $item = Product::where('id', $productId)->whereDoesntHave('variants')->lockForUpdate()->first();
                            if (empty($item)) {
                                throw new \Exception('Sản phẩm không tồn tại');
                            }
                            if ($item->qty < $detail['qty']) {
                                throw new \Exception('Số lượng sản phẩm trong kho không đủ');
                            }
                            $product = $item;
                        }
                        $totalDetail = $product->price_discount * $detail['qty'];
                        $data[$index]['product_id'] = $productId;
                        $data[$index]['product_name'] = $product->name;
                        $data[$index]['product_code'] = $product->code;
                        $data[$index]['price'] = $product->price_discount;
                        $data[$index]['cost'] = $product->price;
                        $data[$index]['total'] = $totalDetail;
                        $data[$index]['order_id'] = $order->id;
                        $data[$index]['qty'] = $detail['qty'];
                        $item->decrement('qty', $detail['qty']);
                        $total += $totalDetail;
                    }

                    OrderDetail::insert($data);
                    $order->total = $total;
                    $order->save();
                    return $order;
                });
            } catch (\Exception $e) {
                GAK_ERROR::handle($e,'orders');
                dd($e);
            }
    }
}
