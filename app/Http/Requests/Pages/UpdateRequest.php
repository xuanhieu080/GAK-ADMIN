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
        return [
            'name'               => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'name')->ignore($this->route('page')->id)
            ],
            'title'             => 'nullable|string|max:255',
            'image'             => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'is_active'         => 'required|in:1,0,true,false',
            'show_header'       => 'required|in:1,0,true,false',
            'description'       => 'required|string',
            'description_short' => 'nullable|string|max:255',
            'meta_description'  => 'required|max:255',
            'meta_title'        => 'required|max:255',
            'group_id'          => 'nullable|exists:post_groups,id',
            'slug'               => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('pages', 'slug')->ignore($this->route('page')->id)
            ],
            'meta_key'         => 'required|max:255',
        ];
    }

}
