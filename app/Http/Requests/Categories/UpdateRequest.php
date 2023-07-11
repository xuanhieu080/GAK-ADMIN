<?php

namespace App\Http\Requests\Categories;

use App\Http\Requests\BaseRequest;

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
            'name'        => 'required|string|unique:categories,name,' . $this->request->get('name') . ',name|max:200',
            'file'        => 'nullable|image|max:3024|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description' => 'nullable|max:255',
        ];
    }

}
