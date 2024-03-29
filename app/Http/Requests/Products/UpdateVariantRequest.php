<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVariantRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'details'                      => 'array',
            'details.*'                    => 'array',
            'details.*.id'                 => [
                'required',
                Rule::exists('product_variants', 'id')->where('product_id', $this->route('product')->id)
            ],
            'details.*.name'               => [
                'required',
                'string',
                'max:255',
                //                Rule::unique('product_variants', 'name')->ignore($this->route('product')->id)
            ],
            'details.*.image'              => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'details.*.thumb_image'        => 'nullable|array',
            'details.*.thumb_image.*'      => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'details.*.thumb_image_remove' => 'nullable|array',
            'details.*.description'        => 'required|string',
            //            'category_id'        => 'required|exists:categories,id',
            'details.*.is_active'          => 'required|in:1,0,true,false',
            'details.*.price'              => 'nullable|numeric|min:0|max:999999999999',
            'details.*.qty'                => 'nullable|numeric|min:0|max:10000000',

            'details.*.discount'         => 'nullable|max:1000000000000|min:0|numeric',
            //            'priority'           => 'required|numeric|min:0|max:1000',
            'details.*.meta_description' => 'required|max:255',
            'details.*.meta_title'       => 'required|max:255',
            //            'slug'               => [
            //                'required',
            //                'string',
            //                'max:255',
            //                'regex:/^[a-z0-9-]+$/',
            //                Rule::unique('products', 'slug')->ignore($this->route('product')->id)
            //            ],
            'details.*.meta_key'         => 'required|max:255',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->details as $detail) {
                if ($detail['discount'] > $detail['price']) {
                    $validator->errors()->add('details.*.discount', 'Tiền giảm giá không được vượt quá giá tiền gốc');
                }
            }
        });
    }

}
