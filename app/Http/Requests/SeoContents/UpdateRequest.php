<?php

namespace App\Http\Requests\SeoContents;

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
            'link'        => [
                'required',
                'active_url',
                'max:355',
                Rule::unique('seo_contents', 'link')->ignore($this->route('seo_content')->id)
            ],
            'description' => 'required|string',
            'is_active'   => 'required|in:1,0,true,false',
        ];
    }

}
