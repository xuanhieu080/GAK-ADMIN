<?php

namespace App\Http\Requests\AttributeGroups;

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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_groups', 'name')->ignore($this->route('attribute_group')->id)
            ]
        ];
    }

}
