<?php

namespace App\Http\Requests\Supports;

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
            'name'      => [
                'required',
                'string',
                'max:255',
                Rule::unique('supports', 'name')->ignore($this->route('support')->id)
            ],
            'file'        => 'nullable|image|max:3145728|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm',
            'description' => 'nullable|max:255',
            'phone'       => 'required|max:255',
            'zalo'        => 'nullable|max:255',
            'telegram'    => 'nullable|max:255',
        ];
    }

}
