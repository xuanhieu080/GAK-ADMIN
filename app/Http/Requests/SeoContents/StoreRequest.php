<?php

namespace App\Http\Requests\SeoContents;

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
            'is_active'   => 'required|in:1,0,true,false',
            'link'        => 'required|active_url|max:355|unique:seo_contents,link',
            'description' => 'required|string',
        ];
    }
}
