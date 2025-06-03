<?php

namespace App\V1\Requests\Orders;

use App\Rules\ProductNotVariant;
use App\Rules\ProductVariantExists;
use App\Rules\ProductVariantQty;
use App\V1\Requests\ValidatorBase;

class CreateRequest extends ValidatorBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'items'                      => 'required|array',
            'items.*'                    => 'required',
            'items.*.product_id'         => [
                'required',
                'exists:products,id,is_active,1',
                new ProductNotVariant()
            ],
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.qty'                => [
                'required',
                'numeric',
                'min:1',
                'max:99999999',
                new ProductVariantQty()
            ],
            'customer_name'              => 'required|string|max:255',
            'customer_email'             => 'required|email|max:255',
            'customer_phone'             => 'required|string|max:20|min:8',
            'address'                    => 'required|string|max:255',
            'ward_id'                    => 'required|exists:wards,id',
            'district_id'                => 'required|exists:districts,id',
            'province_id'                => 'required|exists:provinces,id',
            'note'                       => 'nullable|string|max:255',
            'payment_method'             => 'required|in:COD,Zalopay,Momo,ShopeePay,VNPAY',
            'lang'                       => 'nullable|string',
        ];
    }
}
