<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name'              => 'required|string|max:255|unique:products,name,' . $this->request->get('name') . ',name',
            'file'              => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:categories,id',
            'is_active'         => 'required|in:1,0,true,false',
            'price'             => 'required|numeric|min:0|max:999999999999',
            'priority'          => 'required|numeric|min:0|max:1000',
            'detail_currents'   => 'nullable|array',
            'detail_currents.*' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (empty($value['id']) || empty($value['description'])) {
                        return $fail(__("messages.failed"));
                    }

                    $detail = ProductDetail::whereProductId($this->route('product')->id)
                        ->whereId($value['id'])
                        ->first();
                    if (empty($detail)) {
                        return $fail(__("messages.not_exist", ['name' => 'Giá trị']));
                    }
                    $item = ProductDetail::whereDescription($value['description'])
                                         ->where('product_id','<>',$this->route('product')->id)->first();
                    if (!empty($item) && $item->id != $value['id']) {
                        return $fail(__('messages.unique', ['name' => "$attribute: #" . $value['id']]));
                    }
                }
            ],
            'details'           => 'nullable|array',
            'details.*'         => [
                'required',
                'string',
                'unique:product_details,description'
            ],
        ];
    }

}
