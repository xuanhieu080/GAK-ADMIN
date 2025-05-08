<?php

namespace App\Http\Requests\Pages;

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
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'name')->ignore($this->route('page')->id)
            ],
            'name_en'     => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'name_en')->ignore($this->route('page')->id)
            ],
            'is_active'   => 'required|in:1,0,true,false',
            'show_header' => 'required|in:1,0,true,false',
            'is_button'   => 'required|in:1,0,true,false',
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
                Rule::unique('pages', 'slug')->ignore($this->route('page')->id)
            ];
            $rules['slug_en'] = [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('pages', 'slug_en')->ignore($this->route('page')->id)
            ];
            $rules['title'] = 'nullable|string|max:255';
            $rules['title_en'] = 'nullable|string|max:255';
            $rules['image'] = 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm';
            $rules['description'] = 'required|string';
            $rules['description_en'] = 'nullable|string';
            $rules['description_short'] = 'nullable|string|max:255';
            $rules['description_short_en'] = 'nullable|string|max:255';
            $rules['meta_description'] = 'required|max:255';
            $rules['meta_description_en'] = 'nullable|max:255';
            $rules['meta_title'] = 'required|max:255';
            $rules['meta_title_en'] = 'nullable|max:255';
            $rules['meta_key'] = 'required|max:255';
            $rules['meta_key_en'] = 'nullable|max:255';
        }
        return $rules;
    }

}
