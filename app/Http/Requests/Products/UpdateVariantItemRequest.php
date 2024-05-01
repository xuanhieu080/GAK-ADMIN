<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVariantItemRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name'               => [
                'required',
                'string',
                'max:255',
                //                Rule::unique('product_variants', 'name')->ignore($this->route('product')->id)
            ],
            'is_active'          => 'nullable|in:1,0,true,false',
            'price'              => 'nullable|numeric|min:0|max:999999999999',
            'qty'                => 'nullable|numeric|min:0|max:10000000',
            'is_hot'           => 'nullable|in:true,false,1,0',
            'params'           => 'required|string|max:255',
        ];
    }

}
