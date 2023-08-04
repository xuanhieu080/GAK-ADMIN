<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|string|max:255|unique:products,name',
            'file'        => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_active'   => 'required|in:1,0,true,false',
            'price'       => 'required|numeric|min:0|max:999999999999',
            'priority'    => 'required|numeric|min:0|max:1000',
            'details'     => 'nullable|array',
            'details.*'   => 'required|unique:product_details,description',
        ];
    }
}
