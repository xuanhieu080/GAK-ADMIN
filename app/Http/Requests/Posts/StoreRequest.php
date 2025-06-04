<?php

namespace App\Http\Requests\Posts;

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
            'title'               => 'required|string|max:255|unique:posts,title',
            'title_en'            => 'nullable|string|max:255|unique:posts,title_en',
            'image'               => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'is_active'           => 'required|in:1,0,true,false',
            'is_hot'              => 'required|in:1,0,true,false',
            'is_new'              => 'required|in:1,0,true,false',
            'view'                => 'required|numeric|min:0|max:99999999',
            'content'             => 'required|string',
            'content_en'          => 'nullable|string',
            'meta_description'    => 'required|max:400',
            'meta_description_en' => 'nullable|max:400',
            'meta_title'          => 'required|max:255',
            'meta_title_en'       => 'nullable|max:255',
            'group_id'            => 'nullable|exists:post_groups,id',
            'slug'                => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:posts,slug',
            ],
            'slug_en'             => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:posts,slug_en',
            ],
            'meta_key'            => 'nullable|max:255',
            'meta_key_en'         => 'nullable|max:255',
        ];
    }
}
