<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\BaseRequest;
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
        $rules = [
            'name'                => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->route('category')->id)
            ],
            'name_en'             => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'name_en')->ignore($this->route('category')->id)
            ],
            'description'         => 'nullable|max:255',
            'description_en'      => 'nullable|max:255',
            'parent_id'           => 'nullable|exists:categories,id',
            'image'               => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'meta_description'    => 'required|max:255',
            'meta_description_en' => 'nullable|max:400',
            'meta_title'          => 'required|max:400',
            'meta_title_en'       => 'nullable|max:255',
            'content_seo'         => 'nullable',
            'content_seo_en'      => 'nullable',
            'slug'                => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('categories', 'slug')->ignore($this->route('category')->id)
            ],
            'slug_en'             => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('categories', 'slug_en')->ignore($this->route('category')->id)
            ],
            'meta_key'            => 'required|max:255',
            'meta_key_en'         => 'nullable|max:255',
            'show_header'         => 'required|in:true,false',
            'show_dashboard'      => 'required|in:true,false',
            'order'               => 'nullable|numeric|min:0',
        ];

        if (filter_var($this->input('remove_image'), FILTER_VALIDATE_BOOLEAN)) {
            $rules['image'] = 'required|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm';
        }

        return $rules;
    }

}
