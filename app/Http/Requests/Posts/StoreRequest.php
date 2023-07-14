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
            'title'     => 'required|string|max:255|unique:posts,title',
            'file'      => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'is_active' => 'required|in:1,0,true,false',
            'content'   => 'required|string',
        ];
    }
}
