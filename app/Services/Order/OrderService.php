<?php

namespace App\Services\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Services\Media\MediaService;
use App\Supports\GAK_ERROR;
use App\Supports\HasImage;
use App\Supports\Support;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public $model;

    /**
     * The service instance
     * @var MediaService
     */
    public function __construct()
    {
        $this->model = new Order();
    }

    /**
     * Get a single resource from the database
     * @param Order $order
     * @return OrderResource
     */
    public function get(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $query = Order::query();
        if (!empty($data['search'])) {
            $query = $query->search($data['search']);
        }
        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        }

        return OrderResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     * @param array $data
     * @return OrderResource
     */


    public function store($input)
    {
        $order = null;
        try {
            DB::transaction(function () use ($input, &$order) {
                $params = [
                    "code"           => Support::genCode('orders', 'code', 16),
                    "date"           => Carbon::now(),
                    "user_id"        => \Auth::id(),
                    "user_name"      => \Auth::user()->name,
                    "customer_name"  => $input['customer_name'],
                    "customer_email" => $input['customer_email'],
                    "customer_phone" => $input['customer_phone'],
                    "address"        => $input['address'],
                    "province_id"    => $input['province_id'],
                    "district_id"    => $input['district_id'],
                    "ward_id"        => $input['ward_id'],
                    "note"           => Arr::get($input, 'note'),
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
                        $data[$index]['product_variant_id'] = null;
                        $data[$index]['product_variant_code'] = null;
                        $data[$index]['product_variant_name'] = null;
                        $data[$index]['option_name'] = null;
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
                    $product->decrement('qty', $detail['qty']);
                    $total += $totalDetail;
                }

                OrderDetail::insert($data);
                $order->total = $total;
                $order->save();
            });

            if ($order) {
                return new OrderResource($order);
            } else {
                // Xử lý trường hợp không tạo được đơn hàng hoặc lỗi xảy ra
                return response()->json(['error' => 'Dữ liệu không chính xác'], 500);
            }
        } catch (\Exception $e) {
            GAK_ERROR::handle($e, 'orders');
        }
    }

    /**
     * Updates resource in the database
     * @param Order|Model $order
     * @param array $data
     * @return OrderResource
     */
    public function update(Order $order, array $data)
    {
//        try {
//            DB::beginTransaction();
//            $data = $this->clean($data);
//
//            $order->name = Arr::get($data, 'name', $order->name);
//            $order->description = Arr::get($data, 'description', $order->description);
//            $order->price = Arr::get($data, 'price', $order->price);
//            $order->category_id = Arr::get($data, 'category_id', $order->category_id);
//            $order->qty = Arr::get($data, 'qty', $order->qty);
//            $order->is_active = filter_var(Arr::get($data, 'is_active', $order->is_active), FILTER_VALIDATE_BOOLEAN);
//            $order->priority = Arr::get($data, 'priority', $order->priority);
//            if (!empty($data['file'])) {
//                $order->image = HasImage::updateImage($data['file'], $order->image, Order::path);
//            }
//
//            $order->save();
//            $detailCurrents = Arr::get($data, 'detail_currents', []);
//            $valueIds = array_column($detailCurrents, 'id');
//            OrderDetail::where('Order_id', $order->id)
//                ->whereNotIn('id', $valueIds)
//                ->where('status', 'PENDING')
//                ->delete();
//
//            $keyToRemove = 'title';
//            $detailCurrents = $this->removeKey($detailCurrents, $keyToRemove);
//            OrderDetail::upsert($detailCurrents, ['id'], ['description']);
//
//            $details = Arr::get($data, 'details', []);
//            $details = array_unique($details);
//            $params = [];
//
//            $values = array_column($detailCurrents, 'description');
//            foreach ($details as $detail) {
//                if (!empty($detail) && !in_array($detail, $values)) {
//                    $params[] = [
//                        'description' => $detail,
//                        'Order_id'    => $order->id,
//                        'is_active'   => true,
//                        'status'      => 'PENDING',
//                    ];
//                }
//            }
//            $params = $this->deleteNull($params);
//            if (!empty($params)) {
//                $order->details()->createMany($params);
//            }
//
//            $order->load(['details']);
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            throw new \Exception($e->getMessage());
//        }
//
//        return new OrderResource($order);
    }


    public function updateStatus(Order $order, $status)
    {
        if ($order->status !== $status && ($order->status !== Order::STATUS_COMPLETED || $order->status === Order::STATUS_CANCELLED)) {
            if ($status == Order::STATUS_CANCELLED) {
                foreach ($order->details as $detail) {
                    if ($detail->product) {
                        $detail->product->increment('qty', $detail->qty);
                        $detail->product->save();
                    }
                }
            }
            $order->status = $status;
        }
        return $order->save();
    }

    /**
     * Deletes resource in the database
     * @param Order|Model $order
     * @return bool
     */
    public function delete(Order $order)
    {
//        HasImage::deleteImage($order->image);
        OrderDetail::where('order_id', $order->id)->delete();
        return $order->delete();
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
     * Clean the data
     * @param array $data
     * @return array
     */
    private function deleteNull(array $data)
    {
        foreach ($data as $i => $row) {
            if (is_null($row)) {
                unset($data[$i]);
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

    // Hàm callback để xoá key trong mảng
    function removeKey($array, $keyToRemove)
    {
        return array_map(function ($item) use ($keyToRemove) {
            return array_filter($item, function ($key) use ($keyToRemove) {
                return $key !== $keyToRemove;
            }, ARRAY_FILTER_USE_KEY);
        }, $array);
    }
}
