<?php

namespace App\Http\Requests\Pages;

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
        $rules = [
            'name'        => 'required|string|max:255|unique:pages,name',
            'name_en'     => 'nullable|string|max:255|unique:pages,name_en',
            'is_active'   => 'required|in:1,0,true,false',
            'is_button'   => 'required|in:1,0,true,false',
            'show_header' => 'required|in:1,0,true,false',
            'group_id'    => 'nullable|exists:page_groups,id',
        ];

        if (filter_var($this->is_button, FILTER_VALIDATE_BOOLEAN)) {
            $rules['link'] = 'required|max:255';
            $rules['link_en'] = 'nullable|max:255';
        } else {
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:pages,slug',
            ];
            $rules['slug_en'] = [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:pages,slug',
            ];
            $rules['title'] = 'nullable|string|max:255';
            $rules['title_en'] = 'nullable|string|max:255';
            $rules['image'] = 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm';
            $rules['description'] = 'required|string';
            $rules['description_en'] = 'nullable|string';
            $rules['description_short'] = 'nullable|string|max:255';
            $rules['description_short_en'] = 'nullable|string|max:255';
            $rules['meta_description'] = 'nullable|max:400';
            $rules['meta_description_en'] = 'nullable|max:400';
            $rules['meta_title'] = 'required|max:255';
            $rules['meta_title_en'] = 'nullable|max:255';
            $rules['meta_key'] = 'nullable|max:255';
            $rules['meta_key_en'] = 'nullable|max:255';
        }

        return $rules;
    }
}
