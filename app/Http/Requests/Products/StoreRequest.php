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
            'name'             => 'required|string|max:255|unique:products,name',
            'image'            => 'required|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'thumb_image'      => 'nullable|array',
            'thumb_image.*'    => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description'      => 'required|string',
            'category_id'      => 'required|exists:categories,id',
            //            'is_active'   => 'required|in:1,0,true,false',
            'price'            => 'required|numeric|min:0|max:999999999999',
            'priority'         => 'required|numeric|min:0|max:1000',
            'meta_description' => 'required|max:255',
            'meta_title'       => 'required|max:255',
            'slug'             => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:products,slug',
            ],
            'meta_key'         => 'required|max:255',
            'video_link'       => 'nullable|url|max:350',
        ];
    }
}
