<?php

namespace App\Http\Requests\Categories;

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
            'name'                => 'required|string|max:255|unique:categories,name',
            'name_en'             => 'nullable|string|max:255|unique:categories,name_en',
            'image'               => 'required|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'parent_id'           => 'nullable|exists:categories,id',
            'description'         => 'nullable|max:255',
            'description_en'      => 'nullable|max:255',
            'meta_description'    => 'required|max:400',
            'meta_description_en' => 'nullable|max:400',
            'meta_title'          => 'required|max:255',
            'meta_title_en'       => 'nullable|max:255',
            'content_seo'         => 'nullable',
            'content_seo_en'      => 'nullable',
            'slug'                => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:categories,slug',
            ],
            'slug_en'             => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:categories,slug_en',
            ],
            'order'               => 'nullable|numeric|min:0',
            'meta_key'            => 'required|max:255',
            'meta_key_en'         => 'nullable|max:255',
            'show_header'         => 'required|in:true,false',
            'show_dashboard'      => 'required|in:true,false',
        ];
    }
}
