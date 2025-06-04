<?php

namespace App\Http\Requests\PostGroups;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

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
            'name'                => 'required|string|max:255|unique:post_groups,name',
            'name_en'             => 'nullable|string|max:255|unique:post_groups,name_en',
            'image'               => 'required|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description'         => 'nullable',
            'description_en'      => 'nullable',
            'meta_description'    => 'required|max:400',
            'meta_description_en' => 'nullable|max:400',
            'meta_title'          => 'required|max:255',
            'meta_title_en'       => 'nullable|max:255',
            'slug'                => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:post_groups,slug',
            ],
            'slug_en'             => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:post_groups,slug_en',
            ],
            'meta_key'            => 'nullable|max:255',
            'meta_key_en'         => 'nullable|max:255',
            'is_active'           => 'nullable|in:1,0,true,false',
            'is_hot'              => 'required|in:1,0,true,false',
        ];
    }
}
