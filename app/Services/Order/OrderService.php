<?php

namespace App\Services\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Services\Media\MediaService;
use App\Supports\HasImage;
use App\Supports\Support;
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
    public function create(array $data)
    {
//        try {
//            DB::beginTransaction();
//            $data = $this->clean($data);
//
//            $data['code'] = Support::genCode('orders', 'code');
//
//            $product = Product::find($data['product_id']);
//            $data['product_name'] = $product->name;
//            $data['customer_id'] = request()->user()->id;
//            $data['customer_name'] = request()->user()->name;
//            $data['customer_phone'] = request()->user()->phone;
//            $data['price'] = $product->price;
//            $data['total'] = $product->price * $data['qty'];
//            $data['is_active'] = 1;
//            $data['status'] = 'APPROVED';
//
//
//            $full_columns = $this->model->getFillable();
//            $data = array_intersect_key($data, array_flip($full_columns));
//
//
//            $record = Order::query()->create($data);
//            $params = [];
//            $productDetailIds = [];
//            $details = ProductDetail::inRandomOrder()
//                ->where('product_id', $product->id)
//                ->limit($data['qty'])
//                ->get();
//
//            foreach ($details as $detail) {
//                $params[] = [
//                    'order_id'          => $record->id,
//                    'product_detail_id' => $detail->id,
//                    'product_id'       => $product->id,
//                ];
//                $productDetailIds[] = $detail->id;
//            }
//
//            ProductDetail::query()->whereId('id', $productDetailIds)
//            ->update(['customer_id' => request()->user()->id]);
//
//            if (!empty($details)) {
//                $record->details()->createMany($params);
//            }
//            $product->qty -= $data['qty'];
//            $product->save();
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            throw new \Exception($e->getMessage());
//        }
//
//        return new OrderResource($record);
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

    /**
     * Deletes resource in the database
     * @param Order|Model $order
     * @return bool
     */
    public function delete(Order $order)
    {
//        HasImage::deleteImage($order->image);
//        return $order->delete();
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
