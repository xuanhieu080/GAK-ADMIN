<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;

class UpdateHighlightRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'highlight_image'        => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'highlight_image_remove' => 'nullable|in:1,0,false,true',
            'highlight'              => 'nullable|string',
        ];
    }

}
