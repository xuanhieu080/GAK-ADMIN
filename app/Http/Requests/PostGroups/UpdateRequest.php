<?php

namespace App\Http\Requests\PostGroups;

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
            'name'             => [
                'required',
                'string',
                'max:255',
                Rule::unique('post_groups', 'name')->ignore($this->route('post_group')->id)
            ],
            'description'      => 'nullable|max:255',
            'image'            => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'meta_description' => 'required|max:255',
            'meta_title'       => 'required|max:255',
            'slug'             => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('post_groups', 'slug')->ignore($this->route('post_group')->id)
            ],
            'meta_key'         => 'required|max:255',
            'is_active'        => 'nullable|in:1,0,true,false',
        ];

        if (filter_var($this->input('remove_image'), FILTER_VALIDATE_BOOLEAN)) {
            $rules['image'] = 'required|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm';
        }

        return $rules;
    }

}
