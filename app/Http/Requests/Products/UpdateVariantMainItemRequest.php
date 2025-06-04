<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVariantMainItemRequest extends BaseRequest
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
            'name_en'            => [
                'nullable',
                'string',
                'max:255',
                //                Rule::unique('product_variants', 'name')->ignore($this->route('product')->id)
            ],
            'image'              => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'thumb_image'        => 'nullable|array',
            'thumb_image.*'      => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'thumb_image_remove' => 'nullable|array',

            'is_active'           => 'nullable|in:1,0,true,false',
            'meta_description'    => 'required|max:400',
            'meta_description_en' => 'nullable|max:400',
            'meta_title'          => 'required|max:255',
            'meta_title_en'       => 'nullable|max:255',
            'meta_key'            => 'required|max:255',
            'meta_key_en'         => 'nullable|max:255',
            'is_hot'              => 'nullable|in:true,false,1,0',
            'params'              => 'required|string|max:255',
        ];
    }

}
