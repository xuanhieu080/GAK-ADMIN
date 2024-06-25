<?php

namespace App\Http\Requests\PageGroups;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

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
            'name'      => 'required|string|max:255|unique:page_groups,name',
            'column'    => 'required|numeric|min:1|max:10',
            'is_active' => 'nullable|in:true,false,0,1',
        ];
    }
}
