<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name'      => 'required|string|max:255',
            'thumb_image'        => 'nullable|array',
            'thumb_image.*'      => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description'        => 'required|string',
            'reply'              => 'nullable|string',
            'product_variant_id' => [
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $this->route('product')->id)
            ],
            'rate'               => 'required|numeric|min:1|max:5',
            'date'               => 'required|date_format:Y-m-d',
        ];
    }
}
