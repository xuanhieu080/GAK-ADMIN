<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;

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
            'name'                => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($this->route('product')->id)
            ],
            'name_en'             => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'name_en')->ignore($this->route('product')->id)
            ],
            'image'               => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'thumb_image'         => 'nullable|array',
            'thumb_image.*'       => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'thumb_image_remove'  => 'nullable|array',
            'description'         => 'required|string',
            'description_en'      => 'nullable|string',
            'category_id'         => 'required|exists:categories,id',
            'is_active'           => 'required|in:1,0,true,false',
            'price'               => 'nullable|numeric|min:0|max:999999999999',
            'price_en'               => 'nullable|numeric|min:0|max:999999999999',
            'qty'                 => 'nullable|numeric|min:0|max:10000000',
            'priority'            => 'required|numeric|min:0|max:1000',
            'meta_description'    => 'required|max:255',
            'meta_description_en' => 'nullable|max:255',
            'meta_title'          => 'required|max:255',
            'meta_title_en'       => 'nullable|max:255',
            'is_hot'              => 'nullable|in:true,false,1,0',
            'is_upcoming'         => 'nullable|in:true,false,1,0',
            'is_new'              => 'nullable|in:true,false,1,0',
            'is_uniform'          => 'nullable|in:true,false,1,0',
            'slug'                => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('products', 'slug')->ignore($this->route('product')->id)
            ],
            'slug_en'             => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('products', 'slug_en')->ignore($this->route('product')->id)
            ],
            'meta_key'            => 'required|max:255',
            'meta_key_en'         => 'nullable|max:255',
            'video_link'          => 'nullable|url|max:350',
            'discount'            => [
                'nullable',
                'max:1000000000000',
                'min:0',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value > $this->price) {
                        return $fail('Tiền giảm giá không được vượt quá giá tiền gốc');
                    }
                }
            ],
            'discount_en'            => [
                'nullable',
                'max:1000000000000',
                'min:0',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value > $this->price_en) {
                        return $fail('Tiền giảm giá không được vượt quá giá tiền gốc');
                    }
                }
            ],
        ];
    }

}
