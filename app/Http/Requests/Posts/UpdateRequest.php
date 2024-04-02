<?php

namespace App\Http\Requests\Posts;

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
            'title'            => 'required|string|unique:posts,title,' . $this->request->get('title') . ',title|max:255',
            'image'            => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'is_active'        => 'required|in:1,0,true,false',
            'is_hot'           => 'required|in:1,0,true,false',
            'is_new'           => 'required|in:1,0,true,false',
            'view'             => 'required|numeric|min:0|max:99999999',
            'content'          => 'required|string',
            'meta_description' => 'required|max:255',
            'meta_title'       => 'required|max:255',
            'group_id'         => 'nullable|exists:post_groups,id',
            'slug'               => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('posts', 'slug')->ignore($this->route('post')->id)
            ],
            'meta_key'         => 'required|max:255',
        ];
    }

}
