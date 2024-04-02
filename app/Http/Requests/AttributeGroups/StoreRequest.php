<?php

namespace App\Http\Requests\AttributeGroups;

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
            'name'     => 'required|string|max:255|unique:attribute_groups,name',
            'priority' => 'required|numeric|min:0|max:1000',
        ];
    }
}
