<?php

namespace App\Http\Requests\Categories;

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
            'name'             => 'required|string|max:255|unique:categories,name',
            'image'            => 'required|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'parent_id'        => 'nullable|exists:categories,id',
            'description'      => 'nullable',
            'meta_description' => 'required|max:255',
            'meta_title'       => 'required|max:255',
            'slug'             => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:categories,slug',
            ],
            'meta_key'         => 'required|max:255',
            'show_header'      => 'required|in:true,false',
        ];
    }
}
