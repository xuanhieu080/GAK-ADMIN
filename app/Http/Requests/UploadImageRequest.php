<?php

namespace App\Http\Requests;

class UploadImageRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'image' => 'required|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
        ];
    }
}
